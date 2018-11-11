<?php

 class ListarUsuarios extends Controlador
 {
   public function listar(){
     $data=parent::$modelo->listUsu();
     parent::$vista->listaUsuarios($data);
   }
 }



?>
