<?php
/**
 *
 * José A. Romero Vegas, 2006
 * jangel.romero@gmail.com
 */

//------------------------------------------------------------------
function WPanel($align='center') {
  echo <<<EOD
  <table class="panel" border="0" cellspacing="0" cellpadding="0" align="$align"><tr><td align="$align" class="panel_contenido">
EOD;
}

function WPanel_END() {
  echo <<<EOD
  </td></tr></table><br>
EOD;
}
//------------------------------------------------------------------
?>
