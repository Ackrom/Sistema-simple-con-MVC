<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";

?>
<script type="text/javascript">

$(document).ready(function() {
	$('#tabla').DataTable( {
		stateSave: true
	} );
} );


</script>
  <body>

    <div class="container" style="margin-top:30px;">
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-2 col-md-offset-2">
						<a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atras</a>
					</div>


				</div>
			</div>
      <div class="row" style="margin-top:15px">
        <div class="col-md-8 col-md-offset-2">
          <table class="table table-hover" id="tabla">
            <thead>
              <tr>
                <th>
                   ID
                </th>
                <th>
                  Nombre de usuario
                </th>
                <th>
                  Descripción
                </th>
                <th>
                  Fecha y hora
                </th>
              </tr>
            </thead>
            <tbody>
              <?php for($i=0;$i<count($data);$i++): ?>
              <tr>
                <td>
                  <?php echo $data[$i]['id']; ?>
                </td>
                <td>
                  <?php echo $data[$i]['usuario']; ?>
                </td>
                <td>
                  <?php echo $data[$i]['descripcion']; ?>
                </td>
                <td>
                  <?php
                    $fecha=explode(".",$data[$i][3]);
                    echo $fecha[0]; 
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
