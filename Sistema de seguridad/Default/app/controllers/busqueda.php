<?php
  /**
   *
   */
  class Busqueda extends Controlador{

    //esta función toma un filtro seleccionado por el usuario y lo prepara para incertarlo en la sentencia SQL
    public function prepararFiltro($filtros){
      $salidaSql='';
      foreach ($filtros as $key => $value) {
        switch ($key) {
          case 'fecha':
              switch ($value) {
                case '24h':
                  $salidaSql.=" AND (now()::date - interval '1 day') <= Fich.fecha_creacion";
                  break;
                case 'us':
                  $salidaSql.=" AND (now()::date - interval '7 day') <= Fich.fecha_creacion";
                  break;
                case 'um':
                  $salidaSql.=" AND (now()::date - interval '1 month') <= Fich.fecha_creacion";
                  break;
                case 'ua':
                  $salidaSql.=" AND (now()::date - interval '1 year') <= Fich.fecha_creacion";
                  break;
                default:
                  $aux=explode(';',$value);
                  if(!(count($aux)>=2))
                    break;
                  $salidaSql.=" AND ((Fich.fecha_creacion>='{$aux[0]}') AND (Fich.fecha_creacion<='{$aux[1]}'))";
                  break;
              }
              break;
          case 'tipo_delito':
            $delitos=parent::$modelo->getDelitos();
            foreach ($delitos as $valor) {
              if($valor[0]==$value)
                $salidaSql.=" AND Deli.id_delito={$valor[0]}";
            }
            break;
        }
      }
      return $salidaSql;
    }
    public function busquedaCompuesta($dato,$filtros,$tipo){
      $filtros=$this->prepararFiltro($filtros);
      $res=($tipo==1)?parent::$modelo->buscarDet($dato,$filtros):parent::$modelo->buscarNotif($dato,$filtros);
      if($res){
          $salida=($tipo==1)?$this->convertHTML($res,'busquedaCompuestaCauc'):$this->convertHTML($res,'busquedaCompuestaNot');
      }else{
        if($tipo==1)
          $salida="<tr>
            <td colspan='8' style='text-align:center;'>
              Detenido no registrado
            </td>
          </tr>";
        else
          $salida="<tr>
            <td colspan='6' style='text-align:center;'>
              Detenido no registrado
            </td>
          </tr>";
      }
      return $salida;
    }
    
    
    public function busquedaSimple($dato){
      //1 para cedula 2 para nombre
      $res=parent::$modelo->BQCaucionS($dato);
      if($res){
          $salida=$this->convertHTML($res,'busquedaSimple');
      }else{
        $salida="<tr>
          <td colspan='8' style='text-align:center;'>
            Detenido no registrado
          </td>
        </tr>";
      }
      return $salida;
    }
    
    
    public function listadoIndocumentados(){
      $res=parent::$modelo->indocumentados();
      if($res){
          $salida=$this->convertHTML($res,'listIndocumentados');
      }else{
        $salida="<tr>
          <td colspan='7' style='text-align:center;'>
            No hay detenidos indocumentados registrados en el sistema.
          </td>
        </tr>";
      }
      return $salida;
    }

    //Prepara los datos para ser mostrados en el documento HTML
    public function convertHTML($datos,$tipoBusqueda){
      $salida="";
      switch ($tipoBusqueda) {
        case 'busquedaSimple':
          foreach($datos as $linea){
            $aux=explode('/',$linea['dic_imagen']);
            $aux=VISUAL_IMG.array_pop($aux);
            $linea['doc']=($linea['cedula']<=0)?false:true;
            $linea['cedula']=($linea['cedula']<=0)?'INDOCUMENTADO':$linea['cedula'];
            $salida.="
            <tr>
                <td style='text-align:center; font-size:25px; padding-top:30px;'>
                  <a href='index.php?act=perfil&ci={$linea['cedula']}'><span class='glyphicon glyphicon-log-in' ></span></a>
                </td>
                <td>
                  <img src='$aux' style='width:100px; height:100px'>
                </td>
                <td>

                   {$linea['cedula']}

                </td>
                <td>
                  {$linea['p_nombre']}
                </td>
                <td>
                  {$linea['p_apellido']}
                </td>
                <td>
                  {$linea['desc_delito']}
                </td>
                <td>
                  {$linea['fecha_creacion']}
                </td>";
                if($linea['doc'])
                  $salida.="
                    <td style='color:green;'>
                      <i class='glyphicon glyphicon-ok'></i> &nbsp;Documentado
                    </td>
                  </tr>";
                else
                  $salida.="
                    <td style='color:red;'>
                      <i class='glyphicon glyphicon-remove'></i> &nbsp;Indocumentado
                    </td>
                  </tr>";
              }
            return $salida;

        case 'busquedaCompuestaCauc':
          foreach($datos as $linea){
            $aux=explode('/',$linea['dic_imagen']);
            $aux=VISUAL_IMG.array_pop($aux);
            $linea['doc']=($linea['cedula']<=0)?false:true;
            $linea['cedula']=($linea['cedula']<=0)?'INDOCUMENTADO':$linea['cedula'];
            $salida.="<tr>
                <td style='text-align:center; font-size:25px; padding-top:30px;'>
                  <a href='index.php?act=perfil&ci={$linea['cedula']}'><span class='glyphicon glyphicon-log-in' ></span></a>
                </td>
                <td>
                  <img src='$aux' style='width:100px; height:100px'>
                </td>
                <td>
                   {$linea['cedula']}
                </td>
                <td>
                  {$linea['p_nombre']} {$linea['s_nombre']}
                </td>
                <td>
                  {$linea['p_apellido']} {$linea['s_apellido']}
                </td>
                <td>
                  {$linea['desc_delito']}
                </td>
                <td>
                  {$linea['fecha_creacion']}
                </td>";
                if($linea['doc'])
                  $salida.="
                    <td style='color:green;'>
                      <i class='glyphicon glyphicon-ok'></i> &nbsp;Documentado
                    </td>
                  </tr>";
                else
                  $salida.="
                    <td style='color:red;'>
                      <i class='glyphicon glyphicon-remove'></i> &nbsp;Indocumentado
                    </td>
                  </tr>";
              }
            return $salida;

        case 'listIndocumentados':
          foreach($datos as $linea){
            $aux=explode('/',$linea['dic_imagen']);
            $aux=VISUAL_IMG.array_pop($aux);

            $salida.="<tr>
                <td style='text-align:center; font-size:25px; padding-top:30px;'>
                  <a href='index.php?act=perfil&ci={$linea['cedula']}'><span class='glyphicon glyphicon-log-in' ></span></a>
                </td>
                <td>
                  <img src='$aux' style='width:100px; height:100px'>
                </td>

                <td>
                  {$linea['p_nombre']}
                </td>
                <td>
                  {$linea['p_apellido']}
                </td>
                <td>
                  {$linea['desc_delito']}
                </td>
                <td>
                  {$linea['fecha_creacion']}
                </td>";
          }
          return $salida."</tr>";
        case 'busquedaCompuestaNot':
          foreach($datos as $linea){
            $salida.="<tr>
                <td style='text-align:center; font-size:25px; padding-top:30px;'>
                  <a href='index.php?act=perfil&ci={$linea['cedula']}&not=1'><span class='glyphicon glyphicon-log-in' ></span></a>
                </td>
                <td>
                   {$linea['cedula']}
                </td>
                <td>
                  {$linea['p_nombre']} {$linea['s_nombre']}
                </td>
                <td>
                  {$linea['p_apellido']} {$linea['s_apellido']}
                </td>
                <td>
                  {$linea['desc_delito']}
                </td>
                <td>
                  {$linea['fecha_creacion']}
                </td>
                </tr>";

              }
            return $salida;
      }
    }
  }

?>
