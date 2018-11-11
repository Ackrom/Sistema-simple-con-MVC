<?php
if (!isset($_SESSION)) { session_start(); }
class Session{
	public static function set($indice,$valor){
		$_SESSION[$indice]=$valor;
	}
	public static function get($indice){
		if(isset($_SESSION[$indice]))
			return $_SESSION[$indice];
		else
			return false;
	}
	public static function eliminar($indice){
		if(isset($_SESSION[$indice]))
			unset($_SESSION[$indice]);
	}
}
?>
