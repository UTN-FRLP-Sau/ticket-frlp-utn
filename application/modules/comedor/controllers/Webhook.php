<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webhook extends CI_Controller
{
    public function mercadopago()
    {
        require_once FCPATH . 'vendor/autoload.php';
        MercadoPago\SDK::setAccessToken('TU_ACCESS_TOKEN');

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (isset($data['type']) && $data['type'] === 'payment') {
            $payment_id = $data['data']['id'];
            $payment = MercadoPago\Payment::find_by_id($payment_id);

            if ($payment && $payment->status === 'approved') {
                $external_reference = $payment->external_reference;

                $CI =& get_instance();
                $CI->load->model('ticket_model');
                $compra_pendiente = $CI->ticket_model->getCompraPendiente($external_reference);

                if ($compra_pendiente && !$compra_pendiente->procesada) {
                    $seleccion = json_decode($compra_pendiente->datos, true);

                    // Reutilizá tu lógica de registro de compra aquí,
                    // pero NO verifiques saldo ni lo descuentes
                    $n_compras = 0;
                    $dias = $seleccion; // array con los datos de menú seleccionados
                    $id_usuario = $compra_pendiente->id_usuario;
                    $costoVianda = 0;
                    foreach ($dias as $compra) {
                        $costoVianda = $compra['precio']; // para usar debajo
                        $data_compra = [
                            'fecha' => date('Y-m-d', time()),
                            'hora' => date('H:i:s', time()),
                            'dia_comprado' => $compra['dia_comprado'],
                            'id_usuario' => $id_usuario,
                            'precio' => $compra['precio'],
                            'tipo' => $compra['tipo'],
                            'turno' => $compra['turno'],
                            'menu' => $compra['menu'],
                            'transaccion_id' => -$id_usuario
                        ];
                        $data_log = [
                            'fecha' => date('Y-m-d', time()),
                            'hora' => date('H:i:s', time()),
                            'dia_comprado' => $compra['dia_comprado'],
                            'id_usuario' => $id_usuario,
                            'precio' => $compra['precio'],
                            'tipo' => $compra['tipo'],
                            'turno' => $compra['turno'],
                            'menu' => $compra['menu'],
                            'transaccion_tipo' => 'Compra',
                            'transaccion_id' => -$id_usuario
                        ];

                        if ($CI->ticket_model->addCompra($data_compra)) {
                            $CI->ticket_model->addLogCompra($data_log);
                            $n_compras = $n_compras + 1;
                        }
                    }
                    if ($n_compras > 0) {
                        $transaction_compra = [
                            'fecha' => date('Y-m-d', time()),
                            'hora' => date('H:i:s', time()),
                            'id_usuario' => $id_usuario,
                            'transaccion' => 'Compra',
                            'monto' => -$costoVianda * $n_compras,
                            'saldo' => null // NO modificar saldo
                        ];
                        $id_insert = $CI->ticket_model->addTransaccion($transaction_compra);
                        $compras = $CI->ticket_model->getComprasByIDTransaccion(-$id_usuario);
                        foreach ($compras as $compra) {
                            $id_compra = $compra->id;
                            $CI->ticket_model->updateTransactionInCompraByID($id_compra, $id_insert);
                        }
                        $logcompras = $CI->ticket_model->getLogComprasByIDTransaccion(-$id_usuario);
                        foreach ($logcompras as $compra) {
                            $id_compra = $compra->id;
                            $CI->ticket_model->updateTransactionInLogCompraByID($id_compra, $id_insert);
                        }
                        $CI->ticket_model->setCompraPendienteProcesada($external_reference);
                    }
                }
            }
        }
        http_response_code(200);
    }
}