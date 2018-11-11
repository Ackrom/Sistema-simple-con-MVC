<nav class="navbar navbar-inverse" style="border-radius:0px; margin:0px;" role="navigation">
  <div class="container-fluid" >
      <div class="row">
              <div class="navbar-header" >
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#colapse" >
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              </div>
            <div class="col-md-11 col-md-offset-1">
                <div class="navbar-collapse collapse " id="colapse">
                  <ul class="nav navbar-nav">
                    <li><a href="index.php?act=menu"><center><span class="glyphicon glyphicon-home"></span><br>Menú</center></a></li>
                    <li><a href="index.php?act=fichaNotif"><center><span class="glyphicon glyphicon-list-alt"></span><br>Ficha notificación</center></a></li>
                    <li><a href="index.php?act=fichaCauc"><center><span class="glyphicon glyphicon-list-alt"></span><br>Ficha caución</center></a></li>
                    <li>
                      <form class="form-inline" action="index.php?act=buscar" method="post" style="margin:15px;">
                        <div class="form-group">
                          <label for="cerdula" style="color:#9d9d9d">Buscar detenidos: </label>
                          <div class="input-group">
                            <input type="number" name="cedula" placeholder="Buscar..." class="form-control" required="required" maxlength="12" />
                            <span class="input-group-btn"><button type="submit" name="button" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                          </div>
                        </div>
                      </form>
                    </li>
                    <li><a href="index.php?act=salir"><center>Salir</center></a></li>
                  </ul>
                </div>
             </div>
          </div>
  </div>
</nav>
