<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\Magic;

use angelrove\membrillo\Application;
use angelrove\utils\FileSystem;
use angelrove\utils\Db_mysql;

class Magic extends Application
{
    private $document_root;

    public function __construct($document_root)
    {
        parent::__construct($document_root, true);

        $this->document_root = $document_root;
    }

    public function command($params)
    {
        list($command, $param) = each($params);

        $this->{'comm_'.$command}($param);
    }

    /**
     * Commands
     */

    public function comm_newsecc($name)
    {
        // Model ---
        $name_model = $this->comm_newmodel($name);

        // Secc ---
        $name_secc = strtolower($name);

        $dest   = $this->document_root.'/app/sections/'.$name_secc;
        $source = __DIR__.'/NewSecc/files/sections/sample';

        if (is_dir($dest)) {
            echo("The Section already exists!");
            return;
        }

        FileSystem::recurse_copy($source, $dest);

        // Replacements
        FileSystem::strReplace($dest, '[Sample]', $name_model);

        echo("Section folder ... OK\n");

        echo("Done!");
    }

    public function comm_newmodel($name)
    {
        $name_secc  = strtolower($name);
        $name_model = ucfirst($name_secc);

        $dest   = $this->document_root.'/app/Models/'.$name_model.'.php';
        $source = __DIR__.'/NewSecc/files/Models/Sample.php';

        if (file_exists($dest)) {
            echo("The Model already exists!\n");
            return $name_model;
        }

        // Replacements
        $str = file_get_contents($source);
        $str = str_replace("[name_model]", $name_model, $str);
        $str = str_replace("[name_table]", $name_secc, $str);

        // Copy file
        file_put_contents($dest, $str);

        // BBDD ----
        $sqlTable = "
          CREATE TABLE IF NOT EXISTS `$name_secc` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `name` varchar(250) NOT NULL,
              PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;";

        Db_mysql::query($sqlTable);
        echo("Database table ... OK\n");

        echo("Model ... OK\n");

        return $name_model;
    }
}