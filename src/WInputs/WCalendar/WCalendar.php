<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WInputs\WCalendar;

use angelrove\utils\CssJsLoad;
use angelrove\membrillo\CrudUrl;
use angelrove\membrillo\WApp\Local;


class WCalendar
{
  private $LIST_MESES = array(
            '1' =>'enero',
            '2' =>'febrero',
            '3' =>'marzo',
            '4' =>'abril',
            '5' =>'mayo',
            '6' =>'junio',
            '7' =>'julio',
            '8' =>'agosto',
            '9' =>'septiembre',
            '10'=>'octubre',
            '11'=>'noviembre',
            '12'=>'diciembre'
          );

  private $control;
  private $calendars;

  private $showBtNew    = false;
  private $showBtNew_lb = '';

  //------------------------------------------------------------------
  /* calendars[]: ->nombre, ->class, ->style, ->id_rango */
  function __construct($control, $calendars='')
  {
    $this->control   = $control;
    $this->calendars = $calendars;

    CssJsLoad::set(__DIR__.'/styles.css');
    CssJsLoad::set(__DIR__.'/libs.js');
  }
  //-------------------------------------------------------
  public function showBtNew($title='') {
    $this->showBtNew    = true;
    $this->showBtNew_lb = $title;
  }
  //-------------------------------------------------------
  public function setDayEditor($dayEditor) {
    $this->dayEditor = $dayEditor;
  }
  //------------------------------------------------------------------
  public function getObjMes($PARAM_MES, $PARAM_ANHO) {
    return '
    <script>
    var control   = "'.$this->control.'";
    var showBtNew = "'.$this->showBtNew.'";
    </script>
    '.
    $this->getMes($PARAM_MES, $PARAM_ANHO);
  }
  //------------------------------------------------------------------
  public function getObjYear($year='', $show_prev_next=true)
  {
    if(!$year) {
       $hoy = getdate();
       $year = $hoy['year'];
    }

    $htm_calendar = '';
    $cols = 1;
    for($c=1; $c<=12; $c++) {
       $htm_calendar .= $this->getMes($c, $year);
       if($cols++ == 4) {
          $htm_calendar .= '<div style="clear:both"></div>';
          $cols = 1;
       }
    }

    // Prev / Next
    $href = CrudUrl::get('xxx', $this->control, '', '', 'f_year='.($year-1));
    $objPrevYear = '<a href="'.$href.'">&laquo;</a>';

    $href = CrudUrl::get('xxx', $this->control, '', '', 'f_year='.($year+1));
    $objNextYear = '<a href="'.$href.'">&raquo;</a>';

    $btNew = '';
    if($this->showBtNew_lb) {
       $href = CrudUrl::get(CRUD_EDIT_NEW, $this->control);
       $btNew = '<div class="btNuevo"><a href="'.$href.'">'.$this->showBtNew_lb.'...</a></div>';
    }

    $tool_prev_next = '';
    if($show_prev_next) {
       $tool_prev_next = '<div class="year font-size-big">'.$objPrevYear.' &nbsp; '.$year.' &nbsp; '.$objNextYear.'</div>';
    }

    return <<<EOD
     <script>
     var control   = "$this->control";
     var showBtNew = "$this->showBtNew";
     </script>

     $btNew
     <div id="WCalendar">
       $tool_prev_next
       $htm_calendar
       <div style="clear:both">&nbsp;</div>
     </div>
EOD;
  }
  //------------------------------------------------------------------
  // PRIVATE
  //------------------------------------------------------------------
  private function ultimoDia($a, $m) {
    if(((fmod($a,4) == 0) and (fmod($a,100) != 0)) or (fmod($a,400) == 0)) {
       $dias_febrero = 29;
    } else {
       $dias_febrero = 28;
    }

    switch($m) {
      case  1: $valor = 31; break;
      case  2: $valor = $dias_febrero; break;
      case  3: $valor = 31; break;
      case  4: $valor = 30; break;
      case  5: $valor = 31; break;
      case  6: $valor = 30; break;
      case  7: $valor = 31; break;
      case  8: $valor = 31; break;
      case  9: $valor = 30; break;
      case 10: $valor = 31; break;
      case 11: $valor = 30; break;
      case 12: $valor = 31; break;
    }
    return $valor;
  }
  //------------------------------------------------------------------
  private function numero_dia_semana($d,$m,$a)
  {
    $f = getdate(mktime(0,0,0,$m,$d,$a));
    $d = $f['wday'];
    if($d == 0) $d = 7;
    return $d;
  }
  //------------------------------------------------------------------
  private function nombre_dia_semana($d,$m,$a)
  {
    $f = getdate(mktime(0,0,0,$m,$d,$a));

    if (Local::getLang() == 'es') {
      switch($f['wday']) {
        case 1: $valor = 'lunes';     break;
        case 2: $valor = 'martes';    break;
        case 3: $valor = 'miércoles'; break;
        case 4: $valor = 'jueves';    break;
        case 5: $valor = 'viernes';   break;
        case 6: $valor = 'sábado';    break;
        case 0: $valor = 'domingo';   break;
      }
    }
    else {
      switch($f['wday']) {
        case 1: $valor = 'Monday';    break;
        case 2: $valor = 'Tuesday';   break;
        case 3: $valor = 'Wednesday'; break;
        case 4: $valor = 'Thursday';  break;
        case 5: $valor = 'Friday';    break;
        case 6: $valor = 'Saturday';  break;
        case 0: $valor = 'Sunday';    break;
      }
    }

    return $valor;
  }
  //------------------------------------------------------------------
  private function isHoy($anho, $mes, $dia) {
   $hoy = getdate();
   /*if( ($anho * 10000 + $mes * 100 + $dia) == ($hoy['year'] * 10000 + $hoy['mon'] * 100 + $hoy['mday']) ) {
     return true;
   }*/

   if($anho == $hoy['year'] && $mes == $hoy['mon'] && $dia == $hoy['mday']) {
      return true;
   }

   return false;
  }
  //------------------------------------------------------------------
  /* ¿Es día de otro mes? */
  private function isOtroMes($numDiaSem, $diasem, $dia, $dias_mes) {
   if(($numDiaSem == $diasem) && ($dia <= $dias_mes)) {
      return false;
   }
   //echo "<br>if(($numDiaSem == $diasem) && ($dia <= $dias_mes))";
   return true;
  }
  //------------------------------------------------------------------
  /* ¿Es día pasado? */
  /*function isPasado($anho, $mes, $dia) {
   global $hoy;

   if(($anho < $hoy['year']) || ($anho <= $hoy['year'] && $mes < $hoy['mon']) || ($mes == $hoy['mon'] && $dia < $hoy['mday'])) {
     return true;
   }
   return false;
  }*/
  //------------------------------------------------------------------
  /*function isManana($anho, $mes, $dia) {
   global $manana;

   if(($anho * 10000 + $mes * 100 + $dia) == ($manana['year'] * 10000 + $manana['mon'] * 100 + $manana['mday'])) {
     return true;
   }
   return false;
  }*/
  //------------------------------------------------------------------
  /*function getManana() {
    $hoy = getdate();
    return getdate(mktime(0, 0, 0, $hoy['mon'], $hoy['mday']+1, $hoy['year']));
  }*/
  //------------------------------------------------------------------
  // OUT
  //------------------------------------------------------------------
  private function getMes($PARAM_MES, $PARAM_ANHO) {
    $dia = 1;
    $dias_mes = $this->ultimoDia($PARAM_ANHO, $PARAM_MES);

    // dias mes prev ---
    /*$year  = ($PARAM_MES == 1)? $PARAM_ANHO - 1 : $PARAM_ANHO;
    $month = ($PARAM_MES == 1)? 12 : $PARAM_MES - 1;
    $dias_mes_prev = $this->ultimoDia($year, $month);*/
    //------------------

    $numDiaSemana = $this->numero_dia_semana($dia, $PARAM_MES, $PARAM_ANHO);
    //$numeroSemanas = ceil(($dias_mes + ($numDiaSemana - 1)) / 7);

    $htm_mes = '';
    for($semana=1; $semana <= 6; $semana++) { // semanas: max. de 6 semanas por mes
       $strLinea = '';

       for($diasem=1; $diasem <= 7; $diasem++) { // dias
          $numDiaSemana = $this->numero_dia_semana($dia, $PARAM_MES, $PARAM_ANHO);

          $isHoy     = $this->isHoy($PARAM_ANHO, $PARAM_MES, $dia);
          $isOtroMes = $this->isOtroMes($numDiaSemana, $diasem, $dia, $dias_mes);
          $is_weekend = ($diasem == 6 || $diasem == 7)? true : false;

          // Estilos comunes ----
          $styleDia = '';
          if($isHoy)      $styleDia = 'dia_hoy';
          if($is_weekend) $styleDia = 'dia_weekend';
          if($isOtroMes)  $styleDia = 'dia_otroMes';

          // Contenido ----------
          $contenido  = '';
          $str_rango  = '';
          $properties = array('toolTip'=> '',
                              'class'  => '',
                              'style'  => '');
          if($isOtroMes) {
             $contenido = '&nbsp;';
             if($semana > 1) {
                if($dia > 20) $dia = 0;
                $contenido = ++$dia;
             }
          }
          else {
             $contenido = $dia;
             $time = strtotime("$PARAM_ANHO-$PARAM_MES-$dia");

             // Edit user -------
             if(isset($this->dayEditor)) {
                $contenido  = $this->dayEditor->getValue($numDiaSemana, $diasem, $dias_mes, $PARAM_ANHO, $PARAM_MES, $dia);
                $properties = $this->dayEditor->getProperties($time, $PARAM_ANHO, $PARAM_MES, $dia);
             }
             //------------------
             elseif($this->calendars) {
                foreach($this->calendars as $calendar) {
                   if($calendar[$time]) {
                      $properties['id_rango']= $calendar[$time]->id_rango;
                      $properties['tooltip'] = $calendar[$time]->nombre;
                      $properties['class']   = $calendar[$time]->class;
                      $properties['style']   = $calendar[$time]->style;
                   }
                }
             }
             //------------------

             if($properties['tooltip']) {
                $properties['tooltip'] = "<span>$properties[tooltip]</span>";
             }
             if($properties['style']) {
                $properties['style'] = 'style="'.$properties['style'].'"';
             }
             if($properties['id_rango']) {
                if(is_array($properties['id_rango'])) {
                   foreach($properties['id_rango'] as $id_rango) {
                      $str_rango .= 'rango_'.$id_rango.' ';
                   }
                }
                else {
                   $str_rango = 'rango_'.$properties['id_rango'];
                }
             }

             $dia++;
          }

          // Ret ----------------
          $strLinea .= <<<EOD
<td><div day="$time" class="dia $styleDia $properties[class] $str_rango" $properties[style]>$contenido $properties[tooltip]</div></td>
EOD;
       }

       $htm_mes .= '<tr class="dias">'.$strLinea.'</tr>';
    }

    // OUT ---
    $htm_mes = '
      <table class="objMes" cellspacing="0" cellpadding="0" style="float:left">
        <tr><td class="bar_mesActual" colspan="7" align="center">
          <span><b>'.$this->LIST_MESES[$PARAM_MES].'<b></span>
        </td></tr>
        <tr class="dia_semana">
          <td>lu</td><td>ma</td><td>mi</td><td>ju</td><td>vi</td><td>sa</td><td>do</td>
        </tr>
        '.$htm_mes.'
      </table>
      ';

    return $htm_mes;
  }
  //------------------------------------------------------------------

}
