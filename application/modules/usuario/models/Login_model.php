<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validateUser($documento, $password)
    {
        /*Usado en:
        index
        */
        $this->db->where('documento', $documento);
        $this->db->where('pass', $password);
        $query = $this->db->get('usuarios');
        if (!empty($query->row())) {
            return true;
        }
        return false;
    }

    public function getUserByDocumento($documento)
    {
        /*Usado en:
        index
        passwordRecoveryRequest
        */
        $this->db->select('*');
        $this->db->where('documento', $documento);
        $query = $this->db->get('usuarios');
        if (!empty($query)) {
            return $query->row();
        }
        return false;
    }

    public function getRecoveryByToken($token)
    {
        /*Usado en:
        passwordRecoveryRequest
        newPasswordRequest
        */
        $this->db->select('*');
        $this->db->where('token', $token);
        return $this->db->get('passrecovery')->row();
    }

    public function addLogPassrecovery($data)
    {
        /*Usado en:
        passwordRecoveryRequest
        */
        $this->db->insert('log_passrecovery', $data);
        $this->db->insert('passrecovery', $data);

        return true;
    }

    public function updatePasswordById($password, $iduser)
    {
        /*Usada en:
        newPasswordRequest
        */
        $data = [
            'pass' => md5($password)
        ];

        $this->db->where('id', $iduser);
        $this->db->update('usuarios', $data);
        return true;
    }

    public function deleteRecoverylogById($id)
    {
        /*Usada en:
        newPasswordRequest
        */
        $this->db->delete('passrecovery', ['id' => $id]);
        return true;
    }

    public function getMailConfirmacionByToken($token)
    {
        /*Usado en:
        confirmarCorreo
        */
        $this->db->select('*');
        $this->db->where('token', $token);
        return $this->db->get('mail_confirmacion')->row();
    }

    public function getMailConfirmacionPendiente($id_usuario, $mail_nuevo)
    {
        /*Usado en:
        perfil (usuario/Usuario)

        Dedupe por (id_usuario, mail_nuevo): el token es aleatorio (no
        determinístico), así que no se puede recalcular para chequear si ya
        existe una solicitud pendiente y evitar reenviar el correo en cada
        submit del formulario.
        */
        $this->db->select('*');
        $this->db->where('id_usuario', $id_usuario);
        $this->db->where('mail_nuevo', $mail_nuevo);
        return $this->db->get('mail_confirmacion')->row();
    }

    public function addMailConfirmacion($data)
    {
        /*Usado en:
        perfil (comedor/usuario)
        */
        $this->db->insert('mail_confirmacion', $data);
        return true;
    }

    public function deleteMailConfirmacionById($id)
    {
        /*Usada en:
        confirmarCorreo
        */
        $this->db->delete('mail_confirmacion', ['id' => $id]);
        return true;
    }

    public function mailEstaRegistrado($mail)
    {
        /*Usada en:
        confirmarCorreo

        Revalida al momento de confirmar que nadie haya tomado ese mail
        mientras la confirmación estaba pendiente (la validación is_unique
        de perfil() solo corre al momento de solicitar el cambio).
        */
        $this->db->where('mail', $mail);
        return $this->db->get('usuarios')->num_rows() > 0;
    }

    public function updateUserMail($id_usuario, $mail)
    {
        /*Usada en:
        confirmarCorreo
        */
        $this->db->where('id', $id_usuario);
        $this->db->update('usuarios', ['mail' => $mail]);
        return true;
    }
}