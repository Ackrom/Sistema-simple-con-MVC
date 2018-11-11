<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>

<div class="container-fluid" style="margin-top:30px">
  <div class="row" >
    <div class="col-md-8 col-md-offset-2 well">
      <center><h2>Modificar usuario</h2></center>
      <form id="form" class="form form-horizontal" enctype="multipart/form-data" action="index.php?act=modificar&usu=<?php echo $data[0]["nombre"];?>" method="post">
        <!--
        <div class="form-group">
          <div class="col-md-3">
            <label for="nombre">Nuevo de usuario:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="text" name="nombreUsu" placeholder="Nombre de usuario..." required="required" value='<?php //echo $data[0]['nombre'] ?>' />
          </div>
        </div>
         -->
         <input type="text" name="nombreUsu" value="<?php echo $data[0]["nombre"];?>"  hidden="hidden">
         <input type="text" name="cedula" value="<?php echo $data[0]["cedula"];?>" hidden="hidden">

         <div class="form-group">
           <div class="col-md-3">
             <label for="nombre">Cambiar primer nombre:</label>
           </div>
           <div class="col-md-8">
             <input class="form-control" type="text" name="nombrePer" placeholder="Nombre ..." required="required" value='<?php echo $data[0]['p_nombre'] ?>'/>
           </div>
         </div>

         <div class="form-group">
           <div class="col-md-3">
             <label for="nombre">Cambiar primer apellido:</label>
           </div>
           <div class="col-md-8">
             <input class="form-control" type="text" name="apellido" placeholder="Apellido ..." required="required" value='<?php echo $data[0]['p_apellido'] ?>'/>
           </div>
         </div>
        <div class="form-group">
          <div class="col-md-3">
            <label for="apellido">Nueva contraseña:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="password" id="pass1" name="pass1" placeholder="Contraseña..." required="required" />
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


        <!--
        <div class="form-group">
          <div class="col-md-3">
            <label for="nombre">Cambiar cédula:</label>
          </div>
          <div class="col-md-8">
            <input class="form-control" type="number" name="cedula" placeholder="cédula..." required="required" value='<?php// echo $data[0]['cedula'] ?>'/>
          </div>
        </div>
         -->
          <div class="form-group">
            <div class="col-md-12">
              <div class="col-md-2">
                <a href="index.php?act=verCuentas" class="btn btn-block btn-danger">Atrás</a>
              </div>
              <div class="col-md-10">
                <button class="btn btn-primary btn-block" type="submit" name="enviar">Modificar</button>
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
          pass1:{required:true,minlength:8,maxlength:60},
          pass2:{required:true, equalTo:"#pass1"},
          nombrePer:{minlength:2,maxlength:20,soloLetras:true,required:true},
          apellido:{minlength:2,maxlength:20,soloLetras:true,required:true},

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
