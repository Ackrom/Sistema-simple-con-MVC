<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>
<div class="container">
    <div class="row" style="margin-top:5px;">
      <div class="col-md-2 col-md-offset-5">
        <a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atras</a>
      </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php if($estado==1 || $estado==3):?>
            <form class="form-inline" method="post" action="index.php?act=desactivar&usu=<?php echo $_GET['usu'];?>">
                <h1>¿Esta seguro que desea desactivar la cuenta del usuario <?php echo $_GET['usu'] ?> ?</h1>
                Si desea desactivarlo, introduzca su contraseña y presione Continuar. 
                <input class="form-control" type="password" name="pass"/>
                <button name="enviar" value="1" class="btn btn-primary">Continuar</button>
                <?php if($estado==3):?>
                <span>contraseña inválida</span>
                <?php endif; ?>
            </form>
            <?php else:?>
            <h1>Usuario desactivado</h1>
            <?php endif;?>
        </div>
    </div>
</div>

<?php
  require_once WEB_PATH."layout/footer.php";
?>

  </body>
</html>
