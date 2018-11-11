<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>

    <div class="container">
      <div class="row" style="margin-top:30px;">
        <div class="col-md-4 col-md-offset-4 well">
          <a href="index.php?act=registrar" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-plus " style="color:#BDBDBD" > </span><br>Registrar usuarios</a>
          <a href="index.php?act=verCuentas" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-search " style="color:#BDBDBD" > </span><br>Buscar usuarios</a>
          <a href="index.php?act=auditoria" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-folder-open" style="color:#BDBDBD" > </span><br>Auditoría</a>
          <a href="index.php?act=respaldo" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-floppy-save" style="color:#BDBDBD" > </span><br>Respaldar Datos</a>
          <a href="index.php?act=restaurar" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-floppy-open" style="color:#BDBDBD" > </span><br>Restaurar Datos</a>

          <a href="index.php?act=salir" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-off " style="color:#BDBDBD" > </span><br>Salir</a>
        </div>
      </div>
    </div>

<?php
  require_once WEB_PATH."layout/footer.php";
?>

  </body>
</html>
