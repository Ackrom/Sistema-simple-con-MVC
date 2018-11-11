<?php
require_once APP_PATH."models/modelo.php";
require_once APP_PATH."views/vista.php";
require_once "ingreso.php";
require_once "registrarUsuario.php";
require_once "listarUsuarios.php";
require_once "modifCuentas.php";
require_once "z_auditoria.php";
require_once "generarPDF.php";
require_once "cargar_direcciones.php";
require_once "busqueda.php";

class Controlador{
  protected static $vista;
  protected static $modelo;
  function __construct(){
    $this->setVista();
    $this->setModelo();
  }
  public function getVista(){return self::$vista;}
  public function getModelo(){return self::$modelo;}
  public function setVista(){self::$vista=new Vista();}
  public function setModelo(){self::$modelo=new Modelo();}

  public function inicio($data){
    $audit=new Auditoria();
    $controlador=new Ingreso();
    $controlador->iniciar($data);
    $audit->insertar("Ingresó al sistema.");
  }
  public function menuNvl3(){
    self::$vista->menuNvl3();
  }

  public function regUsuario($data){
    $registro=new Registro();
    $registro->registrar($data);
  }
  public function error($error){
    self::$vista->error($error);
  }
  public function salir(){
    $audit=new Auditoria();
    $audit->insertar("Cerró sesion.");
    Session::eliminar('id');
    Session::eliminar('permisos');

    header("location: index.php?act=ingreso");
  }
  public function listaUsuarios(){
    $listar=new ListarUsuarios();
    $listar->listar();
  }
  public function menuNvl1(){
    self::$vista->menuNvl1();
  }
  public function menuNvl2(){
    self::$vista->menuNvl2();
  }
  public function modificar($data){
    if(!isset($_GET['usu'])){
      self::$vista->error("Usuario no encontrado");
      exit;
    }
    if(!$data){
      $modificar=new Modificar();
      $modificar->datosActuales($_GET['usu']);
    }else{
      $modificar=new Modificar();
      $modificar->modif($data);
    }


  }


  public function crearFichas($data,$img){
    $pdf=new GenerarPDF();
    $audit=new Auditoria();
    if(!$data){
      self::$vista->crearFichas();
      exit;
    }
    foreach($data as $llave=>$valor){
      $data[$llave]=strtoupper($valor);
    }
    $data['tra_parroquia']=(trim($data['tra_parroquia'])=='')?-1:$data['tra_parroquia'];
    if(strcmp($data['enviar'],'CAU')==0){
      $data['tipo']=($data['doc']=='IN')?1:0;
      $data['cedula']=($data['doc']=='IN')?-1:$data['cedula'];
      $data['dir_img']=$pdf->procesarFoto($img,$data['cedula'].$data['primer_nombre'].$data['primer_apellido']);
      //var_dump($data);
      self::$modelo->caucionMS($data['tipo'],$data['cedula'],$data['primer_nombre'],$data['primer_apellido'],$data['segundo_nombre'],$data['segundo_apellido'],$data['sexo'],$data['nacionalidad'],$data['res_parroquia'],$data['res_lugar'],$data['tra_parroquia'],$data['tra_lugar'],$data['tipo_delito'],$data['descripcion'],$data['num_telefono'],$data['estado_civil'],$data['profesion'],$data['fecha'],$data['dir_img'],$data['observacion']);
      $audit->insertar("Registró una caución para el individuo: {$data['primer_nombre']} {$data['primer_apellido']} ");

    }else{
      self::$modelo->notificacionMS($data['cedula'],$data['primer_nombre'],$data['primer_apellido'],$data['segundo_nombre'],$data['segundo_apellido'],$data['sexo'],$data['nacionalidad'],$data['res_parroquia'],$data['res_lugar'],$data['tra_parroquia'],$data['tra_lugar'],$data['tipo_delito'],$data['descripcion'],$data['num_telefono'],$data['estado_civil'],$data['profesion'],$data['fecha']);
      $audit->insertar("Registró una notificación del individuo: {$data['primer_nombre']} {$data['primer_apellido']}");
    }
    $pdf->codDirANombres($data);
    $pdf->mostrarPDF($data);
  }
  public function perfilPDF(){
    if(!$_GET['ci']){
      $this->error("No hay datos para mostrar el PDF.");
      exit;
    }
    $data=self::$modelo->datosDetenido($_GET['ci']);
    if(!$data){
      $this->error("No hay datos para mostrar el PDF.");
      exit;
    }
    $data[0]['delito']=$_GET['delito'];
    $data[0]['descripcion']=$_GET['desc'];
    $data[0]['fc']=$_GET['fc'];
    $pdf=new GenerarPDF();
    $pdf->perfilesPDF($data[0],'caucion');
  }

  public function buscar($tipo){
    self::$modelo->BQCaucionS('');
    self::$vista->buscar($tipo);
  }
  public function cargarBusquedaAJAX($data){
    $busq=new Busqueda();
    $data=$busq->busquedaCompuesta($data['busqueda'],$data['filtro'],1);
    echo $data;
  }
  public function cargarDireccionesAJAX($data){
    $dir=new Direcciones();
    $dir->cargarInfo($data);
  }
  public function estadisticas(){
    self::$vista->estadisticas();
  }
  public function cedulaRepetidaAJAX($cedula){
    $res=self::$modelo->cedulaRepetida($cedula['cedula']);
    if($res)
      echo 'r';
    else
      echo 'null';
  }
  //08/08/2016
  public function busquedaSimpleAJAX($datos){
    $busqueda=new Busqueda();
    echo $busqueda->busquedaSimple($datos['busqueda']);
  }
  public function cargarDelitosAJAX(){
    $res=self::$modelo->getDelitos();
    $salida='';
    foreach ($res as $lin) {
      $salida.="<option value='{$lin[0]}'>{$lin[1]}</option>";
    }
    echo $salida;
  }
  //09/07/2016
  public function datosYaexistentesAJAX($dato){
    //$resultado=self::$modelo->datosYaExistentes($dato['cedula']);
    $resultado=self::$modelo->datosDetenido($dato['cedula']);
    if($resultado && $resultado[0]['dic_imagen']!=null){
      $aux=explode('/',$resultado[0]['dic_imagen']);
      $resultado[0]['dic_imagen']="app/img/".array_pop($aux);
      echo json_encode($resultado[0]);
    }
    else
      echo 'null';
  }
  public function listadoIndocumentadosAJAX(){
    $busqueda=new Busqueda();
    echo $busqueda->listadoIndocumentados();
  }
  //19/07/2016
  public function cargarNotificacionesAJAX($data){
    $busqueda=new Busqueda();
    $data=$busqueda->busquedaCompuesta($data['busqueda'],$data['filtro'],2);
    echo $data;
  }
  public function perfil(){
    $datos=self::$modelo->datosDetenido($_GET['ci']);
    self::$vista->perfil($datos);
  }
  public function estadisticasMensualesAJAX($data){
    $resultado=self::$modelo->getCantDelitosTiempo($data['year'],$data['mes']);
    echo json_encode($resultado);
  }
  public function auditoria(){
    $datos=self::$modelo->conAudi();
    self::$vista->auditoria($datos);
  }
  public function desactivar($datos){
    $audit=new Auditoria();
    if(!isset($_GET['usu']))
      $this->error("ERROR: falta de datos=>nombre de usuario");
    if(!$datos)
      self::$vista->desactivar(1);
    else{
      if(strcmp($datos['pass'],Session::get('pass'))==0){
        self::$modelo->eliminarUsuario($_GET['usu']);
        self::$vista->desactivar(2);
      }else
        self::$vista->desactivar(3);
        $audit->insertar("Se desactivó la cuenta del usuario: {$_GET['usu']}");
    }
  }
  public function activar($datos){
    $audit=new Auditoria();
    if(!isset($_GET['usu']))
      $this->error("ERROR: falta de datos=>nombre de usuario");
    if(!$datos)
      self::$vista->activar(1);
    else{
      if(strcmp($datos['pass'],Session::get('pass'))==0){
        self::$modelo->activarUsuario($_GET['usu']);
        self::$vista->activar(2);
      }else{
        self::$vista->activar(3);
        $audit->insertar("Se activó la cuenta del usuario: {$_GET['usu']}");
      }
    }
  }
  public function respaldoDB($datos){
    if(!$datos){
      self::$vista->respaldoDB();
    }else{
      putenv("PGPASSWORD=12345");
      $stringCMD='"'.$datos["dir_pg"].'"'." -U postgres Sistema_de_seguridad > ".'"'.dirname(APP_PATH).'\BDD.sql"';
      $res=exec($stringCMD);
  		putenv("PGPASSWORD");
      self::$vista->respaldoDB(true);
    }

  }
  public function restaurar($datos){
    if(!$datos){
      self::$vista->restaurar();
    }else{
      putenv("PGPASSWORD=12345");
      $stringCMD='"'.$datos["dir_pg"].'"'." -U postgres Sistema_de_seguridad < ".'"'.$datos["dir_res"].'"';
      $res=exec($stringCMD);
  		putenv("PGPASSWORD");
      self::$vista->restaurar(true);
    }
  }
  public function perfilGeneral(){
    if(!isset($_GET["ci"])){
      $this->error("No se encontró un historial para la cedula introducida.");
    }else{
      $datos=self::$modelo->datosDetenido($_GET["ci"]);
      $pdf=new GenerarPDF();
      $pdf->historialPDF($datos);
    }
  }
}
?>
