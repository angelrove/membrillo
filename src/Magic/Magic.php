<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\Magic;

use \angelrove\utils\FileSystem;

class Magic
{
    public static function command($docRoot, $params)
    {
        list($command, $param) = each($params);

        self::{'comm_'.$command}($docRoot, $param);
    }

    /**
     * Commands
     */

    public static function comm_newsecc($docRoot, $name)
    {
        $name       = strtolower($name);
        $name_model = ucfirst($name);

        // secc ---
        $dest   = $docRoot.'/app/sections/'.$name;
        $source = __DIR__.'/NewSecc/files/sections/sample';

        if (is_dir($dest)) {
            echo("The Section already exists!");
            return;
        }

        FileSystem::recurse_copy($source, $dest);

        // Replacements
        FileSystem::strReplace($dest, '[Sample]', $name_model);

        echo("Section folder ... OK\n");

        // Model ---
        $dest   = $docRoot.'/app/Models/'.$name_model.'.php';
        $source = __DIR__.'/NewSecc/files/Models/Sample.php';

        if (file_exists($dest)) {
            echo("The Model already exists!");
            return;
        }

        // Replacements
        $str = file_get_contents($source);
        $str = str_replace("[Sample]",  $name_model, $str);
        $str = str_replace("[samples]", $name, $str);

        // Copy file
        file_put_contents($dest, $str);
        echo("Model ... OK\n");

        echo("Done!");
    }
}