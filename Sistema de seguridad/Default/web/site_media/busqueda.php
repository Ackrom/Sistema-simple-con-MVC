<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>

    <div class="container-fluid">
      <div class="row" style="margin-top:15px; ">
        <div class="col-md-12">
          <div class="col-md-2 col-md-offset-5">
            <a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atrás</a>
          </div>
        </div>
        <div class="col-md-12">
          <?php if($tipo==1): ?>
          <h1 style="font-family:'Palatino Linotype', 'Book Antiqua', Palatino, serif;">Busqueda de cauciones</h1>
          <?php else: ?>
          <h1 style="font-family:'Palatino Linotype', 'Book Antiqua', Palatino, serif;">Busqueda de notificaciones</h1>
          <?php endif; ?>
        </div>
        <div class="col-md-10 col-md-offset-1 ">
          <form action="index.php?act=buscar" method="post" class="form form-horizontal bs-callout bs-callout-default" id="form">
            <div class="form-group">
              <div class="form-group">
                <div class="col-md-2">
                  <label>Filtros:</label>
                </div>
              </div>

              <div class="form-group">
                <div class="col-md-2">
                  <label for="fecha">Por fecha:</label>
                </div>
                <div class="col-md-5">
                  <select class="form-control" name="fecha" id="fecha">
                    <option value="cf">-- Cualquier fecha --</option>
                    <option value="24h">Últimas 24 horas</option>
                    <option value="us">Última semana</option>
                    <option value="um">Último mes</option>
                    <option value="ua">Último año</option>
                    <option value="intervalo" onclick="$('#Rango').show('slow')">Intervalo perzonalizado...</option>
                  </select>
                </div>
              </div>
              
              <div class="form-group" id="Rango" style="display:none;">
                <div class="col-md-2">
                  <label for="nacimiento">Rango de fechas:</label>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <i>Desde:</i>
                    <input type="text" name="desde" class="form-control" readonly style="background:white" placeholder="dd/mm/aa"  id="fdesde">
                  </div>
                  <div class="input-group">  
                    <i>Hasta</i>
                    <input type="text" name="hasta" class="form-control" readonly style="background:white" placeholder="dd/mm/aa"  id="fhasta">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-2">
                  <label for="tipo_delito">Por tipo de delito:</label>
                </div>
                <div class="col-md-5">
                  <select class="form-control" name="tipo_delito" id="tipo_delito">

                  </select>
                </div>

              </div>

            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group" style="margin-top:20px;">
                    <input type="text" name="busqueda" class="form-control" maxlength="50" id="busqueda">
                    <span class="input-group-btn"><button type="submit" class="btn btn-primary" >Buscar</button></span>
                  </div>
                </div>
              </div>

            </div>
          </form>
        </div>
      </div>

      <div class="row">
        <div class="col-md-10 col-md-offset-1" id="con">
          <table class="table table-hover table-bordered" id="tabla">
            <thead>
              <tr>
                <th>
                  Perfil
                </th>
                <?php if($tipo==1): ?>
                <th>
                  Imagen
                </th>
              <?php endif; ?>
                <th>
                   # cedula
                </th>
                <th>
                  Nombres
                </th>
                <th>
                  Apellidos
                </th>
                <th>
                  Tipo delito
                </th>
                <th>
                  Fecha creación
                </th>
                <?php if($tipo==1): ?>
                <th>
                  Documentación
                </th>
              <?php endif; ?>
              </tr>
            </thead>
            <tbody id="contenedor">

            </tbody>
          </table>
        </div>
      </div>
    </div>

<?php
  require_once WEB_PATH."layout/footer.php";
?>
<script type="text/javascript">
var rangoDeFechas=function(){
  $("#fdesde").datepicker({
    onClose: function (selectedDate) {
      $("#fhasta").datepicker("option", "minDate", selectedDate);
    },
    changeMonth: true,
    changeYear: true,
    yearRange:"1900:c",
    maxDate:"0",
    dateFormat:'dd/mm/yy'
  });
  $("#fhasta").datepicker({
    onClose: function (selectedDate) {
      $("#fdesde").datepicker("option", "maxDate", selectedDate);
    },
    changeMonth: true,
    changeYear: true,
    yearRange:"1900:c",
    maxDate:"0",
    dateFormat:'dd/mm/yy'
  });
}

var cargarDelitos=function(html_id){
  $.ajax({
    method:"POST",
    data:{ajax:5},
  }).done(function(datos){
    $("#"+html_id).html("<option value=' '>--seleccionar--</option>"+datos);
  });
}
//modelo de la tabla
  modTabla=$('#con').html();
  var tabla=function(id) {
    _tabla=$('#'+id).dataTable( {
      "bFilter":false,
      "bDestroy":true,
      "colReorder":true
    } );

  }
  var buscar=function(search,id_tabla){
    var fech;
    if($("#fecha").val()=='intervalo'){
      fech=$('#fdesde').val()+';'+$("#fhasta").val();
    }else{
      fech=$("#fecha").val();
    }
    $.ajax({
      method:"POST",
      data:{ajax:<?php echo ($tipo==1)?'2':'8'; ?>,busqueda:search,filtro:{fecha:fech,tipo_delito:$("#tipo_delito").val()}}
    }).done(function(datos){
      $(id_tabla).html(datos);
      tabla('tabla');
    })
  }
  tabla('tabla');
  $(function(){
    rangoDeFechas();
    $("#fecha").change(function(){
      if($(this).val()=='intervalo'){
        $("#Rango").show('slow');
      }else{
        $("#Rango").hide('slow');
      }
    });
    $('#busqueda').keydown(function(){
      $("#con").empty();
      $("#con").html(modTabla);
      buscar($('#busqueda').val(),'#contenedor');

    });
    $('#form').submit(function(){
      $("#con").empty();
      $("#con").html(modTabla);
      buscar($('#busqueda').val(),'#contenedor');
      return false;
    });
    cargarDelitos('tipo_delito');
  });

</script>
  </body>
</html>
