<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>
<div class="container-fluid">
  <div class="row" style="margin-top:10px;">
  <a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atrás</a>

    <div class="col-md-8 col-md-offset-2 well" style="margin-top:15px;">
      <center><h1><?php echo $error; ?></h1></center>
    </div>
  </div>
</div>

<?php
  require_once WEB_PATH."layout/footer.php";
?>

  </body>
</html>
