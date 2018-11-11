<?php
require_once LIBS_PATH."Validar.class.php";

class Ingreso extends Controlador{

  public function iniciar($data=false){

  //si el usuario no ha introducido los campos del formulario
    if(!$data){
      parent::$vista->ingreso();
      exit;
    }
  //si introdujo los campos y lo envió

  //validaciones

  //validaciones de sintaxis
      if(!array_key_exists('nombre',$data) || !array_key_exists('pass',$data)){
        parent::$vista->ingreso("Nombre de usuario o contraseña no válido.");
        exit;
      }
      $val=new Validar();
      if(!$val->pass($data['nombre']) || !$val->pass($data['pass'])){
        parent::$vista->ingreso("El nombre solo puede contener letras y  la contraseña no puede contener caracteres especiales('/%&/#).");
        exit;
      }
  //validaciones de existencia en la DB
      $permisos=parent::$modelo->login($data['nombre'],$data['pass']);
      if($permisos[0][0]){
        Session::set('id',$data['nombre']);
        Session::set('permisos',$permisos[0][0]);
        Session::set('pass',$data['pass']);
        header("location: index.php?act=menuNvl".$permisos[0][0]);
      }else{
        parent::$vista->ingreso("Nombre de usuario o contraseña no coinciden.");
      }
  }

}
?>
