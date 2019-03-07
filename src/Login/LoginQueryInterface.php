<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Login;


interface LoginQueryInterface
{
   public function get($user, $passwd, $params);
}
