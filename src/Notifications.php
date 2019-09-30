<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo;

use angelrove\utils\CssJsload;

class Notifications
{
    //----------------------------------------------------
    public static function _init()
    {
        CssJsLoad::set_script('
  $(document).ready(function()
  {
     setInterval(getNotifications, 10000);

     function getNotifications() {
         $.ajax({
             data: {
                "sys_service" : "Notifications_get"
             },
             url:  "/index_ajax.php",
             success: function (response) {
                // notificationsCall(response)
             }
         });
     }
  });
', 'notifications');
    }
    //----------------------------------------------------
    // !!>> Guardar en BBDD
    public static function set($notification)
    {
        $_SESSION['notifications'][] = $notification;
    }
    //----------------------------------------------------
    // Ajax
    public static function get()
    {
        self::parseSession();

        // Get messages ---
        foreach ($_SESSION['notifications'] as $notification) {
            echo $notification;
        }

        // Empty ---
        $_SESSION['notifications'] = array();
    }
    //----------------------------------------------------
    private static function parseSession()
    {
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = array();
        }
    }
    //----------------------------------------------------
}
