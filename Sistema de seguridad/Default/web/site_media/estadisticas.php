<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
<body>
  <div class="container" style="margin-top:10px;">
  <div class="row" style="margin:5px">
    <div class="col-md-2 col-md-offset-5">
      <a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atrás</a>
    </div>
  </div>
    <div class="row">
      <div class="col-md-8 col-md-offset-2 panel panel-default">
        <div class="row panel-heading">
          <h4>Gráficas</h4>
        </div>
        <div class="bs-callout bs-callout-default">
          Cantidad de detenidos en función del tiempo.
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
            <form id="form" action="#" class="form-horizontal">
              <div class="form-group">
                <div class="col-md-3">
                  <label for="year">Año:</label>
                </div>
                <div class="col-md-3">
                  <select name="year" id="year" class="form-control">
                    <?php
                      $ahora=date('Y');
                      for($i=$ahora;$i>=($ahora-50);$i--)
                        echo "<option value='$i'>$i</option>";
                    ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                  <div class="col-md-3">
                    <label for="mes">Mes:</label>
                  </div>

                  <div class="col-md-3">
                    <select name="mes" id="mes" class="form-control">
                      <?php
                        $meses=array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                        for($i=1;$i<=12;$i++)
                          echo "<option value='$i'>{$meses[$i-1]}</option>";
                      ?>
                    </select>
                  </div>

              </div>

              <div class="form-group">
                  <div class="col-md-3">
                    <label for="tipo">Tipo de gráfica:</label>
                  </div>

                  <div class="col-md-3">
                    <select name="tipo" id="tipo" class="form-control">
                      <option value="pie">PASTEL</option>
                      <option value="bar">BARRAS (VERTICAL)</option>
                    </select>
                  </div>

              </div>
              <button type="submit" name="enviar" class="btn btn-primary">Buscar</button>
            </form>
            </div>
          </div>

          <div class="row" style="margin-top:10px;">
            <div class="col-md-8 col-md-offset-2">
              <div class="row panel panel-default" style="display:none;" id="mensaje1">
                <div class="panel-heading">
                  No hay estadísticas para el año-mes seleccionados
                </div>
              </div>
              <div id="contenedor1">
                <canvas id="grafico" width="400px" height='400px' style="display:none;"></canvas>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script type="text/javascript">

  $("#form").submit(function(){
    getDatos();
    return false;
  });
  var getDatos=function(){
    $.ajax({
      method:"POST",
      data:{ajax:9,year:$("#year").val(),mes:$("#mes").val()}
    }).done(function(datos){
      datos=jQuery.parseJSON(datos);

      if(datos.length>0){
        $("#contenedor1").empty();
        $("#contenedor1").html('<canvas id="grafico" width="400px" height="400px" style="display:none;"></canvas>');
        $("#mensaje1").hide("slow");
        mostrarGrafica('grafico',datos,$("#tipo").val());
        $("#grafico").show("slow");
      }else{
        $("#mensaje1").show("slow");
        $("#grafico").hide("slow");
      }
    });
  }


  var mostrarGrafica=function(idCanvas,datos,tipo){
    var nombres=[];
    var data=[];
    for(var i=0;i<datos.length;i++){
      nombres[i]=datos[i][1];
      data[i]=datos[i][0];
    }
    switch(tipo){
      case 'bar':
        dataByType={
          labels: nombres,
            datasets: [{
                label: '# de delitos',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        }
      break;
      case 'polarArea':
        dataByType={
           datasets: [{
            data: data,
            backgroundColor: [
                "#FF6384",
                "#4BC0C0",
                "#FFCE56",
                "#E7E9ED",
                "#36A2EB"
              ],
              label: 'leyenda'
          }],
          labels: nombres
        }
        break;
       case 'pie':
        dataByType={
          labels: nombres,
            datasets: [{
                label: '# de delitos',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        }
      break;
    }


    var ctx = document.getElementById(idCanvas).getContext('2d');
    var myChart = new Chart(ctx, {
        type: tipo,
        data: dataByType,
        options: {
          responsive:true,
            scales: {
                yAxes: [{
                    ticks: {
                      min:0,
                      stepSize: 1
                    }
                }]
            }
        }
    });
  }
    </script>
</body>
</html>
