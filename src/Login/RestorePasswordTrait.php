<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\Login;

use angelrove\utils\UtilsBasic;
use Mailjet\Client;
use Mailjet\Resources;

trait RestorePasswordTrait
{
    //------------------------------------------------
    public static function restorePassword($loginEmail, $withHash)
    {
        // New password
        $pass = UtilsBasic::randomPassword();
        $hash = $pass;
        if ($withHash) {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
        }

        // Database update
        $user = \DB::table('users')->where('email', '=', $loginEmail)->first();
        if (!$user) {
            self::view("Check your email");
        }

        \DB::table('users')->where('id', $user->id)
            ->update(['password' => $hash]);

        // Send mail with new password
        // self::sendEmail(EMAIL_NOREPLY, $loginEmail, $pass);
        self::sendMailjet(EMAIL_NOREPLY, $loginEmail, $pass);

        // View
        self::view("Check your email inbox");
    }
    //------------------------------------------------
    private static function sendMailjet($from, $mailTo, $newPass)
    {
        global $CONFIG_APP;

        $apikey = MAILJET['apikey'];
        $apisecret = MAILJET['apisecret'];

        $message = "
            Hello.
            <br>
            Here is your new password:<br>

            <div style='font-family: monospace; padding:5px; background-color:#eee; border:1px solid #ddd; display:table;'>
                $newPass
            </div>

            <p></p>
            <hr>
            Please don't reply to this email.<br>
            ".$CONFIG_APP['data']['COMPANY_NAME']."
        ";

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $from,
                        'Name' => $CONFIG_APP['data']['COMPANY_NAME']
                    ],
                    'To' => [
                        [
                            'Email' => $mailTo,
                            // 'Name' => "León"
                        ]
                    ],
                    'Subject' => 'Restore password',
                    'HTMLPart' => $message
                ]
            ]
        ];

        $mailJet = new \Mailjet\Client($apikey, $apisecret, true, ['version' => 'v3.1']);
        $mailJet->post(Resources::$Email, ['body' => $body]);
    }
    //------------------------------------------------
    // private function sendEmail($from, $mailTo, $newPass)
    // {
    //     $bcc = '';
    //     $subject = 'Restore password';
    //     $body = "
    //         Hello.
    //         <br>
    //         Here you have your new pass: ".$newPass."
    //         <p></p>
    //         <hr>
    //         Please don't reply to this email.
    //     ";

    //     UtilsBasic::sendEMail($from, $mailTo, $bcc, $subject, $body);
    // }
    //------------------------------------------------
}
