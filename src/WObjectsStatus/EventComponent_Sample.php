<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 */

namespace angelrove\membrillo\WObjectsStatus;


class _SampleComponent extends EventComponent {

  //------------------------------------------------
  public function __construct($id_object)
  {
    parent::__construct($id_object);

    //---------------
    $this->parse_event($this->WEvent);
  }
  //--------------------------------------------------------------
  public function parse_event($WEvent)
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
  public function getHtm() {


    return <<<EOD
  <div class="_SampleComponent">
    xxx
  </div>
EOD;
  }
  //--------------------------------------------------------------
  // PRIVATE
  //-------------------------------------------------------

}
