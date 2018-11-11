<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<table  style="width:850px;height:850px">
			<tr>
				<td style="width:100px;height:250px">
					<?php $ver=WEB_PATH;echo "<img src='{$ver}img/escudo.png' style='width:100px'>"; ?>
				</td>
				<td style="text-align:center;">
					REPÚBLICA BOLIVARIANA DE VENEZUELA<br>
					MINISTERIO DEL PODER POPULAR PARA LA DEFENSA <br>
					VICEMINISTERIO DE SERVICIOS <br>
          INSTITUTO DE PREVISIÓN SOCIAL <br>
          DE LAS <br>
          FUERZAS ARMADAS <br>
          GERENCIA DE SEGURIDAD INTEGRAL <br><br>
          <h3>ACTA DE CAUCIÓN</h3>
				</td>
        <td style="width:100px;height:250px">
					<?php echo "<img src='{$datos['dir_img']}' style='width:100px;height:100px'>"; ?>
        </td>
			</tr>
      <tr>
        <td colspan="3" style="text-align:right">
          Caracas, <?php if(!isset($datos['fc']))echo date("d")." del mes ".date("m")." año ".date("Y");else echo $datos['fc']; ?>
          <br><br>
        </td>
      </tr>
      <tr>
        <td colspan="3" >
					<br><br>
          &nbsp;&nbsp;&nbsp;&nbsp;Por medio de la presente yo, <U><?php echo strtoupper($datos['primer_nombre']." ".$datos['segundo_nombre']." ".$datos['primer_apellido']." ".$datos['segundo_apellido']); ?></u>, de nacionalidad <U>
					<?php if(strcmp(strtoupper($datos['nacionalidad']),'V')==0){echo "VENEZOLANO/A";}else{ echo "EXTRANJERO/A";} ?></u>
          , nacido el día <U><?php echo $datos['fecha']; ?></u>, de estado civil <U><?php echo $datos['estado_civil']."/A"; ?></u>, titular de la cédula de identidad N° <U><?php if($datos['cedula']>0){echo $datos['cedula'];}else{echo "--INDOCUMENTADO--";} ?></u>
          residenciado(a) en <U><?php if(strcmp(trim($datos['res_estado']),'')!=0){echo "el estado ".$datos['res_estado']." municipio ".$datos['res_municipio']." parroquia ".$datos['res_parroquia']." lugar ".strtoupper($datos['res_lugar']);}else{echo "-- SIN DIRECCIÓN --";} ?></u> de profesión u oficio <U><?php if($datos['profesion']!=null && strcmp(trim($datos['profesion']),'')!=0){echo $datos['profesion'];}else{echo "--SIN PROFESIÓN--";} ?>
					</u>, laborando en <U><?php if($datos['tra_estado']!=null && strcmp(trim($datos['tra_estado']),'')!=0){echo "el estado ".$datos['tra_estado']." municipio ".$datos['tra_municipio']." parroquia ".$datos['tra_parroquia']." dirección ". strtoupper($datos['tra_lugar']);}else{echo "-- SIN DIRECCIÓN --";} ?></u>,
          teléfono N° <U><?php if($datos['num_telefono']!=null && strcmp(trim($datos['num_telefono']),'')!=0){echo $datos['num_telefono'];}else{echo "--SIN NÚMERO DE TELEFONO--";} ?></u>. <br><br><br>
          &nbsp;&nbsp;&nbsp;&nbsp;Manifiesto libre de toda capción y apremio, que el Personal Militar y los Funcionarios de Seguridad en ningún momento me han causado maltrato físico, me han quitado dinero,
          ni he recibido ofensas de tipo verbal o cualquier otra acción que dañara mi integridad personal y moral, manifestación esta que hago por voluntad propia en mi condición de
          persona adulta y en consecuencia firmo y estampo mis huellas dactilares. De igual manera me comprometo a cumplir las normativas internas que lleva esta institución.
          <br><br>
          <u><b>OBSERVACIONES:</b></u>
          <br>
          <?php echo $datos['descripcion']; ?>
          <br>
        </td>
      </tr>
      <tr>
        <td colspan="3" style="text-align:center">
					<br><br>
          <b>EL CAUCIONADO</b>
        </td>
      </tr>
      <tr>
        <td colspan="3" style="text-align:center">
          <br>
          <u>P.I.</u>&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;   &nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;<u>P.D.</u>
          <br><br><br><br>
        </td>
      </tr>
		</table>
	</body>
</html>
