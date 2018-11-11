<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>

    <div class="container-fluid">
      <div class="row" style="margin-top:30px;">
        <div class="col-md-8 col-md-offset-2 well">
          <div class="row" >
            <div class="col-md-4">
              <a href="index.php?act=crearFichas&tipo=cau&doc=doc" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-tasks " style="color:#BDBDBD" > </span>&nbsp;&nbsp;Registrar Caución</a>
            </div>
            <div class="col-md-4">
              <a href="index.php?act=buscarDet" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-search " style="color:#BDBDBD" > </span>&nbsp;&nbsp;Buscar Cauciones</a>
            </div>
            <div class="col-md-4">
              <a href="index.php?act=estadisticas" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-equalizer " style="color:#BDBDBD" > </span>&nbsp;&nbsp;Estadísticas</a>
            </div>
          </div>
          <div class="row" style="margin-top:5px;">
            <div class="col-md-4">
              <a href="index.php?act=crearFichas&tipo=notif" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-edit " style="color:#BDBDBD" > </span>&nbsp;&nbsp;Registrar Notificación</a>
            </div>
            <div class="col-md-4">
              <a href="index.php?act=buscarNot" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-search " style="color:#BDBDBD" > </span>&nbsp;&nbsp;Buscar Notificaciones</a>
            </div>

            <div class="col-md-4">
              <a href="index.php?act=salir" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-off " style="color:#BDBDBD" > </span>&nbsp;&nbsp;Salir</a>
            </div>

          </div>


        </div>
      </div>
      <div class="row" style="margin-top:15px;">
        <div class="col-md-8 col-md-offset-2">
          <div class="bs-callout bs-callout-warning">
            <h4><span class="glyphicon glyphicon-eye-open"></span> Búsqueda simple</h4>
            <p>
              La búsqueda que se muestra a continuación es la "búsqueda simple", una búsqueda rapida. Si desea realizar una búsqueda más exhaustiva, presione el botón "Buscar Cauciones" o "Buscar Notificaciones" en la barra superior.
            </p>
          </div>
          <form action="#" method="post" id="form-buscar">
            <div class="form-group">
              <label for="busqueda">Buscar detenidos:</label>

              <div class="input-group">
                <input type="text" name="busqueda" class="form-control" placeholder="Cedula, nombre o apellido" id="buscar">
                <span class="input-group-btn"><button type="submit" class="btn btn-primary" >Buscar</button></span>
              </div>

            </div>
          </form>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8 col-md-offset-2" id="contenedor1">
          <table class="table table-hover table-bordered" id="tabla1">
            <thead>
              <tr>
                <th>
                  Perfil
                </th>
                <th>
                  Imagen
                </th>
                <th>
                   # cédula
                </th>
                <th>
                  Primer nombre
                </th>
                <th>
                  Primer apellido
                </th>
                <th>
                  Tipo delito
                </th>
                <th>
                  Fecha creación
                </th>
                <th>
                  Documentación
                </th>
              </tr>
            </thead>
            <tbody id="contenido1">

            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="bs-callout bs-callout-warning">
            <h4><span class="glyphicon glyphicon-eye-open"></span> Detenidos indocumentados</h4>
            <p>
              A continuación se muestra una lista con los detenidos indocumentados.<br>
              NOTA: Los detenidos con datos no comprobados pueden ser buscados mediante los dos tipos de búsquedas ("búsqueda simple" y "búsqueda avanzada")
            </p>
          </div>
        </div>
        <div class="col-md-8 col-md-offset-2" id="contenedor2">
          <center><h3>Listado de detenidos indocumentados</h3></center>
          <table class="table table-hover" id="tabla2">
            <thead>
              <tr>
                <th>
                  Perfil
                </th>
                <th>
                  Imagen
                </th>
                <th>
                  Primer nombre
                </th>
                <th>
                  Primer apellido
                </th>
                <th>
                  Tipo de delito
                </th>
                <th>
                  fecha de la Detención
                </th>
              </tr>
            </thead>
            <tbody id="contenido2">

            </tbody>
          </table>
        </div>
      </div>
    </div>
<script type="text/javascript">
  modTabla1=$('#contenedor1').html();
  modTabla2=$('#contenedor2').html();

  var Tabla=function(id){
    $(id).dataTable({
      "bFilter":false,
      "bDestroy":true,
      "colReorder":true
    });
  }
  Tabla("#tabla1");

  var busquedaSimple=function(search){
    $.ajax({
      method:"POST",
      data:{ajax:4,busqueda:search}
    }).done(function(datos){
      $("#contenido1").html(datos);
      Tabla('#tabla1');
    });
  }
  
  
  var listaIndocumentados=function(){
    $.ajax({
      method:"POST",
      data:{ajax:7}
    }).done(function(datos){
      $("#contenido2").html(datos);
      Tabla('#tabla2');
    });
  }
  
  
    $(function(){
      $('#buscar').keydown(function(){
        $("#contenedor1").empty();
        $("#contenedor1").html(modTabla1);
        busquedaSimple($('#buscar').val(),$("input[name='modo']:checked").val());
      });
      $("#form-buscar").submit(function(){
        $("#contenedor1").empty();
        $("#contenedor1").html(modTabla1);
        busquedaSimple($('#buscar').val(),$("input[name='modo']:checked").val());
        return false;
      });
      listaIndocumentados();
    });
    
</script>
<?php
  require_once WEB_PATH."layout/footer.php";
?>

  </body>
</html>
