<?php
class Validar{
	private static $is_alfa = '/^(?:[^\W\d_]|([ ]))*$/mu';
	private static $is_alfaNum = '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]*$/mu';
	private static $is_numTel = '^\+?\d{1,3}?[- .]?\(?(?:\d{2,3})\)?[- .]?\d\d\d[- .]?\d\d\d\d$';
	public function email($email){
		if(!empty($email) && is_string($email))
			return filter_var($email, FILTER_VALIDATE_EMAIL);
		else
			return false;
	}
	public function numero($numero){
		if($numero)
			return filter_var($numero, FILTER_VALIDATE_INT);
		else
			return false;
	}
	public function nombre($nombre){
		if(!empty($nombre) && is_string($nombre))
			return (preg_match(self::$is_alfa,$nombre)==1)?true:false;
	}
	public function pass($pas){
		if(!empty($pas) && (is_string($pas) || is_numeric($pas)))
			return(preg_match(self::$is_alfaNum,$pas)==1)?true:false;
	}
	public function intervalo($num,$inicio,$final){
		if($num>=$inicio && $num<=$final)
			return true;
		else
			return false;
	}

	/* Como esta aplicación trabaja con formularios de tamaño variable es necesario
	 * establecer algunar "restricciones"
	 *
	 * @array $arreglo
	 * @int $tamGrupos
	 * @int $cantMaxGrupos
	 * */
	public function cantidadAdecuada($arreglo,$tamGrupos,$cantMaxGrupos){
		if(count($arreglo)%$tamGrupos!=0)
			return false;
		if(count($arreglo)/$tamGrupos>$cantMaxGrupos)
			return false;
		return true;
	}

	/*
	* Valida los datos de la ficha
	*/
	/*
	Datos presentes en el formulario (18/06/2016)

	Datos presentes en los dos formularios=+
	Solo caucion = 1
	Solo notificacion = 2

	1 doc
	+cedula
	+nacionalidad
	+primer_nombre
	+segundo_nombre
	+primer_apellido
	+segundo_apellido
	+sexo

	1 fecha
	+estado_civil
	num_telefono
	+profesion
	+tipo_delito

	+res_estado
	+res_municipio
	+res_parroquia
	+res_lugar

	+tra_estado
	+tra_municipio
	+tra_parroquia
	+tra_lugar
	+descripcion
	1 imagen

	+submit

	Max=23
	Min=21
	*/
	public function evalFicha($datos){
		if(count($datos)!=23 && count($datos)!=21 )
			return array(false,'La cantidad de campos no es la adecuada. Cantidad de campos:'.count($datos));
		//1 = caución; 2 = notificación
		$tipo= count($datos)==23?1:2;
		echo $tipo;
		switch ($tipo) {

//ficha de caución
			case 1:
					echo "sep";
					try {
						if(!$this->numero($datos['cedula']))
							return array(false,'La cedula no es un numero');
					} catch (Exception $e) {

					}




				break;
//ficha de notificación
			case '2':
				var_dump($datos);

				if(!$this->numero($datos['cedula']) || !$this->numero($datos['num_telefono']))
					return false;




				break;
			default:
				# code...
				break;
		}
	}
}
?>
