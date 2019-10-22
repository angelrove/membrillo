<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\Login;

use angelrove\utils\Db_mysql;
use App\Models\User;

class LoginCtrl
{
    private static $iLoginQuery;

    //----------------------------------------------------------
    public static function init_ajax()
    {
        self::init(true);
    }
    //----------------------------------------------------------
    public static function init(bool $isAjax = false)
    {
        global $CONFIG_APP;

        // No login
        if (!$CONFIG_APP['login']['LOGIN']) {
            return true;
        }

        // Logged
        Login::init();
        if (Login::$user_id) {
            return true;
        }

        // Set login
        if ($isAjax == true) {
            die('Restricted area.');
        } else {
            self::initPage();
        }
    }
    //------------------------------------------------
    public static function set_iLoginQuery(LoginQueryInterface $iLoginQuery)
    {
        self::$iLoginQuery = $iLoginQuery;
    }
    //------------------------------------------------
    public static function initPage()
    {
        global $CONFIG_APP;

        // Authenticate --------------
        $userData = array();

        // Login -----------
        if ((isset($_REQUEST['LOGIN_USER']) && $_REQUEST['LOGIN_USER']) ||
            isset($_REQUEST['auth_token']) ) {
            // User data
            $userData = array();
            if (isset(self::$iLoginQuery)) {
                $userData = self::$iLoginQuery->get($_REQUEST['LOGIN_USER'], $_REQUEST['LOGIN_PASSWD'], $_REQUEST);
            } else {
                if ($CONFIG_APP['login']['LOGIN_HASH']) {
                    $userData = User::loginHash($_REQUEST['LOGIN_USER'], $_REQUEST['LOGIN_PASSWD']);
                } else {
                    $userData = User::login($_REQUEST['LOGIN_USER'], $_REQUEST['LOGIN_PASSWD']);
                }
            }

            // Login ok
            if (isset($userData['id'])) {
                new Login($userData['id'], $userData['email'], $userData);
                header("Location: /?APP_EVENT=timezone");
                exit();
            }
        }

        // Authenticate view -------
        if (!Login::$user_id) {
            $msg = (isset($_REQUEST['LOGIN_USER']))? 'Username or password is incorrect' : '';

            if ($CONFIG_APP['login']['LOGIN_VIEW']) {
                include $CONFIG_APP['login']['LOGIN_VIEW'];
            } else {
                include 'tmpl_form.php';
            }

            exit();
        }
    }
    //-----------------------------------------------------------------
}
