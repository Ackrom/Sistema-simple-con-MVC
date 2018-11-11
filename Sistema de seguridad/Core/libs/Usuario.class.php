<?php
/**
 * Objetivos:
 *1- analizar los permisos del usuario e informar al sistema de las acciones($acciones) que puede realizar.
 */
require_once "Session.class.php";
class Usuario{

  /*
    Lista de acciones:
    las acciones se guardan en la base de datos como un string de la forma: "01011010"
    en donde el primer índice[0] indica que el usuario NO tiene permitido realizar Busquedas
    por otra parte el segundo índice[1] indica que el usuario puede crear fichas de caución y notificación.

    1- Busquedas.
    2- Creación de fichas de caución y notificación.
    3- Generar estadisticas.
    4- Crear cuentas.
    5- Modificar cuentas.
    6- Eliminar cuentas.
    7- Visualizar las cuentas existentes.
    8- Realizar auditorias.
  */

public static function accionesDisponibles(){
  //las acciones se deben colocar en orden de las más basicas[usuario nvl1] a las más "complejas" [usuario nvl3].
  switch((integer)Session::get('permisos')){
    case 1:
      $acciones=array(
        'ingreso'=>true,
        'menuNvl1'=>true,
        'buscarDet'=>true,
        'buscarNot'=>true,
        'crearFichas'=>true,
        'reportes'=>true,
        'estadisticas'=>true,
        'salir'=>true,
        'perfil'=>true,
        'perfilPDF'=>true,
        'perfilGeneral'=>true
      );
      break;
    case 2:
      $acciones=array(
        'ingreso'=>true,
        'menuNvl2'=>true,
        'buscarDet'=>true,
        'buscarNot'=>true,
        'reportes'=>true,
        'estadisticas'=>true,
        'salir'=>true,
        'perfil'=>true,
        'perfilPDF'=>true,
        'perfilGeneral'=>true
      );
      break;
    case 3:
      $acciones=array(
        'ingreso'=>true,
        'menuNvl3'=>true,
        'verCuentas'=>true,
        'registrar'=>true,
        'auditorias'=>true,
        'salir'=>true,
        'modificar'=>true,
        'auditoria'=>true,
        'desactivar'=>true,
        'activar'=>true,
        'respaldo'=>true,
        'restaurar'=>true
      );
      break;
    default:
      $acciones=array(
        'ingreso'=>true
      );
      break;
  }
  return $acciones;
}

/*  Esta función es para el caso de que se necesite crear cuentas con permisos particulares
  public static function accionesDisponibles(){
    $permisos=Session::get('permisos');
    $acciones=array(
      'buscar'=>false,
      'crearFichas'=>false,
      'estadisticas'=>false,
      'crearC'=>false,
      'modificarC'=>false,
      'eliminarC'=>false,
      'visualizarC'=>false,
      'auditorias'=>false
    );
    reset($acciones);
    for($i=0;$i<strlen($permisos);$i++){
      if(strcmp($permisos[$i],"1")==0){
        $acciones[key($acciones)]=true;
      }
      next($acciones);
    }
    return $acciones;
  }
*/
}

 ?>
