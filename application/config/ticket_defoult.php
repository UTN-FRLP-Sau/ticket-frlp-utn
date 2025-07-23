<?php
defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set('America/Argentina/Buenos_Aires');

$config['modules_locations'] = [
    APPPATH . 'modules/' => '../modules/',
];

//SMTP
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'smtp.gmail.com';
$config['smtp_user'] = 'ticketwbfrlp@gmail.com';
$config['smtp_pass'] = '';
$config['smtp_port'] = '587';
$config['smtp_crypto'] = 'tls';
$config['email_settings_sender'] = 'ticketweb@frlp.utn.edu.ar';
$config['email_settings_sender_name'] = 'TicketWeb';

// Permite la compra de viandas en los dos turnos en simulateneo en un mismo dia
$config['permitir_ambos_turnos_mismo_dia'] = FALSE;

// Mercado Pago Config
$config['MP_ACCESS_TOKEN'] = '';
$config['MP_PUBLIC_KEY'] = '';
$config['MP_WEBHOOK_SECRET'] = '';





//Set lenguage
setlocale(LC_ALL, "es_ES");