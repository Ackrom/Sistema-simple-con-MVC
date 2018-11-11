<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
<script type="text/javascript">

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
            <div class="bs-callout bs-callout-default">
                <h4>Respaldo de la base de datos.</h4>
                <div class="bs-callout bs-callout-warning">
                    <h4><span class="glyphicon glyphicon-eye-open"></span> Para realizar un respaldo de la base de datos PostgreSQL, debe ubicar el archivo pg_dump que se encuentra en la carpeta "\bin" donde se instaló el PostgreSQL.</h4>
                </div>
                <form class="form" action="index.php?act=respaldo" method="post">
                    <div class="form-group">
                        <label for="dir_pg">Dirección del archivo "pg_dump"</label>
                        <input type="text" name="dir_pg" class="form-control"/>
                        <input type="submit" value="Submit" class="button button-default"/>
                    </div>
                </form>
                <?php if($datos): ?>
                  <span style="color:green;">Base de datos respaldada correctamente. El archivo de respaldo se encuentra en la siguiente dirección:<?php echo " ".dirname(APP_PATH).'\BDD.sql'; ?></span>
                <?php endif; ?>
            </div>
        </div>
      </div>
    </div>

<?php
  require_once WEB_PATH."layout/footer.php";
?>

  </body>
</html>
