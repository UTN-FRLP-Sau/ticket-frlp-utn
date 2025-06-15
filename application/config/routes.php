<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'comedor/ticket';
$route['404_override'] = 'general/errores/error404';
$route['translate_uri_dashes'] = false;

$route['usuario'] = 'comedor/ticket/index';
$route['usuario/cambio-password'] = 'usuario/changePassword';
$route['usuario/comprar'] = 'comedor/ticket/compra';
$route['usuario/carga_virtual'] = 'usuario/botones_de_pago';
$route['usuario/carga_virtual/add'] = 'usuario/add_carga_virtual';
$route['usuario/comprar/success'] = 'comedor/ticket/compraSuccess';
$route['usuario/devolver_compra'] = 'comedor/ticket/devolverCompra';
$route['usuario/devolver/success'] = 'comedor/ticket/devolverCompraSuccess';
$route['usuario/ultimos-movimientos'] = 'usuario/ultimosMovimientos';
$route['usuario/ultimos-movimientos/(:num)'] = 'usuario/ultimosMovimientos';
$route['login'] = 'usuario/login';
$route['logout'] = 'usuario/login/logout';
$route['usuario/recovery'] = 'usuario/login/passwordRecoveryRequest';
$route['usuario/recovery/(:any)'] = 'usuario/login/newPasswordRequest';

$route['contacto'] = 'comedor/contacto/index';
$route['menu'] = 'comedor/menu/index';
$route['comentarios'] = 'comedor/comentarios/index';
$route['comedor/agregar_comentario'] = 'comedor/comentarios/agregar_comentario';
// $route['terminos'] = 'comedor/terminos/index';
// $route['aceptarTerminos'] = 'comedor/terminos/aceptarTerminos';
// $route['rechazarTerminos'] = 'comedor/terminos/rechazarTerminos';


$route['dbmigrate'] = 'admin/migrate/index';

$route['admin'] = 'admin/vendedor/index';
$route['admin/login'] = 'admin/login/index';
$route['admin/nuevo_usuario'] = 'admin/vendedor/createUser';
$route['admin/cargar_saldo'] = 'admin/vendedor/cargarSaldo';
$route['admin/cargar_saldo/succes'] = 'admin/vendedor/cargarSaldoSucces';
$route['admin/modificar_usuario/(:num)'] = 'admin/vendedor/updateUser/$1';
$route['admin/listados'] = 'admin/vendedor/descargarExcel';
$route['admin/informe'] = 'admin/vendedor/viewDescargarInformes';
$route['admin/informe/diario'] = 'admin/vendedor/descargarCierreCajaDiario';
$route['admin/informe/semana'] = 'admin/vendedor/descargarCierreCajaSemana';
$route['admin/informe/pedido'] = 'admin/vendedor/descargarResumenPedidosSemana';
$route['admin/historial'] = 'admin/vendedor/historialCargas';
$route['admin/menu'] = 'admin/vendedor/updateMenu';
$route['admin/ver_comentarios'] = 'admin/administrador/ver_comentarios';
// $route['admin/ver_historial_menu'] = 'admin/vendedor/ver_historial_menu';

$route['admin/crear_vendedor'] = 'admin/administrador/createVendedor';
$route['admin/csv_carga'] = 'admin/administrador/cargar_archivo_csv';
$route['admin/csv_confirmar_carga'] = 'admin/administrador/confirmarCargasCVS';
$route['admin/configuracion/periodos'] = 'admin/administrador/configuracion_general';
$route['admin/configuracion/precios'] = 'admin/administrador/configuracion_costos';
$route['admin/configuracion/feriados_list/(:num)'] = 'admin/administrador/feriados_list';
$route['admin/configuracion/feriados_list/(:num)/d/(:num)'] = 'admin/administrador/borrar_feriado';
$route['admin/conf/feriados/add/fecha'] = 'admin/administrador/add_feriado';
$route['admin/conf/feriados/add/csv'] = 'admin/administrador/add_csv_feriado';
$route['admin/compras/usuario/(:num)'] = 'admin/administrador/ver_compras_userid';
$route['admin/compras/usuario/(:num)/(:num)'] = 'admin/administrador/ver_compras_userid';
$route['admin/compras/devolver/(:num)/(:num)'] = 'admin/administrador/devolver_compra_by_id';
$route['admin/configuracion/links'] = 'admin/administrador/listar_link_pagos';
$route['admin/configuracion/links/add'] = 'admin/administrador/add_link_pago';
$route['admin/configuracion/links/rm'] = 'admin/administrador/remove_link_pago';
$route['admin/cargasvirtuales/list'] = 'admin/administrador/cargas_virtuales_all_list';
$route['admin/cargasvirtuales/list/(:any)'] = 'admin/administrador/cargas_virtuales_all_list/$1';
$route['admin/cargasvirtuales/list/(:any)/aprobar'] = 'admin/administrador/carga_virtual_ok/$1';
$route['admin/cargasvirtuales/list/(:any)/rechazar'] = 'admin/administrador/carga_virtual_remove/$1';

$route['admin/repartidor'] = 'admin/repartidor/buscar_compra_por_fecha_user';
$route['admin/repartidor/entregar'] = 'admin/repartidor/entregar_compra_by_id';
$route['admin/repartidor/historial/(:num)'] = 'admin/repartidor/historial_entregas_by_fecha';



$route['webhook/mercadopago'] = 'comedor/webhook/mercadopago';