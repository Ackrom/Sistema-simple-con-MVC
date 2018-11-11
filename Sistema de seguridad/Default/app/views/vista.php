<?php
class Vista{
  public function ingreso($error=false){
    require_once SITE_MEDIA_PATH.'ingreso.php';
  }
  public function error($error){
    require_once SITE_MEDIA_PATH.'error.php';
  }
  public function menuNvl3(){
    require_once SITE_MEDIA_PATH.'menuNvl3.php';
  }
  public function regUsuario(){
    require_once SITE_MEDIA_PATH.'regUsuario.php';
  }
  public function registroExitoso(){
    require_once SITE_MEDIA_PATH.'registroExitoso.php';
  }
  public function listaUsuarios($data){
    require_once SITE_MEDIA_PATH.'listaDeUsuarios.php';
  }
  public function menuNvl1(){
    require_once SITE_MEDIA_PATH.'menuNvl1.php';
  }
  public function modificar($data){
    require_once SITE_MEDIA_PATH.'modificar.php';
  }
  public function crearFichas(){
    require_once SITE_MEDIA_PATH.'fichas.php';
  }
  public function buscar($tipo){
    require_once SITE_MEDIA_PATH.'busqueda.php';
  }
  public function crearPDF(){
    require_once SITE_MEDIA_PATH.'generarPDF.php';
  }
  public function estadisticas(){
    require_once SITE_MEDIA_PATH.'estadisticas.php';
  }
  public function perfil($datos){
    require_once SITE_MEDIA_PATH.'perfil.php';
  }
  public function auditoria($data){
    require_once SITE_MEDIA_PATH.'auditoria.php';
  }
  public function desactivar($estado){
    require_once SITE_MEDIA_PATH.'desactivarCuenta.php';
  }
  public function menuNvl2(){
    require_once SITE_MEDIA_PATH.'menuNvl2.php';
  }
  public function activar($estado){
    require_once SITE_MEDIA_PATH.'activarCuenta.php';
  }
  public function respaldoDB($datos=false){
    require_once SITE_MEDIA_PATH.'respaldoDb.php';
  }
  public function restaurar($datos=false){
    require_once SITE_MEDIA_PATH.'restaurar.php';
  }
  public function historialPDF(){
    require_once SITE_MEDIA_PATH.'historialPDF.php';
  }
  public function modifExitoso(){
    require_once SITE_MEDIA_PATH.'modifExitoso.php';
  }
}
?>
