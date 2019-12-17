<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\Login;

use angelrove\utils\Db_mysql;
use App\Models\User;
use angelrove\utils\UtilsBasic;

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
    private static function loginUser($LOGIN_USER, $LOGIN_PASSWD, $params)
    {
        global $CONFIG_APP;

        $userData = array();

        if (isset(self::$iLoginQuery)) {
            $userData = self::$iLoginQuery->get($LOGIN_USER, $LOGIN_PASSWD, $params);
        } else {
            if ($CONFIG_APP['login']['LOGIN_HASH']) {
                $userData = User::loginHash($LOGIN_USER, $LOGIN_PASSWD);
            } else {
                $userData = User::login($LOGIN_USER, $LOGIN_PASSWD);
            }
        }

        // Login ok
        if (isset($userData['id'])) {
            new Login($userData['id'], $userData['email'], $userData);
            header("Location: /?APP_EVENT=timezone");
            exit();
        }
    }
    //------------------------------------------------
    private static function restorePass($LOGIN_USER)
    {
        global $CONFIG_APP;

        // New password
        $pass = UtilsBasic::randomPassword();
        $hash = $pass;
        if ($CONFIG_APP['login']['LOGIN_HASH']) {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
        }

        // Database update
        $user = \DB::table('users')->where('email', '=', $LOGIN_USER)->first();
        if (!$user) {
            self::view("Check your email");
        }

        \DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => $hash,
            ]);

        // Send mail with new password
        $from = 'noreply@'.$CONFIG_APP['data']['domain'];
        $mailto = $LOGIN_USER;
        $bcc = '';
        $subject = 'Restore password';
        $body = '
            Hello.
            Here you have your new pass: '.$pass.'

            <hr>
            Please dont reply to this email.
        ';

        UtilsBasic::sendEMail($from, $mailto, $bcc, $subject, $body);
        self::view("Check your email inbox");
    }
    //------------------------------------------------
    public static function initPage()
    {
        global $CONFIG_APP;

        // Authenticate ------------
        if ((isset($_REQUEST['restorepass']) && $_REQUEST['restorepass']) && (isset($_REQUEST['LOGIN_USER']) && $_REQUEST['LOGIN_USER'])) {
            self::restorePass($_REQUEST['LOGIN_USER']);
        } elseif ((isset($_REQUEST['LOGIN_USER']) && $_REQUEST['LOGIN_USER']) || isset($_REQUEST['auth_token'])) {
            self::loginUser($_REQUEST['LOGIN_USER'], $_REQUEST['LOGIN_PASSWD'], $_REQUEST);
        }

        // Authenticate view -------
        if (!Login::$user_id) {
            $msg = (isset($_REQUEST['LOGIN_USER']))? 'Username or password is incorrect' : '';
            self::view($msg);
        }
    }
    //------------------------------------------------
    private static function view($msg)
    {
        global $CONFIG_APP;

        if ($CONFIG_APP['login']['LOGIN_VIEW']) {
            include $CONFIG_APP['login']['LOGIN_VIEW'];
        } else {
            include 'tmpl_form.php';
        }

        exit();
    }
    //-----------------------------------------------------------------
}
