<?php
class Modificar extends Controlador
{
  function datosActuales($usu){

    $datos=parent::$modelo->cmodUsu($usu);
    parent::$vista->modificar($datos);
  }
  public function modif($data){
    $result=parent::$modelo->modificarUsuario($data["nombreUsu"],$data["cedula"],$data["pass1"],$data["nombrePer"],$data["apellido"]);
    if(!empty($result)){
      parent::$vista->modifExitoso();
    }else {
      parent::$vista->error("No se pudo realizar la operación.");
    }
  }
}

?>
