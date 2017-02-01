<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo2\Login;


interface LoginQueryInterface
{
   public function get($user, $passwd);
}
