<?php

require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>
    <div class="container-fluid">

    <div class="row" style="margin:3px">
      <div class="col-md-8 col-md-offset-2" style="height:500px">
        <object data="<?php echo VISUAL_PDF.'historial.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1'; ?>" type="application/pdf" width="100%" height="100%" internalinstanceid="3" title="">
        </object>
      </div>

    </div>
  </div>

  <div class="row" style="margin:20px">
    <div class="col-md-8 col-md-offset-2">
      <a href="index.php?act=menuNvl1" class="btn btn-primary btn-block">Regresar al menú</a>
    </div>
  </div>
<?php
  require_once WEB_PATH."layout/footer.php";
?>

  </body>
</html>