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




// Configuracion de parametros

//$config['apertura'] = '2022-04-04';
//$config['cierre'] = '2022-12-09';
//$config['vacaciones_i'] = '2022-07-17';
//$config['vacaciones_f'] = '2022-07-31';
//$config['dia_inicial'] = 1; //lunes
//$config['dia_final'] = 5; //viernes
//$config['hora_final'] = '12:00:00'; //hora del viernes de cierre de compra

//Set lenguage
setlocale(LC_ALL, "es_ES");