<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>

<div class="container-fluid" style="margin-top:30px">
  <div class="row" >
    <div class="col-md-8 col-md-offset-2 well">
      <center><h2>Registro de usuarios</h2></center>
      <form class="form form-horizontal" enctype="multipart/form-data" action="index.php?act=registrar" method="post" id="form">
        
        <div class="form-group">
          <div class="col-md-3">
                <label for="sexo">Género:</label>
             </div>
                <div class="input-group">
                  <label class="radio-inline"><input type="radio" name="sexo" value="H" checked>Masculino</label>
                  <label class="radio-inline"><input type="radio" name="sexo" value="M">Femenino</label>
                </div>
              </div>
        
        <div class="form-group">
          <div class="col-md-3">
            <label for="nombre">Nombre de usuario:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="text" name="nombreUsu" placeholder="Nombre de usuario..." required="required" maxlength="30" />
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-3">
            <label for="apellido">Contraseña:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="password" name="pass1" placeholder="Contraseña..." required="required" id="pass1"/>
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-3">
            <label for="cedula">Repita la contraseña:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="password" name="pass2" placeholder="Repita contraseña..." required="required" />
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-3">
            <label for="nombre">Primer nombre:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="text" name="nombrePer" placeholder="Nombre..." required="required" />
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-3">
            <label for="nombre">Primer apellido:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="text" name="apellido" placeholder="Apellido..." required="required" />
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-3">
            <label for="nombre">Cédula:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="text" name="cedula" placeholder="Cédula..." required="required" maxlength="10"/>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-3">
            <label for="nombre">Cargo:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="text" name="cargo" placeholder="Cargo que ostenta el individuo..." required="required" maxlength="60"/>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-3">
            <label for="nombre">Departamento:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="text" name="dpto" placeholder="Departamento al cual pertenece el individuo..." required="required" maxlength="60"/>
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-3">
            <label for="cedula">Permisos disponibles:</label>
          </div>
          <div class="col-md-8">
                <div class="radio" >
                  <label id="radio1" data-toggle="tooltip" data-placement="top" title="Posee permisos para: 
                    Realizar Búsquedas.             
                    Creación de fichas de caución y notificación.
                    Generar reportes y ver estadisticas.">
                    <input type="radio" name="permisos" value="1" checked>
                    Usuario de nivel 1
                  </label>
                </div>
                <div class="radio" >
                  <label id="radio2" data-toggle="tooltip" data-placement="top" title="Posee permisos para: Realizar búsquedas. Ver estadísticas.">
                    <input type="radio" name="permisos" value="2">
                    Usuario de nivel 2
                  </label>
                </div>
                <div class="radio" >
                  <label id="radio3" data-toggle="tooltip" data-placement="top" title="Posee permisos para: 
                    Crear cuentas.
                    Desactivar cuentas.
                    Realizar auditorías.
                    Respaldar y Restaurar Datos.">
                    <input type="radio" name="permisos" value="3">
                    Administrador
                  </label>
                </div>
                <br>
                <script type="text/javascript">
                  $("#radio1").tooltip();
                  $("#radio2").tooltip();
                  $("#radio3").tooltip();
                </script>
                
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <div class="col-md-2">
                <a href="index.php?act=menuNvl3" class="btn btn-block btn-danger">Atrás</a>
              </div>
              <div class="col-md-10">
                <button class="btn btn-primary btn-block" type="submit" name="enviar">Registrar usuario.</button>
              </div>

            </div>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
jQuery.validator.addMethod("soloLetras", function(value, element) {
  return this.optional(element) || /^[a-z||ñáéíóúü]+$/i.test(value);
}, "Por favor, solo introduzca letras.");
jQuery.validator.addMethod("soloImagenes", function(value, element) {
  var extencionesPermitidas=new Array(".jpeg",".jpg",".png");
  var extencion=value.substring(value.lastIndexOf(".")).toLowerCase();
  for(var i=0;i<extencionesPermitidas.length;i++){
    if(extencion==extencionesPermitidas[i]){
      return true;
    }
  }
  return false;
}, "Formato no válido, solo se pueden introducir imagenes con extensión '.png', '.jpeg' y '.jpg'");
jQuery.validator.addMethod("cedulaRepetida", function(value, element) {
var resultado;
  $.ajax({
    method:"POST",
    async: false,
    data:{ajax:3,cedula:value},
  }).done(function(datos){

    if(datos=='r'){
      resultado=false;
    }else {
      resultado=true;
    }
  });
  return resultado;
}, "La cédula ya se encuentra registrada en la base de datos.");
jQuery.validator.addMethod("notNull", function(value, element) {
  return this.optional(element) || value.trim();
}, "Este campo es obligatorio.");
  $("#form").validate({
        rules:{
          nombreUsu:{maxlength:30,minlength:6},
          pass1:{required:true,minlength:8,maxlength:60},
          pass2:{required:true, equalTo:"#pass1"},
          nombrePer:{minlength:2,maxlength:20,soloLetras:true,required:true},
          apellido:{minlength:2,maxlength:20,soloLetras:true,required:true},
          cedula:{maxlength:10,minlength:6,digits:true},
          cargo:{maxlength:60,minlength:5},
          dpto:{maxlength:60,minlength:5}
        },
        messages:{
          pass2:{equalTo:"Las contraseñas no coinciden."}
        },
        highlight: function(element) {
            $(element).parent().parent().addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent().parent().removeClass('has-error');
            $(element).parent().parent().addClass('has-success has-feedback');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
  });

</script>
<?php
  require_once WEB_PATH."layout/footer.php";
?>
  </body>
</html>
