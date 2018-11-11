<?php
require_once WEB_PATH."layout/head.php";
require_once WEB_PATH."layout/header.php";
?>
  <body>
<div class="container" style="margin-top:10px;">
        <div class="row">
            <div class="col-md-2 col-md-offset-5">
                <a href="index.php?act=menuNvl<?php echo Session::get('permisos'); ?>" class="btn btn-block" style="background:#333333; color:#BDBDBD"><span class="glyphicon glyphicon-chevron-left" style="color:#BDBDBD" > </span> Atras</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <center>
                    <?php if(!isset($_GET['not'])): ?>
                    <h3>Perfil del detenido</h3>
                    <?php else: ?>
                    <h3>Perfil notificación</h3>
                    <?php endif; ?>
                </center>
            </div>
        </div>
        
    <div class="row">
        <div class="col-md-8 col-md-offset-2 ">
        <div class="bs-callout bs-callout-default">
        <?php if(!isset($_GET['not'])): ?>
        <div class="row">
            <div class="col-md-2 col-md-offset-5">
                <a href="#" class="thumbnail" style="width:171px;">
                    <?php 
                    
                    $aux=explode('/',$datos[0]['dic_imagen']);
                    $aux=VISUAL_IMG.array_pop($aux);
                    ?>
                  <img src='<?php echo $aux; ?>' style='width:171px; height:180px'>
                </a>
            </div>
        </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <?php if(!isset($_GET['not'])): ?>
                <div class="row">
                    <div class="col-md-4">
                      Documentos:  
                    </div>
                    <div class="col-md-8">
                        <?php if($datos[0]['cedula']<0){echo "<span style='color:red;'>INDOCUMENTADO</span>";}else{echo "<span style='color:green;'>DOCUMENTADO</span>";}?>
                            
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-4">
                        Cédula:
                    </div>
                    <div class="col-md-8">
                        <?php 
                            if($datos[0]['cedula']<0) 
                                echo "<span style='color:red;'>INDOCUMENTADO</span>";
                            else
                                echo $datos[0]['cedula'];
                        ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        Nacionalidad    
                    </div>
                    <div class="col-md-8">
                        <?php if(strcmp($datos[0]['nacionalidad'],'V')==0) echo "VENEZOLANO"; else echo "EXTRANJERO";?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        Nombres:
                    </div>
                    <div class="col-md-8">
                        <?php echo "{$datos[0]['p_nombre']} {$datos[0]['s_nombre']}"; ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        Apellidos:  
                    </div>
                    <div class="col-md-8">
                        <?php echo "{$datos[0]['p_apellido']} {$datos[0]['s_apellido']}";?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        Fecha nacimiento:  
                    </div>
                    <div class="col-md-8">
                       <?php
                        $aux=explode("-",$datos[0]['fecha_nacimiento']);
                        echo "{$aux[2]}-{$aux[1]}-{$aux[0]}";
                       ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        Estado civil:    
                    </div>
                    <div class="col-md-8">
                       <?php echo "{$datos[0]['estadocivil']}";?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                         Num. Teléfono  
                    </div>
                    <div class="col-md-8">
                        <?php if($datos[0]['telefono']) echo $datos[0]['telefono']; else echo "--SIN NÚMERO--"; ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        Profesión  
                    </div>
                    <div class="col-md-8">
                       <?php if($datos[0]['profesion'])echo $datos[0]['profesion']; else echo "--SIN PROFESIÓN--";?>
                    </div>
                </div>
                
                
                
                <div class="row">
                    <div class="col-md-4">
                        Dirección de Residencia:
                    </div>
                    <div class="col-md-8">
                        <?php echo "Estado: {$datos[0]['estadocasa']} </br> Municipio: {$datos[0]['municipiocasa']} </br> Parroquia: {$datos[0]['parroquiacasa']} </br> Lugar: {$datos[0]['lugarcasa']}"; ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        Dirección de trabajo:
                    </div>
                    <div class="col-md-8">
                        <?php if(isset($datos[0]['estadot'])) echo "Estado: {$datos[0]['estadot']} </br> Municipio: {$datos[0]['municipiot']} </br> Parroquia: {$datos[0]['parroquiat']} </br> Lugar: {$datos[0]['lugart']}";else echo "--SIN DIRECCIÓN--"; ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        Género    
                    </div>
                    <div class="col-md-8">
                      <?php if(strcmp($datos[0]['sexo'],'M')==0) echo "MASCULINO"; else echo"FEMENINO";?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                      Cantidad de incidentes:  
                    </div>
                    <div class="col-md-8">
                      <?php echo "{$datos[0]['cantidaddelitos']}";?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                      Observaciones:  
                    </div>
                    <div class="col-md-8">
                      <?php echo "{$datos[0]['observaciones']}";?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                      Cantidad de incidentes:  
                    </div>
                    <div class="col-md-8">
                      <?php echo "{$datos[0]['cantidaddelitos']}";?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                      Perfil general (historial):  
                    </div>
                    <div class="col-md-8">
                        <a class="btn btn-default" href="index.php?act=perfilGeneral&ci=<?php echo "{$datos[0]['cedula']}";?>">Perfil general PDF</a>
                    </div>
                </div>
                
                <?php 
                    
                    $delitos=explode('/',$datos[0]['delito']);    
                    $desc=explode("/",$datos[0]['descripcion']);
                    $fechaC=explode("/",$datos[0]['fechac']);
                    for($i=0;$i<count($delitos);$i++):
                
                ?>
                <div class="row">
                    <div class="col-md-4">
                      Descripción del delito:<br>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-12">
                          </br>
                                Tipo de delito: <?php echo $delitos[$i]; ?></br><br>
                                Descripción del delito:</br> <?php echo $desc[$i]; ?>

                                <a href="index.php?act=perfilPDF&ci=<?php echo $datos[0]['cedula'] ?>&desc=<?php echo $desc[$i]; ?>&delito=<?php echo $delitos[$i]; ?>&fc=<?php echo $fechaC[$i]; ?>" class="btn btn-default">PDF</a>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <?php endfor; ?>
            </div>
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
