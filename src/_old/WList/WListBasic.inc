<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 * WListBasic: muestra simplemente un listado con los datos de una consulta
 *
 */

class WListBasic extends EventComponent {
  private $sqlQ;
  private $options = array();
  private $rows;
  private $numRowsSelect = 0;
  private $numRowsUpdate = 0;

  //------------------------------------------------
  function __construct($id_object, $sqlQ='')
  {
    parent::__construct($id_object);

    //---------------
    $this->setSqlQuery($sqlQ);

    //---------------
    $this->parse_event($this->WEvent);
  }
  //--------------------------------------------------------------
  function parse_event($WEvent)
  {
    switch($WEvent->EVENT) {
      //----------
      default:

      break;
      //----------
    }
  }
  //--------------------------------------------------------------
  // PUBLIC
  //-------------------------------------------------------
  public function setSqlQuery($sqlQ) {
    if (!$sqlQ) {
       return;
    }
    $this->sqlQ = $sqlQ;

    $this->rows = Db_mysql::getListNoId($this->sqlQ);
    $this->numRowsSelect = count($this->rows);
    $this->numRowsUpdate = Db_mysql::affected_rows();
  }
  //-------------------------------------------------------
  protected function get_sql() {
    return $this->sqlQ;
  }
  //-------------------------------------------------------
  public function setArrayData($rows) {
    $this->rows = $rows;
    $this->numRowsSelect = count($this->rows);
    $this->numRowsUpdate = $this->numRowsSelect;
  }
  //-------------------------------------------------------
  public function setOptions($options=array()) {
    $this->options = $options;
  }
  //-------------------------------------------------------
  public function getHtm() {
    $rowTitulos = '';
    $rowsDatos  = '';

    if($this->numRowsSelect > 0) {
       $rowTitulos = $this->getHtmRowTitles();
       $rowsDatos  = $this->getHtmRowsValues();
       $numRows = $this->numRowsSelect;
    }
    else {
       $numRows = $this->numRowsUpdate;
    }

    return <<<EOD
  <table class="WListBasic" cellpadding="2" cellspacing="0">
    $rowTitulos
    $rowsDatos
    <tr><td class="field_footer" colspan="20" align="center">Results: <b>$numRows</b></td></tr>
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
    $row = current($this->rows);

    // 'SHOW TABLE' ---
    if( $this->sqlQ == 'SHOW TABLE STATUS') {
      $row = array(
          'Name' => '',
          'Rows' => '',
          // 'Collation' => '',
        );
    }

    foreach($row as $dbField => $value) {
       $htmTitles .= '<td class="field_title">'. $dbField .'</td>';
    }

    if ($this->options) {
       $htmTitles .= '<td colspan="2">&nbsp;</td>';
    }

    return "<tr> $htmTitles </tr>\n";
  }
  //-------------------------------------------------------
  private function getHtmRowsValues() {
    $htmList = '';

    foreach($this->rows as $key => $row) {
       $first_field = each($row);
       $id = $first_field['value'];

       // 'SHOW TABLE' ---
       if ( $this->sqlQ == 'SHOW TABLE STATUS') {
         $row = array(
             'Name' => $row['Name'],
             'Rows' => $row['Rows'],
             // 'Collation' => $row['Collation'],
           );
       }

       /* columnas */
       $strCols = '';
       foreach($row as $dbField => $value) {
          $strCols .= '<td class="tupla">'.$row[$dbField].'</td>';
       }
       if($strCols == '') {
          continue;
       }

       /* options */
       foreach($this->options as $event => $label) {
          $option = 'CONTROL='.$this->id_object.'&EVENT='.$event.'&ROW_ID='.$first_field['value'];
          $strCols .= '<td class="tupla_op"><a href="./?'.$option.'">'.$label.'</a></td>';
       }

       /* Selected */
       $classSelected = '';
       $ROW_ID = $this->wObjectStatus->getRowId();
       if ($id == $ROW_ID) {
          $classSelected = 'class="selected"';
       }

       /* Tupla */
       $htmList .= "<tr $classSelected>$strCols</tr>";
    }

    return $htmList;
  }
  //-------------------------------------------------------
}
?>
