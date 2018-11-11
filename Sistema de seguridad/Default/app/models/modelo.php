<?php
class Modelo extends PDO{
	 private $tipo_de_base='pgsql';
	 private $host='localhost';
	 private $nombre_de_base='Sistema_de_seguridad';
	 private $usuario='postgres';
	 private $clave='12345';

	 public function __construct() {
		 try{
			 parent::__construct($this->tipo_de_base.':host='.$this->host.'; dbname='.$this->nombre_de_base.'; port=5432',
			 $this->usuario,$this->clave);
		 }catch(PDOException $e){
			 echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
			 exit;
		 }
  	}
  	//para las busquedas simples
  	public function get($sql){
		$resultado=parent::query($sql);
		return $resultado->fetchAll();
	}

	/*
	 * @String $sql
	 * @array $param
	 * estructura del array: array([0]=>dato,[1]=>tipoDeDato);
	 * ejemplo dato: 1445
	 * ejemplo tipo de dato: PDO::PARAM_INT
	 *
	 * Valores de retorno:
	 * array con los datos solicitados
	 * FALSE en caso de error
	 *  */
	public function getCompuesto($sql,$param){
		//simpre debe existir una cantidad par de parametros
		if(count($param)%2!=0)
			return false;
		$st=parent::prepare($sql);
		$posParametro=0;
		$aux=1;
		for($i=0;$i<count($param)/2;$i++){
			$st->bindParam($aux++,$param[$posParametro],$param[++$posParametro]);
			$posParametro++;
		}

		$res=$st->execute();
		//var_dump($st->errorInfo());
		return $st->fetchAll();
	}
	/*
	 * Esta función es igual que la anterio pero su objetivo es insertar datos
	 * en la DB.
	 * NOTA: la funcion "getCompuesto" puede usarse para insertar en la DB pero
	 * para que el código sea más comprensible se dividieron en dos.
	 * */

	public function set($sql,$param){
		//simpre debe existir una cantidad par de parametros
		if(count($param)%2!=0)
			return false;

		$st=parent::prepare($sql);
		$posParametro=0;
		$aux=1;
		for($i=0;$i<count($param)/2;$i++){
			
			//LINEAS USADAS PARA "DEBUGGIN"
			$a=$aux;
			$b=$posParametro;
			$c=$b+1;
			echo "$i )  st->bindParam($a,$param[$b],$param[$c]);<br>";
			
			$st->bindParam($aux++,$param[$posParametro],$param[++$posParametro]);
			$posParametro++;
		}
		$res=$st->execute();
		//En caso de error usar "errorInfo"
		//var_dump($st->errorInfo());
		//$st->debugDumpParams();
		//exit;
		return $st->FetchAll();
	}

/*
*A partir de aquí las funciones realizan acciones específicas con la DB
*/
	public function login ($usu,$pass) {
			$param[0]=$usu;
			$param[1]=PDO::PARAM_STR;
			$param[2]=$pass;
			$param[3]=PDO::PARAM_STR;
					$sql="SELECT * from Login (?,?);";
					return	$this-> getCompuesto($sql,$param);
	}

	public function regUsu($usu,$pass,$pNom,$sNom,$pApe,$sApe,$ced,$permit,$sexo,$nacionalidad,$cargo,$departamento){
		$param[0]=$usu; $param[1]=PDO::PARAM_STR;
		$param[2]=$pass; $param[3]=PDO::PARAM_STR;
		$param[4]=$pNom; $param[5]=PDO::PARAM_STR;
		$param[6]=$sNom; $param[7]=PDO::PARAM_STR;
		$param[8]=$sApe; $param[9]=PDO::PARAM_STR;
		$param[10]=$pApe; $param[11]=PDO::PARAM_STR;
		$param[12]=$sexo; $param[13]=PDO::PARAM_STR;
		$param[14]=$nacionalidad;	$param[15]=PDO::PARAM_STR;;
		$param[16]=$cargo; $param[17]=PDO::PARAM_STR;
		$param[18]=$departamento; $param[19]=PDO::PARAM_STR;
		$param[20]=$ced; $param[21]=PDO::PARAM_INT;
		$param[22]=$permit; $param[23]=PDO::PARAM_INT;
				$sql="Select * from Registro(?,?,?,?,?,?,?,?,?,?,?,?) f(Resultado boolean, Nivel integer);";
				return $this-> set($sql,$param);
	}

//000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000


//Consultar un usuario para modificarlo

	public function cmodUsu($usu){
		$sql="SELECT usuarios.nombre, usuarios.contraseña, individuos.p_nombre, individuos.p_apellido, individuos.cedula
		FROM usuarios, individuos, personal
		WHERE individuos.cedula=personal.cedula AND personal.id_personal=usuarios.id_personal AND usuarios.nombre=?;";
		$param=array(
			$usu,PDO::PARAM_STR
		);
		return $this->getCompuesto($sql,$param);
	}
	/*public function cmodUsu($usu) {
		$param[0]=$usu; $param[1]=PDO::PARAM_STR;
			$sql="Select * from DatosUsuario(?)
			f(Nombre varchar, Contrasena varchar, Primer_Nombre varchar, Segundo_Nombre varchar, Primer_Apellido varchar,
			 Segundo_Apellido varchar, Cedula integer); ";
		return $this->getCompuesto($sql,$param);
	}*/

	//Listar por cantidades
		public function listUsu(){
			$sql="SELECT usuarios.nombre, usuarios.contraseña, individuos.p_nombre, individuos.p_apellido, individuos.cedula, usuarios.activo
			FROM usuarios, individuos, personal
			WHERE individuos.cedula=personal.cedula AND personal.id_personal=usuarios.id_personal;";
			return $this->get($sql);
		}
		/*public function listUsu($total,$indice)  {
			$param[0]=$total; $param[1]=PDO::PARAM_INT;
			$param[2]=$indice; $param[3]=PDO::PARAM_INT;
				$sql="Select * from UsuariosRegistrados(?,?) f(Nombre varchar, Contrasena varchar, Primer_Nombre varchar,
				Primer_Apellido varchar, Cedula integer);";
				return $this->getCompuesto($sql,$param);
		}*/
	//Modificar usuario en base al nombre
		public function modificarUsuario($usu,$cedd,$pass,$PN,$PA){
			$param[0]=$usu;  $param[1]=PDO::PARAM_STR;
			$param[2]=$cedd; $param[3]=PDO::PARAM_INT;
			$param[4]=$pass; $param[5]=PDO::PARAM_STR;
			$param[6]=$PN;   $param[7]=PDO::PARAM_STR;
			$param[8]=$PA;   $param[9]=PDO::PARAM_STR;
			$sql="Select * from ActualizarUsuario(?,?,?,?,?);";
			return $this-> set($sql,$param);
		}

	//Consultar auditoria
		public function conAudi() {
			$sql="Select * from ConsultarAuditoria() f(ID integer, Usuario varchar, Descripcion varchar, Fehca TimeStamp);";
			return $this-> get($sql);
		}

	//insertar datos en auditoria
		public function inAudi($usuario,$desc) {
			$param[0]=$usuario; $param[1]=PDO::PARAM_STR;
			$param[2]=$desc; $param[3]=PDO::PARAM_STR;
			$sql="select * from AlmacenarAuditoria(?,?);";
			return $this-> getCompuesto($sql,$param);
		}
	//eliminar un usuario
		public function eliminarUsuario($usuario){
			$sql="Select * from EliminarUsuario(?);";
			$param=array(
				$usuario, PDO::PARAM_STR
			);
			$this-> set($sql,$param);
		}
	//activar cuenta
		public function activarUsuario($usuario){
			$sql="update usuarios set activo=1 where nombre=?";
			$param=array(
				$usuario,PDO::PARAM_STR
			);
			$this-> set($sql,$param);
		}
//========================================================================================
//              FUNCIONES GENÉRICAS PARA USAR FUERA DEL FLUJO NORMAL DE EVENTOS
//========================================================================================

//Función genérica para registrar un Individuo
/*POSIBLES VALORES NULOS:
    SN
    SA
    ParroT
    LugT
*/
   public function IndividuoGenerico($Cedula,$PN,$PA,$SN,$SA,$Sexo,$Nacion,$Parro,$Lug,$ParroT,$LugT)
   {
   	$param[0]=$Cedula; $param[1]=PDO::PARAM_INT;
   	$param[2]=$PN;     $param[3]=PDO::PARAM_STR;
   	$param[4]=$PA;     $param[5]=PDO::PARAM_STR;
   	$param[6]=$SN;     $param[7]=PDO::PARAM_STR;
   	$param[8]=$SA;     $param[9]=PDO::PARAM_STR;
   	$param[10]=$Sexo;   $param[11]=PDO::PARAM_STR;
   	$param[12]=$Nacion; $param[13]=PDO::PARAM_STR;
   	$param[14]=$Parro;  $param[15]=PDO::PARAM_INT;
   	$param[16]=$Lug;    $param[17]=PDO::PARAM_STR;
   	$param[18]=$ParroT; $param[19]=PDO::PARAM_INT;
   	$param[20]=$LugT;   $param[21]=PDO::PARAM_STR;
   	$sql= "Select * from RegistrarIndividuo(?,?,?,?,?,?,?,?,?,?,?);";
    return $this->getCompuesto($sql,$param);
   }


//Funcion genérica para registrar una Ficha
/*
 POSIBLES VALORES NULOS:
  Telef
  Prof
  NumFich
*/
  public function FichaGenerico($Cedula,$Num_Delito,$DescDelito,$Telef,$EdC,$Profe,$FechaN)
   {
   	$param[0]=$Cedula;     $param[1]=PDO::PARAM_INT;
   	$param[2]=$Num_Delito; $param[3]=PDO::PARAM_INT;
   	$param[4]=$DescDelito; $param[5]=PDO::PARAM_STR;
   	$param[6]=$Telef;      $param[7]=PDO::PARAM_STR;
   	$param[8]=$EdC;        $param[9]=PDO::PARAM_STR;
   	$param[10]=$Profe;     $param[11]=PDO::PARAM_STR;
   	$param[12]=$FechaN;    $param[13]=PDO::PARAM_STR;

   	$sql= "Select * from RegistrarFicha(?,?,?,?,?,?);";
    return $this->getCompuesto($sql,$param);
   }


//Función genérica para registrar una Caucion
/*
  NO PUEDE POSEER VALORES NULOS
*/
  public function CaucionGenerico($Fich,$IMG,$Obv)
   {
   	$param[0]=$Fich;  $param[1]=PDO::PARAM_INT;
   	$param[2]=$IMG;   $param[3]=PDO::PARAM_STR;
   	$param[4]=$IMG;   $param[5]=PDO::PARAM_STR;

   	$sql= "Select * from RegistrarCaucion(?,?,?);";
    return $this->getCompuesto($sql,$param);
   }

//========================================================================================
//              FUNCIONES PRINCIPALES EN EL FLUJO NORMAL DE EVENTOS
//========================================================================================

//Función principal para registrar una Notificación
/*
  POSIBLES VALORES NULOS:
  IndividuoGenerico + FichaGenerico
*/

 public function notificacionMS($Cedula,$PN,$PA,$SN,$SA,$Sexo,$Nacion,$Parro,$Lug,$ParroT,$LugT,$Num_Delito,$DescDelito,$Telef,$EdC,$Profe,$FechaN)
   {
   	$param[0]=$Cedula;      $param[1]=PDO::PARAM_INT;
   	$param[2]=$PN;          $param[3]=PDO::PARAM_STR;
   	$param[4]=$PA;          $param[5]=PDO::PARAM_STR;
   	$param[6]=$SN;          $param[7]=PDO::PARAM_STR;
   	$param[8]=$SA;          $param[9]=PDO::PARAM_STR;
   	$param[10]=$Sexo;       $param[11]=PDO::PARAM_STR;
   	$param[12]=$Nacion;     $param[13]=PDO::PARAM_STR;
   	$param[14]=$Parro;      $param[15]=PDO::PARAM_INT;
   	$param[16]=$Lug;        $param[17]=PDO::PARAM_STR;
   	$param[18]=$ParroT;     $param[19]=PDO::PARAM_INT;
   	$param[20]=$LugT;       $param[21]=PDO::PARAM_STR;
   	$param[22]=$Num_Delito; $param[23]=PDO::PARAM_INT;
   	$param[24]=$DescDelito; $param[25]=PDO::PARAM_STR;
   	$param[26]=$Telef;      $param[27]=PDO::PARAM_STR;
   	$param[28]=$EdC;        $param[29]=PDO::PARAM_STR;
   	$param[30]=$Profe;      $param[31]=PDO::PARAM_STR;
   	$param[32]=$FechaN;     $param[33]=PDO::PARAM_STR;

   	$sql= "Select * from NotificacionMAIN(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
    return $this->set($sql,$param);
   }


//Función para registrar una Caucion
/*
 POSIBLES VALORES NULOS:
 Los mismos que NotificacionMS (Arriba)
*/

 public function caucionMS($Indoc,$Cedula,$PN,$PA,$SN,$SA,$Sexo,$Nacion,$Parro,$Lug,$ParroT,$LugT,$Num_Delito,$DescDelito,$Telef,$EdC,$Profe,$FechaN,$IMG,$Obv)
   {
   	$param[0]=$Indoc;       $param[1]=PDO::PARAM_INT;
   	$param[2]=$Cedula;      $param[3]=PDO::PARAM_INT;
   	$param[4]=$PN;          $param[5]=PDO::PARAM_STR;
   	$param[6]=$PA;          $param[7]=PDO::PARAM_STR;
   	$param[8]=$SN;          $param[9]=PDO::PARAM_STR;
   	$param[10]=$SA;         $param[11]=PDO::PARAM_STR;
   	$param[12]=$Sexo;       $param[13]=PDO::PARAM_STR;
   	$param[14]=$Nacion;     $param[15]=PDO::PARAM_STR;
   	$param[16]=$Parro;      $param[17]=PDO::PARAM_INT;
   	$param[18]=$Lug;        $param[19]=PDO::PARAM_STR;
   	$param[20]=$ParroT;     $param[21]=PDO::PARAM_INT;
   	$param[22]=$LugT;       $param[23]=PDO::PARAM_STR;
   	$param[24]=$Num_Delito; $param[25]=PDO::PARAM_INT;
   	$param[26]=$DescDelito; $param[27]=PDO::PARAM_STR;
   	$param[28]=$Telef;      $param[29]=PDO::PARAM_STR;
   	$param[30]=$EdC;        $param[31]=PDO::PARAM_STR;
   	$param[32]=$Profe;      $param[33]=PDO::PARAM_STR;
   	$param[34]=$FechaN;     $param[35]=PDO::PARAM_STR;
   	$param[36]=$IMG;        $param[37]=PDO::PARAM_STR;
   	$param[38]=$Obv;        $param[39]=PDO::PARAM_STR;

   	$sql= "Select * from CaucionMAIN(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
    return $this->set($sql,$param);
   }


//========================================================================================
//              BUSCADORES PARA USAR CON AJAX O ALGO POR EL ESTILO
//========================================================================================

  //Función de búsqueda de cauciones SIMPLE
  public function BQCaucionS($Texto)
   {
   	$param[0]=$Texto; $param[1]=PDO::PARAM_STR;
   	$sql= "Select * from BusquedaCaucionesSimple(?) f(dic_imagen varchar, Cedula integer, p_nombre varchar, p_apellido varchar, desc_delito varchar, Fecha_Creacion Date);";
    return $this->getCompuesto($sql,$param);
   }


  //Función para obtener los datos de un detenido mediante su cedula
  public function datosDetenido($Cedula)
   {
   	$param[0]=$Cedula; $param[1]=PDO::PARAM_INT;
   	$sql= "Select * from DatosDetenido(?) f(dic_imagen varchar, Cedula integer, Sexo char, Nacionalidad char, p_nombre varchar, p_apellido varchar, s_nombre varchar, s_apellido varchar, CantidadDelitos integer, Observaciones varchar, Delito text, Descripcion text, FechaC text, Fecha_Nacimiento Date, EstadoCivil varchar, Profesion varchar, Telefono varchar, EstadoCasa varchar, MunicipioCasa varchar, ParroquiaCasa varchar, LugarCasa varchar, EstadoT varchar, MunicipioT varchar, ParroquiaT varchar, LugarT varchar);";
    return $this->getCompuesto($sql,$param);
   }


	// Guarda fichas de tipo caución[1] o de tipo notificación[2]
public function guardarFicha($datos,$tipo){
//convertir todos los datos opcionales "vacios" en null
foreach ($datos as $key => $value) {
	$datos[$key]=(trim($value)=='')?null:$value;
}
//=====================================Cedula de indocumentado (ID de los indocumentados)===================================
	if($datos['cedula']==0){
		$aux=$this->minCedula();
		$aux--;
		$datos['cedula']=$aux;
	}
//=======================================Comprobar que el individuo es nuevo en el sistema==================================
	$sqlNuevo="SELECT cedula FROM view_individuos WHERE cedula=?";
	$paramNuevo=array($datos['cedula'],PDO::PARAM_INT);
	$nuevo=$this->getCompuesto($sqlNuevo,$paramNuevo);
	$nuevo=empty($nuevo);


//=======================================IDENTIFICADORES==============================================
		$id_direccion=$this->maxId_direcciones()+1;
		$id_ficha=$this->maxId_ficha()+1;
		$id_caucion=$this->maxId_caucion()+1;
//==========================================INSERTS===============================
		$sqlIndividuo="INSERT INTO individuos(cedula, p_nombre, p_apellido, s_nombre, s_apellido,
            sexo, nacionalidad)
    VALUES ( ?, ?, ?, ?, ?,
            ?, ?);";
		$sqlDireccion="INSERT INTO direcciones(
            id_direccion, id_parroquia, lugar,id_individuo)
    VALUES (?, ?, ?, ?);";
		$sqlFicha="INSERT INTO fichas(
            id_ficha, id_individuo, nro_telefono, edo_civil, profesion, fecha_creacion,
            id_delito, desc_delito, num_ficha, fecha_nacimiento)
    VALUES (?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?);";
		$sqlCauciones="INSERT INTO cauciones(
            id_caucion, id_ficha, dic_imagen)
    VALUES (?, ?, ?);";

//======================================PARAMETROS=====================================
		$paramIndividuos=array(
			$datos['cedula'],PDO::PARAM_INT,
			$datos['primer_nombre'],PDO::PARAM_STR,
			$datos['primer_apellido'],PDO::PARAM_STR,
			$datos['segundo_nombre'],PDO::PARAM_STR,
			$datos['segundo_apellido'],PDO::PARAM_STR,
			$datos['sexo'],PDO::PARAM_STR,
			$datos['nacionalidad'],PDO::PARAM_STR
		);

		$paramDireccionRes=array(
			$id_direccion,PDO::PARAM_INT,
			(integer)$datos['res_parroquia'],PDO::PARAM_INT,
			$datos['res_lugar'],PDO::PARAM_STR,
			$datos['cedula']
		);
		$datos['tra_parroquia']=trim($datos['tra_parroquia']);
		if(!empty($datos['tra_parroquia'])){
			$id_direccion++;
			$paramDireccionTra=array(
				$id_direccion,PDO::PARAM_INT,
				(integer)$datos['tra_parroquia'],PDO::PARAM_INT,
				$datos['tra_lugar'],PDO::PARAM_STR,
				$datos['cedula']
			);
		}

		$paramFichas=array(
			$id_ficha,PDO::PARAM_INT,
			$datos['cedula'],PDO::PARAM_INT,
			$datos['num_telefono'],PDO::PARAM_INT,
			$datos['estado_civil'],PDO::PARAM_STR,
			$datos['profesion'],PDO::PARAM_STR,
			date('d/m/Y'),PDO::PARAM_STR,
			$datos['tipo_delito'],PDO::PARAM_INT,
			$datos['descripcion'],PDO::PARAM_STR,
			'123',PDO::PARAM_INT,
			$datos['fecha'],PDO::PARAM_STR
		);
		$paramCaucion=array(
			$id_caucion,PDO::PARAM_INT,
			$id_ficha,PDO::PARAM_INT,
			$datos['dir_img'],PDO::PARAM_STR
		);

//-------------------------------------INSERTAR EN LA DB--------------------------------------------
		//si es nuevo guarda en la tabla individuos
		if($nuevo)
			$this->set($sqlIndividuo,$paramIndividuos);

		$this->set($sqlDireccion,$paramDireccionRes);
		$this->set($sqlFicha,$paramFichas);

		//si es una caución [1] guarda en la DB
		if($tipo==1)
			$this->set($sqlCauciones,$paramCaucion);

		if(isset($paramDireccionTra))
			$this->set($sqlDireccion,$paramDireccionTra);
	}


//26/06/2016+
//tabla es un entero 1..2
public function cargarDireccion($tabla,$id){
	switch ($tabla) {
		case 0:
				$sql="SELECT id_estado, nombre
				FROM view_estados;";
				return $this->get($sql);
			break;
		case 1:
				$sql="SELECT id_municipio, nombre
	  		FROM view_municipios WHERE id_estado=?;";
				$param=array(
					$id,PDO::PARAM_INT
				);
				return $this->getCompuesto($sql,$param);
			break;

		case 2:
			$sql="SELECT id_parroquia, nombre
			FROM view_parroquias WHERE id_municipio=?;";
			$param=array(
				$id,PDO::PARAM_INT
			);
			return $this->getCompuesto($sql,$param);
		break;

		default:
			return false;
			break;
	}
}
public function getNombreDir($tabla,$id){
	switch ($tabla) {
		case 0:
				$sql="SELECT nombre
				FROM view_estados WHERE id_estado=?;";
				$param=array(
					$id,PDO::PARAM_INT
				);
				$res= $this->getCompuesto($sql,$param);
				return $res[0][0];
			break;
		case 1:
				$sql="SELECT nombre
	  		FROM view_municipios WHERE id_municipio=?;";
				$param=array(
					$id,PDO::PARAM_INT
				);
				$res= $this->getCompuesto($sql,$param);
				return $res[0][0];
			break;

		case 2:
			$sql="SELECT nombre
			FROM view_parroquias WHERE id_parroquia=?;";
			$param=array(
				$id,PDO::PARAM_INT
			);
			$res= $this->getCompuesto($sql,$param);
			return $res[0][0];
		break;

		default:
			return false;
			break;
	}
}

//28/06/2016+

public function buscarDet($dato,$filtros){
	$sql="SELECT
  Ind.cedula,
  Ind.p_nombre,
  Ind.p_apellido,
  Ind.s_nombre,
  Ind.s_apellido,
  Fich.desc_delito,
  Deli.desc_delito,
  to_char(Fich.fecha_creacion,'DD/MM/YYYY') as fecha_creacion,
  Cauc.dic_imagen
FROM
  view_individuos as Ind,
  view_fichas as Fich,
  view_delitos as Deli,
  view_cauciones as Cauc
WHERE
  Fich.cedula = Ind.cedula AND
  Cauc.id_ficha = Fich.id_ficha AND
  Deli.id_delito = Fich.id_delito
  AND ((CAST(Ind.cedula as TEXT) LIKE '%' || ? || '%') OR (Ind.p_nombre LIKE '%' || upper(?) || '%') OR (Ind.p_apellido LIKE '%' ||  upper(?) || '%') OR (Ind.s_apellido LIKE '%' || upper(?) || '%' ) OR (Ind.s_nombre LIKE '%' || upper(?) || '%' ) OR (Fich.desc_delito LIKE '%' || upper(?) || '%'))";
	$sql.=$filtros.";";
	$param=array(
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR
	);
	return $this->getCompuesto($sql,$param);
}


//05/07/2016+
public function buscarNotif($dato,$filtro){

	$sql="SELECT
	Ind.cedula,
	Ind.p_nombre,
	Ind.p_apellido,
	Ind.s_nombre,
	Ind.s_apellido,
	Fich.desc_delito,
	Fich.fecha_creacion,
	Deli.desc_delito
FROM
	view_individuos as Ind,
	view_fichas as Fich,
	view_delitos as Deli
WHERE
	Fich.cedula = Ind.cedula AND
	Deli.id_delito = Fich.id_delito
	AND (Select COUNT(Cauc.ID_Ficha) from Cauciones AS Cauc WHERE Cauc.ID_Ficha = Fich.ID_Ficha) < 1
	AND ((CAST(Fich.cedula as TEXT) LIKE '%' || ? || '%') OR (Ind.p_nombre LIKE '%' || upper(?) || '%') OR (Ind.p_apellido LIKE '%' ||  upper(?) || '%') OR (Ind.s_apellido LIKE '%' || upper(?) || '%' ) OR (Ind.s_nombre LIKE '%' || upper(?) || '%' ) OR (Fich.desc_delito LIKE '%' || upper(?) || '%'))";
	$sql.=$filtro.";";
	$param=array(
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR,
		$dato,PDO::PARAM_STR
	);
	return $this->getCompuesto($sql,$param);
}

//07/07/2016
public function getId_parroquia($nombre){
	$sql="SELECT id_parroquia FROM view_parroquias WHERE nombre= ?;";
	$param=array(
		$nombre,PDO::PARAM_STR
	);
	return $this->getCompuesto($sql,$param);
}
public function cedulaRepetida($cedula){
	$sql="SELECT cedula FROM view_individuos WHERE cedula= ?;";
	$param=array(
		$cedula,PDO::PARAM_INT
	);
	return $this->getCompuesto($sql,$param);
}


//08/07/2016
public function busqSimple($dato,$tipo){
	$sql="SELECT
  Ind.cedula,
  Ind.p_nombre,
  Ind.p_apellido,
  Fich.desc_delito,
  Deli.desc_delito,
  to_char(Fich.fecha_creacion,'DD/MM/YYYY') as fecha_creacion,
  Cauc.dic_imagen
FROM
  view_individuos AS Ind,
  view_fichas AS Fich,
  view_cauciones AS Cauc,
  view_delitos AS Deli
WHERE
  Fich.id_individuo = Ind.cedula AND
  Cauc.id_ficha = Fich.id_ficha AND
	Deli.id_delito = Fich.id_delito ";
	if(strcmp(trim($dato),'')==0)
		$sql.=($tipo==1)?"AND CAST(cedula AS TEXT) LIKE '%' || ? || '%';":"AND lower(p_nombre) LIKE '%' || lower(?) || '%';";
	else
		$sql.=";";

	$param=array(
		$dato,PDO::PARAM_STR
	);
	return $this->getCompuesto($sql,$param);
}

public function getDelitos(){
	$sql="SELECT id_delito, desc_delito FROM view_delitos;";
	return $this->get($sql);
}

public function datosYaExistentes($cedula){
	$sql="SELECT p_nombre,s_nombre,p_apellido,s_apellido FROM view_individuos WHERE view_individuos.cedula= ?;";
	$param=array($cedula,PDO::PARAM_INT);
	$res= $this->getCompuesto($sql,$param);
	if(empty($res))
		return false;
	else
		return $res[0];
}

public function indocumentados(){
	$sql="Select * from ListaIndocumentados() f(dic_imagen varchar, desc_delito varchar, p_nombre varchar, p_apellido varchar, cedula integer, fecha_creacion date);";
	return $this->get($sql);
}


//cantidad de delitos en función del tiempo
public function getCantDelitosTiempo($año,$mes){
	$sql="select count(Fich.id_delito), Deli.desc_delito
	FROM view_fichas AS Fich RIGHT JOIN view_delitos AS Deli on Fich.id_delito=Deli.id_delito
	WHERE extract(year from fecha_creacion)=? AND extract(month from fecha_creacion )=?
	GROUP BY Deli.desc_delito;";
	$param=array(
		$año,PDO::PARAM_STR,
		$mes,PDO::PARAM_STR
	);
	return $this->getCompuesto($sql,$param);
}

}

?>
