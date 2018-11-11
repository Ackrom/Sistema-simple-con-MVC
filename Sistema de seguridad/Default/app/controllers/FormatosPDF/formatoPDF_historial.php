<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body> 
	    <h1>Historial del detenido</h1>
	    <br>
	 <table style="width:800px;">
	     <tr>
	         <td>
	             Imgen:
	         </td>
	         <td>
                  <img src='<?php echo $datos[0]['dic_imagen']; ?>' style='width:171px; height:180px'>
	         </td>
	     </tr>
         <tr>
             <td>Nombres:</td>
             <td><?php echo "{$datos[0]['p_nombre']} {$datos[0]['s_nombre']}"; ?></td>
         </tr>   
         <tr>
             <td>Apellidos:</td>
             <td><?php echo "{$datos[0]['p_apellido']} {$datos[0]['s_apellido']}";?></td>
         </tr>
         
         <tr>
             <td>Cedula de identidad:</td>
             <td><?php 
                    if($datos[0]['cedula']<0) 
                                echo "INDOCUMENTADO";
                            else
                                echo $datos[0]['cedula'];
                        ?>
             </td>
         </tr>
         <tr>
             <td>
                 Nacionalidad:
             </td>
             <td>
                <?php if(strcmp($datos[0]['nacionalidad'],'V')==0) echo "VENEZOLANO"; else echo "EXTRANJERO";?>
             </td>
         </tr>
         <tr>
             <td>
                Fecha nacimiento:
             </td>
             <td>
                 <?php
                        $aux=explode("-",$datos[0]['fecha_nacimiento']);
                        echo "{$aux[2]}-{$aux[1]}-{$aux[0]}";
                       ?>
             </td>
         </tr>
         <tr>
             <td>
                 Estado civil:
             </td>
             <td>
                <?php echo "{$datos[0]['estadocivil']}";?>
             </td>
         </tr>
         <tr>
             <td>
                Num. Teléfono:
             </td>
             <td>
                 <?php if($datos[0]['telefono']) echo $datos[0]['telefono']; else echo "--SIN NÚMERO--"; ?>
             </td>
         </tr>
         <tr>
             <td>
                Profesión:
             </td>
             <td>
                 <?php if($datos[0]['profesion'])echo $datos[0]['profesion']; else echo "--SIN PROFESIÓN--";?>
             </td>
         </tr>
         <tr>
             <td>
                 Dirección de recidencia:
             </td>
             <td>
                <?php echo "Estado: {$datos[0]['estadocasa']} <br> Municipio: {$datos[0]['municipiocasa']} <br> Parroquia: {$datos[0]['parroquiacasa']} <br> Lugar: {$datos[0]['lugarcasa']}"; ?>
             </td>
         </tr>
         <tr>
             <td>
                 Dirección de trabajo:
             </td>
             <td>
                <?php if(isset($datos[0]['estadot'])) echo "Estado: {$datos[0]['estadot']} <br> Municipio: {$datos[0]['municipiot']} <br> Parroquia: {$datos[0]['parroquiat']} <br> Lugar: {$datos[0]['lugart']}";else echo "--SIN DIRECCIÓN--"; ?>
             </td>
         </tr>
         <tr>
             <td>
                 Genero:
             </td>
             <td>
                                       <?php if(strcmp($datos[0]['sexo'],'M')==0) echo "MASCULINO"; else echo"FEMENINO";?>

             </td>
         </tr>
         <tr>
             <td>
                 Cantidad de incidentes:
             </td>
             <td>
                 <?php echo "{$datos[0]['cantidaddelitos']}";?>
             </td>
         </tr>
         <tr>
             <td>Incidentes:</td>
         </tr>
<?php 
    $delitos=explode('/',$datos[0]['delito']);    
    $desc=explode("/",$datos[0]['descripcion']);
    $fechaC=explode("/",$datos[0]['fechac']);
    for($i=0;$i<count($delitos);$i++):
?>
        <tr>
            <th>
                Delito # <?php echo $i+1;?>
            </th>
        </tr>
         <tr>
             <th>
                 Fecha:
             </th>
             <th>
                 <?php echo $fechaC[$i]; ?>
             </th>
         </tr>
         <tr>
             <th>
                 Tipo de delito:
             </th>
             <th>
                 <?php echo $delitos[$i];?>
             </th>
         </tr>
         <tr>
             <th>
                 Descripción del delito:
             </th>
             <th>
                 <?php echo $desc[$i];?>
             </th>
         </tr>
    <?php endfor;?>
	 </table>
	</body>
</html>