<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Carga de modelos
        $this->load->model('comedor/Webhook_model', 'webhook_model'); 
        $this->load->model('comedor/ticket_model'); 
    }

    private function log_preferencia($mensaje)
    {
        // ruta del archivo de log específica del webhook
        $ruta_log = APPPATH . 'logs/preferencia_' . date('Y-m-d') . '.log';
        $fecha = date('Y-m-d H:i:s');
        file_put_contents($ruta_log, "[$fecha] $mensaje\n", FILE_APPEND);
    }
    /**
     * Mapea un código de estado de detalle de Mercado Pago a un mensaje más amigable para el usuario.
     * Esta función solo delega la llamada al método correspondiente en el Webhook_model.
     * @param string $mp_code El código de estado de detalle de Mercado Pago.
     * @return string Un mensaje descriptivo para el usuario.
     */
    private function mapMercadoPagoStatusDetail($mp_code)
    {
        return $this->webhook_model->_mapMpStatusDetail($mp_code); 
    }

    /**
     * Valida la firma (HMAC SHA256) del webhook de Mercado Pago.
     *
     * @param string $input El cuerpo RAW de la solicitud POST.
     * @param array $data Los datos decodificados del JSON del webhook.
     * @param string $secret_key La clave secreta de tu webhook de Mercado Pago.
     * @return bool True si la firma es válida, false en caso contrario.
     */
    private function _validateWebhookSignature($input, $data, $secret_key)
    {
        $xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        $xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';

        if (empty($xSignature)) {
            $this->webhook_model->_logManual('ERROR: Header x-signature no encontrado.', 'webhook');
            return false;
        }

        $ts = null;
        $v1 = null;
        $parts = explode(',', $xSignature);
        foreach ($parts as $part) {
            $kv = explode('=', $part, 2);
            if (count($kv) == 2) {
                $key = trim($kv[0]);
                $value = trim($kv[1]);
                if ($key === 'ts') $ts = $value;
                if ($key === 'v1') $v1 = $value;
            }
        }

        if (!$ts || !$v1) {
            $this->webhook_model->_logManual('ERROR: ts o v1 no encontrados en x-signature.', 'webhook');
            return false;
        }

        $manifest = '';

        if (isset($data['data']['id']) && !empty($data['data']['id'])) {
            $dataID_for_manifest = $data['data']['id'];
            $manifest = "id:$dataID_for_manifest;";
            if (!empty($xRequestId)) {
                $manifest .= "request-id:$xRequestId;";
            }
            $manifest .= "ts:$ts;";
        } else {
            $normalized_json_body = json_encode($data); 

            if ($normalized_json_body === false) {
                 $this->webhook_model->_logManual('ERROR: Fallo al normalizar el JSON para la firma. JSON: ' . $input, 'webhook');
                 return false;
            }
            $manifest = "ts:$ts;" . $normalized_json_body;
        }

        $calculatedSignature = hash_hmac('sha256', $manifest, $secret_key);

        if (!hash_equals($calculatedSignature, $v1)) {
            $this->webhook_model->_logManual("ERROR: Validación HMAC fallida. Calculado: $calculatedSignature, recibido: $v1. Manifiesto usado: '$manifest'", 'webhook');
            return false;
        }

        $this->webhook_model->_logManual('Validación HMAC exitosa.', 'webhook');
        return true;
    }

    public function mercadopago()
    {
        $this->webhook_model->_logManual('Entré al webhook', 'webhook');

        require_once FCPATH . 'vendor/autoload.php';
        $this->config->load('ticket');

        $access_token = $this->config->item('MP_ACCESS_TOKEN');
        $secret_key = $this->config->item('MP_WEBHOOK_SECRET'); 
        MercadoPago\SDK::setAccessToken($access_token);

        $input = file_get_contents('php://input');
        $this->webhook_model->_logManual('Webhook recibido (RAW): ' . $input, 'webhook');

        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->webhook_model->_logManual('ERROR JSON DECODE: ' . json_last_error_msg() . '. RAW: ' . $input, 'webhook');
            http_response_code(400);
            return;
        }

        if (!is_array($data) || empty($data)) {
            $this->webhook_model->_logManual('ERROR JSON DATA: Datos vacíos o inválidos. RAW: ' . $input, 'webhook');
            http_response_code(400);
            return;
        }

        // Validar la firma del webhook
        if (!$this->_validateWebhookSignature($input, $data, $secret_key)) {
            http_response_code(401); // 401 Unauthorized
            return;
        }

        // Verificar si el tipo de evento es 'payment' y si contiene un ID de pago
        try {
            if ((isset($data['type']) && $data['type'] == 'payment' && isset($data['data']['id']))) {
                $payment_id = $data['data']['id'];
                $this->webhook_model->_logManual('Webhook tipo PAYO (payment). ID de pago: ' . $payment_id, 'webhook');

                $payment_info = $this->ticket_model->getMercadoPagoPayment($payment_id);

                if ($payment_info) {
                    $external_reference = $payment_info->external_reference;
                    $mp_status_from_mp = $payment_info->status;
                    $mp_status_detail = isset($payment_info->status_detail) ? $payment_info->status_detail : 'N/A';

                    $this->webhook_model->_logManual('Estado de pago de MP para ' . $payment_id . ': ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . '). External Reference: ' . $external_reference, 'webhook');

                    $compra_pendiente = $this->ticket_model->getCompraPendiente($external_reference);

                    if ($compra_pendiente) {
                        $this->ticket_model->updateCompraPendienteEstado($compra_pendiente->id, $mp_status_from_mp, $mp_status_detail);
                        $this->webhook_model->_logManual('Actualizado mp_estado de compra_pendiente ' . $compra_pendiente->id . ' a: ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . ').', 'webhook');

                        
                        $descripcion = $payment_info->additional_info->items[0]->description ?? '';
                        if (preg_match('/(\d+)$/', $descripcion, $matches)) {
                            $documento = $matches[1];
                        } else {
                            $documento = 'N/A';
}

                        switch ($mp_status_from_mp) {
                            case 'approved':
                                if ($this->webhook_model->procesarPagoAprobado($compra_pendiente, $payment_info)) {

                                    $this->log_preferencia('Usuario ID: ' . $compra_pendiente->id_usuario . ' ;DNI: ' . $documento . ' ;External Reference: ' . $compra_pendiente->external_reference . ' ;Monto: ' . $payment_info->transaction_amount . ' ;Pago aprobado procesado por Webhook');

                                    $this->webhook_model->_logManual('PAGO APROBADO: Compra ' . $compra_pendiente->id . ' procesada y marcada.', 'webhook');

                                } else {
                                    $this->webhook_model->_logManual('PAGO APROBADO: Fallo al procesar pago aprobado para compra ' . $compra_pendiente->id . '.', 'webhook');
                                }
                                break;

                            case 'rejected':
                            case 'cancelled':
                            case 'expired_by_date_cutoff':
                                $this->webhook_model->_logManual('PAGO RECHAZADO/CANCELADO: Notificación para compra pendiente ' . $compra_pendiente->id . ' con estado: ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . ').', 'webhook');
                                if ($this->webhook_model->procesarPagoRechazado($compra_pendiente, $payment_info)) {
                                    
                                    $this->log_preferencia('Usuario ID: ' . $compra_pendiente->id_usuario . ' ;DNI: ' . $documento . ' ;External Reference: ' . $compra_pendiente->external_reference . ' ;Monto: ' . $payment_info->transaction_amount . ' ;Pago rechazado procesado por Webhook');
                                    $this->webhook_model->_logManual('PAGO RECHAZADO: Compra ' . $compra_pendiente->id . ' procesada como rechazada.', 'webhook');
                                    
                                } else {
                                    $this->webhook_model->_logManual('PAGO RECHAZADO: Fallo al procesar pago rechazado para compra ' . $compra_pendiente->id . '.', 'webhook');
                                }
                                break;

                            case 'pending':
                            case 'in_process':
                                $this->webhook_model->_logManual('PAGO PENDIENTE/EN PROCESO: Notificación para compra pendiente ' . $compra_pendiente->id . '. Estado: ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . '). Se espera confirmación futura.', 'webhook');
                                $this->session->unset_userdata('external_reference');
                                $this->session->unset_userdata('error_compra'); 
                                break;

                            default:
                                $this->webhook_model->_logManual('ESTADO DESCONOCIDO/NO MANEJADO (para acciones): Notificación para compra pendiente ' . $compra_pendiente->id . ' con estado: ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . ').', 'webhook');
                                break;
                        }
                    } else {
                        $this->webhook_model->_logManual('ADVERTENCIA: Compra pendiente no encontrada para external_reference: ' . $external_reference . '. ID de pago: ' . $payment_id, 'webhook');
                    }
                } else {
                    $this->webhook_model->_logManual('ADVERTENCIA: No se pudo obtener la información de pago de MP para ID: ' . $payment_id, 'webhook');
                }
            } else {
                $this->webhook_model->_logManual("Webhook recibido con formato desconocido o tipo no 'payment' (o sin data.id): " . $input, 'webhook');
            }

            $this->webhook_model->_logManual('Proceso de webhook completado exitosamente.', 'webhook');
            http_response_code(200); 
            return;

        } catch (Exception $e) {
            $this->webhook_model->_logManual('EXCEPCIÓN EN EL WEBHOOK: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine() . '.', 'webhook');
            http_response_code(500);
            return;
        }
    }
}