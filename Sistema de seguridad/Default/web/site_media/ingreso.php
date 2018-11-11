<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>

<div class="container-fluid" >
  <div class="row" >
    <div class="col-md-6 col-md-offset-3 ">
      <div class="bs-callout bs-callout-warning">
        <h4><i class="glyphicon glyphicon-eye-open"></i>&nbsp;Importante</h4>
        &nbsp;Para crear una cuenta, pónganse en contacto con el administrador del sistema.
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-md-offset-3 panel panel-default">

      <div class="row panel-heading">
        <div class="col-md-12 panel-title">
          Inicio de sesión
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 panel-body">
          <form class="form-horizontal" action="index.php?act=ingreso" method="post">


              <div class="form-group">
                <label class="col-md-3 col-md-offset-1 control-label" for="nombre">Nombre de usuario:</label>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                    <input class="form-control" type="text" name="nombre" value="" required="required" maxlength="30">
                  </div>
                </div>
              </div>


              <div class="form-group">
                <label class="col-md-3 col-md-offset-1 control-label" for="pass">Contraseña:</label>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                    <input class="form-control" type="password" name="pass" value="" required="required" maxlength="60">
                  </div>


                  <?php if($error): ?>
                    <div class="row">
                      <div class="col-md-12" style="color:red;">
                        <?php echo $error; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-6 col-md-offset-3">
                  <button type="submit" name="button" class="btn btn-block btn-primary">Ingresar</button>
                </div>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
  require_once WEB_PATH."layout/footer.php";
?>

  </body>
</html>
