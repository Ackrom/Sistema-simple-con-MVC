<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
<script type="text/javascript">

$(document).ready(function() {
	$('#tabla').DataTable( {
		stateSave: true
	} );
	$('#desactivar').trigger("click");
} );


</script>
  <body>

    <div class="container" style="margin-top:30px;">
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-2 col-md-offset-2">
						<a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atrás</a>
					</div>


				</div>
			</div>
      <div class="row" style="margin-top:15px">
        <div class="col-md-8 col-md-offset-2">
          <table class="table table-hover" id="tabla">
            <thead>
              <tr>
                <th>
                   # cédula
                </th>
                <th>
                  Nombre de usuario
                </th>
                <th>
                  Primer nombre
                </th>
                <th>
                  Primer apellido
                </th>
                <th id="desactivar">
                  Desactivar/activar
                </th>
								<th id="modificar">
                  Modificar
                </th>
              </tr>
            </thead>
            <tbody>
              <?php for($i=0;$i<count($data);$i++):

                if($data[$i]['activo']==1){
                  echo "<tr class='success'>";
                }else{
                  echo "<tr class='danger'>";
                }
              ?>

                <td>
                  <?php echo $data[$i]['cedula']; ?>
                </td>
                <td>
                  <?php echo $data[$i]['nombre']; ?>
                </td>
                <td>
                  <?php echo $data[$i]['p_nombre']; ?>
                </td>
                <td>
                  <?php echo $data[$i]['p_apellido']; ?>
                </td>
                <td>
                  <?php
                  $nombre=$data[$i]['nombre'];
                  if($data[$i]['activo']==1)
                    echo "<a href='index.php?act=desactivar&usu=$nombre'><span class='glyphicon glyphicon-remove-circle'></span><i style='display:none'>a</i></a>";
                  else
                    echo "<a href='index.php?act=activar&usu=$nombre'><span class='glyphicon glyphicon-ok-sign'></span><i style='display:none'>b</i></a>";
                  ?>
                </td>
								<td>
									<?php
									echo "<a href='index.php?act=modificar&usu=$nombre'><span class='glyphicon glyphicon-pencil'></span><i style='display:none'>a</i></a>";
									 ?>
								</td>
              </tr>
            <?php endfor; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

<?php
  require_once WEB_PATH."layout/footer.php";

  /*<tr>
    <td>
      24558858
    </td>
    <td>
      Usuario001
    </td>
    <td>
      Pedro
    </td>
    <td>
      Perez
    </td>
    <td>
      Nivel 2
    </td>
    <th>
      <a href="#"><span class="glyphicon glyphicon-pencil"></span></a>
    </th>
  </tr>*/
?>

  </body>
</html>
