<?php
  require_once LIBS_PATH."Validar.class.php";
  require_once "z_auditoria.php";
  /**
   * El objetivo de esta clase es recibir los datos de l usuario comprobar su validez y guardarlos en la base de datos.
   */
  class Registro extends Controlador{
    public function registrar($data){

      if(!$data){
        parent::$vista->regUsuario();
        exit;
      }

      $val=new Validar();
      $nom=trim($data['nombreUsu']);
      $pas=trim($data['pass1']);
      $pas2=trim($data['pass2']);
      $apell=trim($data['apellido']);
      $nomP=trim($data['nombrePer']);
      $ci=trim($data['cedula']);
      $permisos=trim($data['permisos']);
      $cargo=trim($data['cargo']);
      $dpto=trim($data['dpto']);
      $sexo=trim($data['sexo']);

      //cantidad de campos adecuado
      if(!is_array($data) || !$val->intervalo(count($data),5,11)){
        parent::$vista->error("Error en la cantidad campos.");
        exit;
      }

      //sintaxis
      if(!$val->pass($nom) || !$val->pass($pas)){
        parent::$vista->error("Nombre de usuario o contraseña inválidos.");
        exit;
      }
      if(!$val->nombre($apell) || !$val->nombre($nomP)){
        parent::$vista->error("Nombre o apellido inválido.");
        exit;
      }
      if(!$val->numero($ci)){
        parent::$vista->error("Cédula inválida.");
        exit;
      }
      if(!$val->numero($permisos)){
        parent::$vista->error("Permisos inválidos.");
        exit;
      }

      //longitud de los campos.
      if(!$val->intervalo(strlen($nom),6,30) || !$val->intervalo(strlen($pas),8,60) ){
        parent::$vista->error("El nombre debe contener entre 8 y 30 carácteres y la contraseña entre 8 y 60 carácteres.");
        exit;
      }
      if(strcmp($pas,$pas2)!=0){
        parent::$vista->error("Las contraseñas no coinciden");
        exit;
      }
      $audit=new Auditoria();
      $resultado=parent::$modelo->regUsu($nom,$pas,$nomP,null,$apell,null,$ci,$permisos,$sexo,'V',$cargo,$dpto);
      if(empty($resultado) || !$resultado[0][0]){
        if(parent::$modelo->cedulaRepetida($ci)){
          parent::$vista->error("Cedula repetida");
          $audit->insertar("Error al intentar registrar al usuario $nom. Tipo de error: CÉDULA REPETIDA");
        }else{
          parent::$vista->error("Usuario ya registrado");
          $audit->insertar("Error al intentar registrar al usuario $nom. Tipo de error: USUARIO YA REGISTRADO");
        }
        }else{
        parent::$vista->registroExitoso();
        $audit->insertar("Se registó al usuario: $nom con los permisos de usuario nivel $permisos");
      }
        
    }
  }

?>
