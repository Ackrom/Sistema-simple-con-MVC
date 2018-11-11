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
						<a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atras</a>
					</div>


				</div>
			</div>
      <div class="row" style="margin-top:15px">
        <div class="col-md-8 col-md-offset-2">
            <div class="bs-callout bs-callout-default">
                <h4>Respaldo de la base de datos.</h4>
                <div class="bs-callout bs-callout-warning">
                    <h4><span class="glyphicon glyphicon-eye-open"></span> Para realizar un respaldo de la base de datos PostgreSQL debe ubicar el archivo "psql" que se encuentra en la carpeta "bin" donode se instaló el postres. Tambien necesita el archivo de respaldo con extención ".SQL" (Predeterminadamente creado en el directorio "APP" del sistema).</h4>
                </div>
                <form class="form" action="index.php?act=restaurar" method="post">
                    <div class="form-group">
                        <label for="dir_pg">Dirección del archivo "psql"</label>
                        <input type="text" name="dir_pg" class="form-control" required="required"/>

                        <label for="dir_res">Dirección del archivo de respaldo ".SQL".</label>
                        <input type="text" name="dir_res" value="" class="form-control" required="required">
                        <input type="submit" value="Submit" class="button button-default"/>
                    </div>
                </form>
                <?php if($datos): ?>
                  <span style="color:green;">Base de datos restaurada correctamente.</span>
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
