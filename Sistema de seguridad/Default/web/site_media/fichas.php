<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>


<body>
<?php

//valida el tipo de ficha que se solicita Notificación o caución
  //si es usuario nivel 1 el string quedaría = "menuNvl1"
  $dir="menuNvl".Session::get('permisos');
  if (!isset($_GET['tipo'])) {
    header("location: index.php?act=$dir");
    exit;
  }
  if(strcmp($_GET['tipo'],'cau')!=0 && strcmp($_GET['tipo'],'notif')!=0){
    header("location: index.php?act=$dir");
    exit;
  }
  $tipo=strcmp($_GET['tipo'],'cau')==0?true:false;
  //echo "<script type='text/javascript' src='{$ver}dist/js/direcciones.js'></script>";
 ?>




 <script type="text/javascript">

 //AJAX que carga las direcciones
 var cargaDir=function(tab,dir_id,html_id){
   $.ajax({
     method:"POST",
     data:{ajax:1,tabla:tab,id:dir_id},
   }).done(function(datos){
     $("#"+html_id).html("<option value=' '>--SELECCIONAR--</option>"+datos);
   });
 }
var cargarDelitos=function(html_id){
  $.ajax({
    method:"POST",
    data:{ajax:5},
  }).done(function(datos){
    $("#"+html_id).html("<option value=' '>--SELECCIONAR--</option>"+datos);
  });
}
var noSelect=function(id_html){
  $("#"+id_html).html("<option value=' '>--SELECCIONAR--</option>");
  $("#"+id_html).attr("enable","true");
}
var requerido=function(){

    $("#tra_estado").attr("required","true");
    $("#tra_municipio").attr("required","true");
    $("#tra_parroquia").attr("required","true");
    $("#tra_lugar").attr("required","true");

}
var noRequerido=function(){
    $("#tra_estado").removeAttr("required");
    $("#tra_municipio").removeAttr("required");
    $("#tra_parroquia").removeAttr("required");
    $("#tra_lugar").removeAttr("required");

    $("#tra_estado").parent().parent().removeClass("has-error");
    $("#tra_municipio").parent().parent().removeClass("has-error");
    $("#tra_parroquia").parent().parent().removeClass("has-error");
    $("#tra_lugar").parent().parent().removeClass("has-error");

}

//Mostrar calendario
 $(function() {
   cargarDelitos("delitos");
   $( "#fecha" ).datepicker({
     changeMonth: true,
     changeYear: true,
     yearRange:"1900:c",
     maxDate:"0",
     dateFormat:'dd/mm/yy'
   });
//carga los datos de un detenido ya registrado y avísa que los datos ya se encuentran en la DB
var actualizarDatos=function(datos){
 
  

   $("#primer_nombre").val(datos.p_nombre);
   $("#segundo_nombre").val(datos.s_nombre);
   $("#primer_apellido").val(datos.p_apellido);
   $("#segundo_apellido").val(datos.s_apellido);
   $("#imagen").attr("src",datos.dic_imagen);
   $("#fecha").val(datos.fecha_nacimiento);
   $("#num_telefono").val(datos.telefono);
   $("#profesion").val(datos.profesion);
   $("input[name='sexo'][value='"+datos.sexo+"']").attr('checked','checked');
   $("#nacionalidad option[value='"+datos.nacionalidad+"']").attr('selected','selected');
   $("#estado_civil option[value='"+datos.estadocivil.toLowerCase()+"']").attr('selected','selected');
   
   //para cargar las direcciones Residencia
     var estRes=$("#res_estado option:contains('"+datos.estadocasa+"')");
     estRes.attr('selected', 'selected');
     cargaDir(1,estRes.val(),"res_municipio");
      
     setTimeout(function(){
        var muniRes=$("#res_municipio option:contains('"+datos.municipiocasa+"')");
        muniRes.attr('selected', 'selected');
        cargaDir(2,muniRes.val(),"res_parroquia");
     }, 1000);
     
     setTimeout(function(){
        var parroRes=$("#res_parroquia option:contains('"+datos.parroquiacasa+"')");
        parroRes.attr('selected', 'selected');
     }, 2000);
     
     $("#res_lugar").val(datos.lugarcasa);
  //para cargar el lugar de trabajo
    //si posee lugar de trabajo, cargar los datos.
    if(datos.parroquiat){
      var estTra=$("#tra_estado option:contains('"+datos.estadot+"')");
       estTra.attr('selected', 'selected');
       cargaDir(1,estTra.val(),"tra_municipio");
        
       setTimeout(function(){
          var muniTra=$("#tra_municipio option:contains('"+datos.municipiot+"')");
          muniTra.attr('selected', 'selected');
          cargaDir(2,muniTra.val(),"tra_parroquia");
       }, 1000);
       
       setTimeout(function(){
          var parroTra=$("#tra_parroquia option:contains('"+datos.parroquiat+"')");
          parroTra.attr('selected', 'selected');
       }, 2000);
       
       $("#tra_lugar").val(datos.lugart);
    }
 }
 
//---------------------------------------------------
//              CARGAR DATOS EN BASE A LA CI
//---------------------------------------------------

$("#cedula").change(function(){
  $.ajax({
    method:"POST",
    data:{ajax:6,cedula:$("#num_cedula").val()},
  }).done(function(datos){
    //console.log(typeof datos);
    datos=jQuery.parseJSON(datos);
    //console.log(datos);
    if (datos!=null) {
      $("#datos_existentes").show("slow");
      actualizarDatos(datos);
      /*global datosActualizados*/
      window.datosActualizados=true;
    }else {
      $("#datos_existentes").hide("slow");
      if(window.datosActualizados){
        //reiniciar el formulario
        $("#form")[0].reset();
        cargaDir(0,0,"res_estado");
        noSelect("res_municipio");
        noSelect("res_parroquia");
        
        cargaDir(0,0,"tra_estado");
        noSelect("tra_municipio");
        noSelect("tra_parroquia");
        window.datosActualizados=false;
      }
    }
  });
});


// cargar los datos de las direcciones de recidencia
   cargaDir(0,0,"res_estado");
   noSelect('res_municipio');
   noSelect('res_parroquia');
  var res_estado= $("#res_estado").change(function(){
     cargaDir(1,res_estado.val(),"res_municipio");
     if(res_estado.val()==''){
       noSelect('res_municipio');
       noSelect('res_parroquia');
     }else {
       res_municipio.removeAttr("disabled");
     }
   });

  var res_municipio=$("#res_municipio").change(function(){
    cargaDir(2,res_municipio.val(),"res_parroquia");
    if(res_municipio.val()==''){
      noSelect('res_parroquia');
    }else {
      $("#res_parroquia").removeAttr("disabled");
    }
  });


// cargar los datos de las direcciones de trabajo
cargaDir(0,0,"tra_estado");
noSelect('tra_municipio');
noSelect('tra_parroquia');
var tra_estado= $("#tra_estado").change(function(){
  cargaDir(1,tra_estado.val(),"tra_municipio");
  if(tra_estado.val().trim()==''){
    noSelect('tra_municipio');
    noSelect('tra_parroquia');
    noRequerido();
  }else {
    tra_municipio.removeAttr("disabled");
    requerido();
  }
});

var tra_municipio=$("#tra_municipio").change(function(){
 cargaDir(2,tra_municipio.val(),"tra_parroquia");
 if(tra_municipio.val()==''){
   noSelect('tra_parroquia');
 }else {
   $("#tra_parroquia").removeAttr("disabled");
 }
});
});


// Mostrar u ocultar elementos

   var Vista={
     required:new Array(12),
     mostrar:function(id){
       $(id).show( "slide",{direction: 'left'},1000);
       for(var i=0;i<this.required.length;i++){
         if(this.required[i]==id){
           $(id+' input').attr('required','true');
           delete this.required[i];
           break;
         }
       }
     },
     ocultar:function(id){
       $("#indocumentado").attr("checked","checked");
       document.getElementById("form").reset();
       $("#datos_existentes").hide();
       $(id).hide( "slide",{direction: 'left'},1000);
           if($(id+' input').attr('required')){
             $(id+' input').removeAttr('required');
             for(var i=0;i<this.required.length;i++){
               if(!this.required[i] || this.required[i]==id){
                 this.required[i]=id;
                 break;
               }
             }
           }
     }
   }

 </script>


<div class="container" >
  <div class="row" style="margin-top:5px;">
    <div class="col-md-2 col-md-offset-5">
      <a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atrás</a>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <?php if($tipo): ?>
      <h1 style="font-family:'Palatino Linotype', 'Book Antiqua', Palatino, serif;">Ficha de caución</h1>
      <?php else: ?>
      <h1 style="font-family:'Palatino Linotype', 'Book Antiqua', Palatino, serif;">Ficha de notificación</h1>
      <?php endif; ?>
    </div>
  </div>

  <div class="row">

    <div class="col-md-8 col-md-offset-2">

      <div class="bs-callout bs-callout-warning">
        <h4><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Atención</h4>
        <?php if($tipo): ?>
        <p>Introduzca los datos de los detenidos. Los campos con un <span class="glyphicon glyphicon-asterisk"></span> son obligatorios. Todos los demás son opcionales.</p>
        <?php else: ?>
        <p>Introduzca los datos de la persona que realiza la notificación. Los campos con un <span class="glyphicon glyphicon-asterisk"></span> son obligatorios. Todos los demás son opcionales.</p>
        <?php endif; ?>
      </div>

      <form class="form form-horizontal bs-callout bs-callout-default " action="index.php?act=crearFichas" enctype="multipart/form-data" method="post" style="padding-top:5px;" id="form">

          <?php if($tipo): ?>
        <div class="form-group" style="margin-left:10px;">
          <div class="col-md-12" style="margin:0px; padding:0px;">
            <div class="bs-callout bs-callout-default" style="margin:1px;padding:0px; padding-left:15px; padding-top:5px;">
              <div class="form-group" style="margin:0px; padding:0px;">
                <label>&nbsp;Documentado</label>
                <input type="radio" name="doc" value="doc" checked="cheked" onclick="Vista.mostrar('#cedula')" id="documentado">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Indocumentado</label>
                <input type="radio" name="doc" value="in" onclick="Vista.ocultar('#cedula')" id="indocumentado">

              </div>
            </div>
          </div>
        </div>

        <?php endif; ?>

            <div class="bs-callout bs-callout-danger" style="display:none; margin-left:10px;" id="datos_existentes">
              <h4>Datos ya registrados en el sistema</h4>
              <a href="#" class="thumbnail" style="width:171px;">
                <img src='' id="imagen" style='width:171px; height:180px'>
              </a>
            </div>
        <div class="bs-callout bs-callout-default" style="margin-left:10px;">
            <div class="form-group" style="margin-top:5px;">
              <div class="col-md-2">
                <label for="sexo">Género:</label>
              </div>
              <div class="col-md-4">
                <div class="input-group">
                  <label class="radio-inline"><input type="radio" name="sexo" value="H" checked>Masculino</label>
                  <label class="radio-inline"><input type="radio" name="sexo" value="M">Femenino</label>
                </div>
              </div>
            </div>

        <div class="form-group" id="cedula" >
          <div class="col-md-2">
            <label for="cedula">Núm. Cédula:</label>
          </div>
          <div class="col-md-4" >
            <div class="input-group" >
              <input type="text" class="form-control" name="cedula" value="" required="required" maxlength="10" placeholder="Núm. cédula" id="num_cedula">
              <span class="input-group-addon "><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
            </div>
          </div>
          <div class="col-md-2">
            <label for="nacionalidad">Nacionalidad:</label>
          </div>
          <div class="col-md-4" >
            <div class="input-group" >
                <select class="selectpicker form-control" name="nacionalidad" id="nacionalidad">
                  <option value="V">VENEZOLANO/A</option>
                  <option value="E">EXTRANJERO/A</option>
                </select>
              <span class="input-group-addon "><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
            </div>
          </div>
        </div>


        <div class="form-group">
          <div class="col-md-2">
            <label for="primer_nombre">Primer nombre </label>
          </div>
          <div class="col-md-4">
            <div class="input-group">
              <input type="text" name="primer_nombre" class="form-control" maxlength="20" required="required" placeholder="Primer nombre" id="primer_nombre">
              <span class="input-group-addon "><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
            </div>
          </div>
          <div class="col-md-2">
            <label for="segundo_nombre">Segundo nombre</label>
          </div>
          <div class="col-md-4">
            <div class="input-group">
              <input type="text" name="segundo_nombre" class="form-control" maxlength="20" placeholder="Segundo nombre" id="segundo_nombre">
            </div>
          </div>
        </div>


        <div class="form-group">
          <div class="col-md-2">
            <label for="primer_apellido">Primer apellido</label>
          </div>
          <div class="col-md-4">

            <div class="input-group">
              <input type="text" name="primer_apellido" class="form-control" required="required" maxlength="20" placeholder="Primer apellido" id="primer_apellido">
              <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
            </div>
          </div>
          <div class="col-md-2">
            <label for="segundo_apellido">Segundo apellido</label>
          </div>
          <div class="col-md-4">
            <div class="input-group">
              <input type="text" name="segundo_apellido" class="form-control" maxlength="20" placeholder="Segundo apellido" id="segundo_apellido">
            </div>
          </div>
        </div>


        <div class="form-group">
          <div class="col-md-2">
            <label for="nacimiento">Fecha de nacimiento:</label>
          </div>
          <div class="col-md-4">
            <div class="input-group">
              <input type="text" name="fecha" class="form-control" readonly style="background:white" placeholder="dd/mm/aa"  id="fecha" required="true">
              <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
            </div>
          </div>


          <div class="col-md-2">
            <label for="estado_civil">Estado civil:</label>
          </div>
          <div class="col-md-4">
            <select class="selector form-control" name="estado_civil" id="estado_civil">
              <option value="soltero">SOLTERO/A</option>
              <option value="casado">CASADO/A</option>
              <option value="divorciado">DIVORCIADO/A</option>
              <option value="viudo">VIUDO/A</option>
            </select>
          </div>
        </div>




          <div class="form-group">
            <div class="col-md-2">
              <label for="telefono">Núm. Teléfono:</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="tel" class="form-control" name="num_telefono" value="" maxlength="15" placeholder="Numero de teléfono" id="num_telefono">
                <span style="color:grey; font-size:12px;">Sólo números Ej: 04167047661</span>
              </div>
            </div>


            <div class="col-md-2">
              <label for="profesion">Profesión u oficio:</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" name="profesion" class="form-control" value="" maxlength="40" placeholder="Profesión" id="profesion">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-2">
              <label for="observaciones">Observaciones</label>
            </div>
            <div class="col-md-10">
              <textarea class="form-control" style="resize:none" rows="4" cols="50" name="observacion" maxlength="500" placeholder="Observaciones del detenido. En este campo puede colocar cualquier observación. Ej: País de origen, rasgos fisicos, etc."></textarea>
            </div>
          </div>
          </div>


          <div class="form-group bs-callout bs-callout-default " style="margin-left:10px;">

              <div class="form-group">
                <div class="col-md-12">
                  <label for="recidencia">Lugar de residencia:</label>
                </div>
              </div>
              <div class="form-group" style="margin:0px;">
                <div class="col-md-2">
                  <label for="res_estado">Estado:</label>
                </div>
                <div class="col-md-9">
                  <div class="input-group">
                    <select class="form-control" name="res_estado" id="res_estado">
                    </select>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
                  </div>

                </div>
              </div>

              <div class="form-group" style="margin:0px;">
                <div class="col-md-2" style="margin-top:10px;">
                  <label for="res_municipio">Municipio:</label>
                </div>
                <div class="col-md-9" style="margin-top:10px;">
                  <div class="input-group">
                    <select name="res_municipio" class="form-control" id="res_municipio">
                    </select>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
                  </div>

                </div>
              </div>

              <div class="form-group" style="margin:0px;">
                <div class="col-md-2" style="margin-top:10px;">
                  <label for="res_parroquia">Parroquia:</label>
                </div>
                <div class="col-md-9" style="margin-top:10px;">
                  <div class="input-group">
                    <select name="res_parroquia" class="form-control" id="res_parroquia">
                    </select>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
                  </div>

                </div>
              </div>

              <div class="form-group" style="margin:0px;">
                <div class="col-md-2" style="margin-top:10px;">
                  <label for="res_parroquia">Dirección:</label>
                </div>
                <div class="col-md-9" style="margin-top:10px;">
                  <div class="input-group">
                    <input name="res_lugar" class="form-control" placeholder="Lugar" maxlength="20" id="res_lugar" required="required">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
                  </div>
                </div>
              </div>

          </div>

          <div class="form-group bs-callout bs-callout-default " style="margin-left:10px;">

              <div class="form-group">
                <div class="col-md-12">
                  <label for="recidencia">Lugar de trabajo:</label>
                </div>
              </div>
              <div class="form-group" style="margin:0px;">
                <div class="col-md-2">
                  <label for="tra_estado">Estado:</label>
                </div>
                <div class="col-md-9">
                  <select name="tra_estado" class="form-control" id="tra_estado">
                  </select>
                </div>
              </div>

              <div class="form-group" style="margin:0px;">
                <div class="col-md-2" style="margin-top:10px;">
                  <label for="tra_municipio">Municipio:</label>
                </div>
                <div class="col-md-9" style="margin-top:10px;">
                  <select name="tra_municipio" class="form-control" id="tra_municipio">
                  </select>
                </div>
              </div>

              <div class="form-group" style="margin:0px;">
                <div class="col-md-2" style="margin-top:10px;">
                  <label for="tra_parroquia">Parroquia:</label>
                </div>
                <div class="col-md-9" style="margin-top:10px;">
                  <select name="tra_parroquia" class="form-control" id="tra_parroquia">
                  </select>
                </div>
              </div>

              <div class="form-group" style="margin:0px;">
                <div class="col-md-2" style="margin-top:10px;">
                  <label for="tra_parroquia">Dirección:</label>
                </div>
                <div class="col-md-9" style="margin-top:10px;">
                  <input name="tra_lugar" class="form-control" placeholder="Lugar" maxlength="20" id="tra_lugar">
                </div>
              </div>

          </div>
        
        <div class="bs-callout bs-callout-default" style="margin-left:10px;">
          
          
          <div class="form-group">
            <div class="col-md-3">
              <?php if($tipo):?>
              <label for="tipo_delito">Tipo de delito:</label>
              <?php else: ?>
              <label for="tipo_delito">Delito a notificar:</label>
              <?php endif; ?>
            </div>
            <div class="col-md-8">
              <div class="input-group">
                <select class="form-control" name="tipo_delito" id="delitos" required="required">

                </select>
                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-2">
              <label for="descripcion">Descripción del delito:</label>
            </div>
            <div class="col-md-9">

              <div class="input-group">
                <textarea name="descripcion" class="form-control" rows="4" cols="40" style="resize:none;" required="required" maxlength="500" placeholder="Introduzca una descripción del delito. Máximo 500 caracteres."></textarea>
                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
              </div>
            </div>
          </div>


          <?php if($tipo): ?>
          <div class="form-group">
            <div class="col-md-2">
              <label for="imagen">Foto del detenido:</label>
            </div>
            <div class="col-md-8">
              <div class="input-group">
                <input type="file" class="form-control"  name="imagen" value="" required="required">
                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk" style="font-size:10px;"></span></span>
              </div>
              <span style="color:grey; font-size:12px;">Los formatos válidos para la foto del detenido son: ".jpg", ".png", ".jpeg"</span>
            </div>
          </div>
        <?php endif; ?>

        </div>
        
          <div class="form-group">
            <div class="col-md-2 col-md-offset-5">
              <button type="submit" name="enviar" value="<?php echo ($tipo)?'cau':'not'; ?>"class="btn btn-primary btn-block">Guardar</button>
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
}, "Formato no válido, solo se pueden introducir imagenes con extención '.png', '.jpeg' y '.jpg'");
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
}, "La sedula ya se encuentra registrada en la base de datos.");
jQuery.validator.addMethod("notNull", function(value, element) {
  return this.optional(element) || value.trim();
}, "Este campo es obligatorio.");
  $("#form").validate({
        rules:{
          cedula:{maxlength:10,minlength:6,digits:true},
          primer_nombre:{minlength:2,maxlength:20,soloLetras:true,required:true},
          segundo_nombre:{minlength:2,maxlength:20,soloLetras:true},
          primer_apellido:{minlength:2,maxlength:20,soloLetras:true,required:true},
          segundo_apellido:{minlength:2,maxlength:20,soloLetras:true},
          num_telefono:{minlength:7,maxlength:15,digits:true},
          profesion:{soloLetras:true},
          tipo_delito:{notNull:true},
          res_estado:{notNull:true},
          res_municipio:{notNull:true},
          res_parroquia:{notNull:true},
          imagen:{soloImagenes:true},
          observacion:{maxlength:500}
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
