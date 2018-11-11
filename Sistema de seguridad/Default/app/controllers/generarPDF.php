<?php
require_once LIBS_PATH."html2pdf/html2pdf.class.php";
require_once LIBS_PATH."Validar.class.php";
class GenerarPDF extends Controlador
{
  public function procesarFoto($img,$nombre){
    $dir_subida = APP_PATH.'img/';
    $tipo=basename($img['imagen']['type']);
    $extencion=substr($tipo,strrchr($tipo,'/'));
    $nombre=$this->filtrarNombre($nombre);
    $fichero_subido = $dir_subida.$nombre.'.'.$extencion;
    move_uploaded_file($_FILES['imagen']['tmp_name'], $fichero_subido);
    return $fichero_subido;
  }
  public function filtrarNombre($nombre){
    $nombre=strtolower($nombre);
    $nombre=str_replace('ñ','n',$nombre);
    $nombre=str_replace('á','a',$nombre);
    $nombre=str_replace('é','e',$nombre);
    $nombre=str_replace('í','i',$nombre);
    $nombre=str_replace('ó','o',$nombre);
    $nombre=str_replace('ú','u',$nombre);
    return $nombre;
  }
  public function generarFicha($datos){
    $validar=new Validar();
    return $validar->evalFicha($datos);
  }
  public function crearPDFCaucion($datos){
    ob_start();
    include 'FormatosPDF/formatoPDF_caucion.php';
    $contenido=ob_get_clean();
    require_once LIBS_PATH.'html2pdf/html2pdf.class.php';

    $pdf=new HTML2PDF('P','A$','es','UTF-8');
    $pdf->writeHTML($contenido);
    // Ob_end_clean es necesario porque limpia el buffer.
    Ob_end_clean();
    $pdf->output(APP_PATH.'Fichas/ficha.pdf','F');
  }
  public function crearPDFNotificacion($datos){
    ob_start();
    include 'FormatosPDF/formatoPDF_notificacion.php';
    $contenido=ob_get_clean();
    require_once LIBS_PATH.'html2pdf/html2pdf.class.php';

    $pdf=new HTML2PDF('P','A$','es','UTF-8');
    $pdf->writeHTML($contenido);
    // Ob_end_clean es necesario porque limpia el buffer.
    Ob_end_clean();
    $pdf->output(APP_PATH.'Fichas/ficha.pdf','F');
  }
  // de codigo de dirección a nombres 
  public function codDirANombres(&$data){
    if(strcmp(trim($data['res_estado']),'')!=0){
      $data['res_estado']=self::$modelo->getNombreDir(0,$data['res_estado']);
      $data['res_municipio']=self::$modelo->getNombreDir(1,$data['res_municipio']);
      $data['res_parroquia']=self::$modelo->getNombreDir(2,$data['res_parroquia']);
    }
    if(strcmp(trim($data['tra_estado']),'')!=0){
      $data['tra_estado']=self::$modelo->getNombreDir(0,$data['tra_estado']);
      $data['tra_municipio']=self::$modelo->getNombreDir(1,$data['tra_municipio']);
      $data['tra_parroquia']=self::$modelo->getNombreDir(2,$data['tra_parroquia']);
    }
  }
  
  public function mostrarPDF($data){
    (strcmp($data['enviar'],'CAU')==0)?$this->crearPDFCaucion($data):$this->crearPDFNotificacion($data);
    self::$vista->crearPDF();
  }
  public function prepararDatos($datos,$tipo){//tipo 1 caucion 2 notificacion
    $doc=($daos['cedula']<0)?'IN':'DOC';
    $salida=array(
    'doc' => $doc,
	  'cedula' =>  $datos['cedula'],
	  'nacionalidad' =>  $datos['nacionalidad'],
	  'primer_nombre' =>  $datos['p_nombre'],
	  'segundo_nombre' =>  $datos['s_nombre'],
	  'primer_apellido' =>  $datos['p_apellido'],
	  'segundo_apellido' =>  $datos['s_apellido'],
	  'sexo' =>  $datos['sexo'],
	  'fecha' =>  $datos['fecha_nacimiento'],
	  'estado_civil' =>  $datos['estadocivil'],
	  'num_telefono' =>  $datos['telefono'],
	  'profesion' =>  $datos['profesion'],
	  'tipo_delito' =>  $datos['delito'],
	  'res_estado' =>  $datos['estadocasa'],
	  'res_municipio' =>  $datos['municipiocasa'],
	  'res_parroquia' =>  $datos['parroquiacasa'],
	  'res_lugar' =>  $datos['lugarcasa'],
	  'tra_estado' =>  $datos['estadot'],
	  'tra_municipio' =>  $datos['municipiot'],
	  'tra_parroquia' =>  $datos['parroquiat'],
	  'tra_lugar' =>  $datos['lugart'],
	  'descripcion' =>  $datos['descripcion'],
	  'dir_img' =>  $datos['dic_imagen']
    );
    if(isset($datos['fc']))
      $salida['fc']=$datos['fc'];
    $salida['enviar']=($tipo==1)?'CAU':'NOT';
    return $salida;
  }
  public function perfilesPDF($datos,$tipo){
      switch($tipo){
        case 'caucion':
          $datos=$this->prepararDatos($datos,1);
          var_dump($datos);
          $this->mostrarPDF($datos);
          break;
        case 'notificacion':
          $datos=$this->prepararDatos($datos,2);
          $this->mostrarPDF($datos);
          break;
      }
  }
  public function historialPDF($datos){
    ob_start();
    include 'FormatosPDF/formatoPDF_historial.php';
    $contenido=ob_get_clean();
    require_once LIBS_PATH.'html2pdf/html2pdf.class.php';

    $pdf=new HTML2PDF('P','A$','es','UTF-8');
    $pdf->writeHTML($contenido);
    // Ob_end_clean es necesario porque limpia el buffer.
    Ob_end_clean();
    $pdf->output(APP_PATH.'Fichas/historial.pdf','F');
    //mostrar el pdf
    self::$vista->historialPDF();
  }
}

?>
