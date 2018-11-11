<?php
//---------------------------CONTROLADOR FRONTAL-------------------------------
//constantes y controlador
include_once dirname(__DIR__)."/app/constants/constantes.php";
include_once APP_PATH."controllers/controlador.php";
include_once LIBS_PATH."Session.class.php";
include_once LIBS_PATH."Usuario.class.php";
require_once APP_PATH."models/modelo.php";
/*$modelo=new Modelo();

$resultado=$modelo->buscarNotif("",'');
var_dump($resultado);*/
//require_once APP_PATH.'controllers/FormatosPDF/formatoPDF_caucion.php';
//Session::eliminar("id");
//acciones disponibles
    //si el usuario ya ingresó
if(Session::get('id')){
  $acciones=Usuario::accionesDisponibles();
}else{
  $acciones=array(
    'ingreso'=>true
  );
}



//evaluar url

if(isset($_GET['act'])){
    if(isset($acciones[$_GET['act']])){
        $act=$_GET['act'];
    }else {
        header('Status: 404 Not Found');
        echo "<h1>Error 404: no se ha podido encontrar la ruta \"{$_GET['act']}\"</h1>";
        exit;
    }
}else{
    $act='ingreso';
}


//datos introducidos por el usuario
$userData=$_POST;
$archivos=$_FILES;


//ejecutar accion
$controlador=new Controlador();

//para consultas AJAX

// WARNING!!!! es importante usar exit en vez de break
if(isset($userData['ajax'])){
  switch ($userData['ajax']) {
    case 1:
      $controlador->cargarDireccionesAJAX($userData);
      exit;
    case 2:
      $controlador->cargarBusquedaAJAX($userData);
      exit;
    case 3:
      $controlador->cedulaRepetidaAJAX($userData);
      exit;
    case 4:
      $controlador->busquedaSimpleAJAX($userData);
      exit;
    case 5:
      $controlador->cargarDelitosAJAX();
      exit;
    case 6:
      $controlador->datosYaexistentesAJAX($userData);
      exit;
    case 7:
      $controlador->listadoIndocumentadosAJAX();
      exit;
    case 8:
      $controlador->cargarNotificacionesAJAX($userData);
      exit;
    case 9:
      $controlador->estadisticasMensualesAJAX($userData);
      exit;
  }

}
  switch ($act) {
    case 'buscarDet':
      $controlador->buscar(1);
      break;
    case 'ingreso':
      $controlador->inicio($userData);
      break;
    case 'salir':
      $controlador->salir();
      break;
    case 'menuNvl3':
      $controlador->menuNvl3();
      break;
    case 'menuNvl1':
      $controlador->menuNvl1();
      break;
    case 'menuNvl2':
      $controlador->menuNvl2();
      break;
    case 'registrar':
      $controlador->regUsuario($userData);
      break;
    case 'verCuentas':
      $controlador->listaUsuarios();
      break;
    case 'modificar':
      $controlador->modificar($userData);
      break;
    case 'crearFichas':
      $controlador->crearFichas($userData,$archivos);
      break;
    case 'estadisticas':
      $controlador->estadisticas();
      break;
    case 'buscarNot':
      $controlador->buscar(2);
      break;
    case 'perfil':
      $controlador->perfil();
      break;
    case 'perfilPDF':
      $controlador->perfilPDF();
      break;
    case 'auditoria':
      $controlador->auditoria();
      break;
    case 'desactivar':
      $controlador->desactivar($userData);
      break;
    case 'activar':
      $controlador->activar($userData);
      break;
    case 'respaldo':
      $controlador->respaldoDB($userData);
      break;
    case 'restaurar':
      $controlador->restaurar($userData);
      break;
    case 'perfilGeneral':
      $controlador->perfilGeneral();
      break;
  }
?>
