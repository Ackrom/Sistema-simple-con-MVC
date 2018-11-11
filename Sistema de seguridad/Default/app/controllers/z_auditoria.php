<?php

  class Auditoria extends Controlador{

    public function insertar($accion){
      if(Session::get('id'))
        parent::$modelo->inAudi(Session::get('id'),$accion);
    }
  }

?>
