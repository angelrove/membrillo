<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WObjects\WList;

use angelrove\utils\Db_mysql;
use angelrove\utils\CssJsLoad;


class WListBasic
{
  private $sqlQ;

  private $rows;
  private $numRows = 0;

  //--------------------------------------------------------------
  // PUBLIC
  //-------------------------------------------------------
  function __construct($sqlQ, $withInput=false)
  {
    //---------------
    CssJsLoad::set(__DIR__.'/styles.css');

    //---------------
    $this->withInput = $withInput;

    $this->sqlQ = $sqlQ;
    if($this->withInput) {
       $this->sqlQ = stripslashes($_POST['WLBasic_sqlQuery']);
    }

    /** Query **/
    if($this->sqlQ) {
       $this->rows = Db_mysql::getListNoId($this->sqlQ);

       $this->numRowsSelect = count($this->rows);
       $this->numRowsUpdate = Db_mysql::affected_rows();
    }

    /** Save query **/
    if(isset($_POST['op'])) {
       if($_POST['op'] == 'saveQuery') {
         $key = count($_COOKIE['queries']);
         setcookie("queries[$key]", $this->sqlQ, time()+60*60*24*60);
         $_COOKIE['queries'][$key] = $this->sqlQ;
       }
       elseif($_POST['op'] == 'delQuery') {
         $key = count($_COOKIE['queries']) - 1;
         setcookie("queries[$key]", "", time() - 3600);
         unset($_COOKIE['queries'][$key]);
       }
    }
  }
  //-------------------------------------------------------
  public function setArrayData($rows) {
    $this->rows = $rows;
    $this->numRowsSelect = count($this->rows);
    $this->numRowsUpdate = $this->numRowsSelect;
  }
  //-------------------------------------------------------
  private function getFormQueries() {
    $consultas = '';
    foreach($_COOKIE['queries'] as $key=>$value) {
       $consultas .= stripslashes($value).'<br>';
    }

    $res = <<<EOD
<script>
$(document).ready(function() {
  $("#onSave").click(function() {
    $("#WListBasic_form #op").val('saveQuery');
    $("#WListBasic_form").submit();
  });
  $("#onDel").click(function() {
    $("#WListBasic_form #op").val('delQuery');
    $("#WListBasic_form").submit();
  });
});
</script>

 <b>consultas guardadas</b>
 <div style="border:1px solid #aaa;background:#ddd;padding:3px">
   $consultas
 </div>
 <input id="onSave" type="button" value="Guardar"> <input id="onDel" type="button" value="Borrar">
 <br> <br>

 <form id="WListBasic_form" name="f_consulta" action="./" method="post">
  <input type="hidden" id="op" name="op">
  <b>Consulta</b><br>
  <textarea class="font-monospace" name="WLBasic_sqlQuery" cols="100" rows="5">$this->sqlQ</textarea><br>
  <input type="submit" value=" Aceptar ">
 </form>
 <div style="clear:both"></div>
EOD;
    return $res;
  }
  //-------------------------------------------------------
  public function getHtm() {
    $rowTitulos = '';
    $rowsDatos  = '';
    $formQueries = ($this->withInput)? $this->getFormQueries() : '';

    if($this->numRowsSelect > 0) {
       $rowTitulos = $this->getHtmRowTitles();
       $rowsDatos  = $this->getHtmRowsValues();
       $numRows = $this->numRowsSelect;
    }
    else {
       $numRows = $this->numRowsUpdate;
    }

    $numRows = '<tr><td colspan="20" align="center">Resultados: <b>'.$numRows.'</b></td></tr>';

    return <<<EOD
  $formQueries
  <table class="WListBasic" cellpadding="2" cellspacing="0" border="0">
   $rowTitulos
   $rowsDatos
   $numRows
  </table>
EOD;
  }
  //--------------------------------------------------------------
  public function getRows() {
    return $this->rows;
  }
  //--------------------------------------------------------------
  // PRIVATE
  //--------------------------------------------------------------
  private function getHtmRowTitles() {
    $htmTitles = '';
    $unaFila = current($this->rows);
    foreach($unaFila as $dbField => $value) {
       $htmTitles .= '<td class="field_title">'. $dbField .'</td>';
    }

    return "<tr> $htmTitles </tr>\n";
  }
  //-------------------------------------------------------
  private function getHtmRowsValues() {
    $htmList = '';

    foreach($this->rows as $row) {
      /* Datos */
      $strCols = '';
      foreach($row as $dbField => $value) {
         $strCols .= '<td class="tupla font-monospace">'.$value.'</td>';
      }
      if($strCols == '') continue;

      /* Tupla */
      $htmList .= "<tr>$strCols</tr>";
    }

    return $htmList;
  }
  //-------------------------------------------------------
}
