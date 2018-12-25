<?php
/**
 * @author <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WInputs;

use angelrove\utils\CssJsLoad;

class WInputDate
{
    //---------------------------------------------------
    public static function get($name, $value, $disabled = '')
    {
        // $nameButton = 'btCalendar_'. $name;
        $idInput = 'WInputs_Date';

        // Inicialización en español para la extensión 'UI date picker'
        CssJsLoad::set_script("
jQuery(function($) {
  $.datepicker.regional['es'] = {
      closeText: 'Cerrar',
      prevText: '&#x3c;Ant',
      nextText: 'Sig&#x3e;',
      currentText: 'Hoy',
      monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
      monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Sept.','Octubre','Noviembre','Diciembre'],
      dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
      dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
      dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
      weekHeader: 'Sm',
      dateFormat: 'dd/mm/yy',
      firstDay: 1,
      isRTL: false,
      showMonthAfterYear: false,
      numberOfMonths: 1,
      yearSuffix: ''};
  $.datepicker.setDefaults($.datepicker.regional['es']);
});
", $idInput);

        CssJsLoad::set_script('
    $(function() {
      $("#' . $name . '").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear:  true
      });
    });
');

        // Retornar el código ---
        ob_start();
        ?>
    <input type="text"
           class="form-control <?=$idInput?>"
           id="<?=$name?>"
           name="<?=$name?>"
           value="<?=$value?>"
           maxlength="10" <?=$disabled?>>
    <?php
        return ob_get_clean();
    }
    //---------------------------------------------------
}
