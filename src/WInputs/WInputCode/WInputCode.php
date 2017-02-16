<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WInputs\WInputCode;

use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;


class WInputCode
{
  private $name = '';
  private $theme = '';
  private $func_on_change = false;

  //---------------------------------------------------------------------
  public function __construct($name, $type='', $theme='ambiance')
  {
    // Codemirror lib ---
    include_once('_vendor.php');
    Vendor::usef('codemirror');

    //------------------
    $this->name  = $name;
    $this->theme = $theme;

    //------------------
    CssJsLoad::set(__DIR__.'/styles.css');
    CssJsLoad::set(__DIR__.'/libs.js');
  }
  //---------------------------------------------------------------------
  public function set_function_on_change()
  {
     $this->func_on_change = true;
  }
  //---------------------------------------------------------------------
  public function show($value)
  {
    if($this->func_on_change) {
       $str_on_change = "WInputCode_change('$this->name');";
    }

    CssJsLoad::set_script("
var theme = '$this->theme';

$(document).ready(function() {

  editores['$this->name'] = CodeMirror.fromTextArea($('.codemirror-textarea_$this->name')[0], {
    selectionPointer: true,
    lineNumbers: true,
    styleActiveLine: true,
    matchBrackets: true,
    autorefresh:true,
    extraKeys: {'Ctrl-Space': 'autocomplete'},
  });
  editores['$this->name'].setOption('theme', theme);

  // on change --------------
  editores['$this->name'].on('change', function(e) {
    $str_on_change
  });

});
");

    return <<<EOD

<!-- WInputCode -->
<style>
.CodeMirror {
   height: 80vh;
   border: 1px solid black;
}
.CodeMirror span {
  font-size: 13px; font-family:Consolas;
}
.cm-s-zenburn {
  background-color: #222;
}
.cm-s-zenburn .CodeMirror-gutters {
  background: #323131 !important;
}
</style>

<div style="width: 100%;">
<textarea id="$this->name" class="codemirror-textarea_$this->name" name="$this->name">$value</textarea>
</div>
<!-- /WInputCode -->

EOD;

  }
  //---------------------------------------------------------------------
}
