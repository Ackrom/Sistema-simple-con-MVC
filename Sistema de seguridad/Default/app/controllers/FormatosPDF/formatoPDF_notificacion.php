<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<table  style="width:850px;height:850px" >
			<tr>
				<td style="width:100px;height:250px">
					<?php $ver=WEB_PATH;echo "<img src='{$ver}img/escudo.png' style='width:100px'>" ?>
				</td>
				<td style="text-align:center;">
					REPÚBLICA BOLIVARIANA DE VENEZUELA<br>
					MINISTERIO DEL PODER POPULAR PARA LA DEFENSA <br>
					VICEMINISTERIO DE SERVICIOS <br>
          INSTITUTO DE PREVENCIÓN SOCIAL <br>
          DE LA <br>
          FUERZA ARMADA <br>
          GERENCIA DE SEGURIDAD INTEGRAL <br><br>
          <h3>ACTA DE NOTIFICACIÓN</h3>
				</td>
        <td style="width:100px;height:250px">

        </td>
			</tr>
      <tr>
        <td colspan="3" style="text-align:right">
          Caracas, <?php echo date("d")." del mes ".date("m")." año ".date("Y"); ?>
          <br><br>
        </td>
      </tr>
      <tr>
        <td colspan="3" >
          &nbsp;&nbsp;&nbsp;&nbsp;Por medio de la presente yo, <U><?php echo strtoupper($datos['primer_nombre']." ".$datos['segundo_nombre']." ".$datos['primer_apellido']." ".$datos['segundo_apellido']); ?></u>, titular de la
					cédula de identidad N° <?php echo $datos['cedula']; ?>, nacido el día <?php echo $datos['fecha']; ?>, de estado civil <?php echo $datos['estado_civil']; ?>, residenciado(a) en <U><?php echo "el estado ".$datos['res_estado']." municipio ".$datos['res_municipio']." parroquia ".$datos['res_parroquia']." dirección ".$datos['res_lugar']; ?></u>
					de profesión u oficio &nbsp;<?php echo $datos['profesion']; ?>, laborando en <U><?php if($datos['tra_estado']!=null && strcmp(trim($datos['tra_estado']),'')!=0){echo "el estado ".$datos['tra_estado']." municipio ".$datos['tra_municipio']." parroquia ".$datos['tra_parroquia']." dirección ". strtoupper($datos['tra_lugar']);}else{echo "-- SIN DIRECCIÓN --";} ?></u>, laborando en <U><?php echo "el estado ".$datos['tra_estado']." municipio ".$datos['tra_municipio']." parroquia ".$datos['tra_parroquia']." dirección ".$datos['tra_lugar']; ?></u>, teléfono &nbsp;
					<?php if($datos['num_telefono']!=null && strcmp(trim($datos['num_telefono']),'')!=0){echo $datos['num_telefono'];}else{echo "--SIN NÚMERO DE TELEFONO--";} ?>&nbsp; acudo a esta dependencia para exponer lo siguiente: <br>
					<?php echo $datos['descripcion']; ?>
          <br><br>
        </td>
      </tr>

      <tr>
        <td >
					<br><br><br>
					<b>EL FUNCIONARIO ACTUANTE</b>
        </td>
				<td style="text-align:right">
					<br><br><br>
					<b>
						EL/LA EXPONENTE <br>
						C.I. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
						P.I.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;P.D.
					</b>
				</td>

				<td >

				</td>
      </tr>
		</table>
	</body>
</html>
