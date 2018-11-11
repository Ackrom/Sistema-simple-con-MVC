<?php
/**
 *
 */
class Direcciones extends Controlador{
  public function cargarInfo($data){
    $resultado=parent::$modelo->cargarDireccion($data['tabla'],$data['id']);

    for ($i=0; $i < count($resultado); $i++) {
      echo "<option value='{$resultado[$i][0]}'>{$resultado[$i][1]}</option>";
    }
  }
}


?>
