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

    //--------------------------------------------------------------
    public function __construct($document_root)
    {
        parent::__construct($document_root, true);

        $this->document_root = $document_root.'/src';
    }
    //--------------------------------------------------------------
    public function command(array $params)
    {

        $commands = array_keys($params);

        //---
        $command = $commands[0];

        //---
        $param_1 = $params[$commands[0]];
        $param_2 = (isset($commands[1]))? $params[$commands[1]] : '';

        //---
        echo("\n");
        $this->{'comm_'.$command}($param_1, $param_2);
        echo("\nDone!\n");
    }
    //--------------------------------------------------------------
    /**
     * Commands
     */
    //--------------------------------------------------------------
    private function comm_newsecc($name)
    {
        // Model ---
        $source = __DIR__.'/NewSecc/files/Models/Sample.php';
        $name_model = $this->comm_newmodel($name, $source);

        // Section folder ---
        $source = __DIR__.'/NewSecc/files/sections/sample';
        $this->sectionFolder($name, $source, ['[Sample]' => $name_model]);

        // Navbar item -----
        $this->newNavItem($name);
    }
    //--------------------------------------------------------------
    /**
     * php magic --parentdetail 'Padres' --param 'Hijos'
     */
    private function comm_parentdetail($name, $nameDetail = '')
    {
        $nameLower = strtolower($name);

        // Model ---
        $source_model = __DIR__.'/NewSeccDetail/files/Models/';
        $source_secc  = __DIR__.'/NewSeccDetail/files/sections/sample';

        $name_modelParent = $this->comm_newmodel($name, $source_model.'Parent.php');

        $name_modelDetail = $this->comm_newmodel(
            $nameDetail,
            $source_model.'Detail.php',
            [
                '[parent_id]' => $nameLower.'_id',
                '[table_parent]' => strtolower($name_modelParent),
            ],
            $nameLower.'_id int(10) NOT NULL,'
        );

        // Section folder ---
        $replacements = [
            '[Sample]' => $name_modelParent,
            '[Model_parent]' => $name_modelParent,
            '[Model_detail]' => $name_modelDetail,
            '[parent_id]' => $nameLower.'_id',
        ];

        $this->sectionFolder($name, $source_secc, $replacements);

        // Navbar item -----
        $this->newNavItem($name);
    }
    //--------------------------------------------------------------
    // PRIVATE
    //--------------------------------------------------------------
    private function newNavItem($name)
    {
        $name_secc = strtolower($name);

        $str = "\n".'$CONFIG_SECCIONES->setSection("'.$name_secc.'", "'.$name.'");';
        file_put_contents('./app/CONFIG_SECC.inc', $str, FILE_APPEND);
        echo(" Nav item ..... OK\n");
    }
    //--------------------------------------------------------------
    private function sectionFolder($name, $source, array $replacements)
    {
        $name_secc = strtolower($name);

        $dest = $this->document_root.'/sections/'.$name_secc;
        if (is_dir($dest)) {
            echo("The Section already exists!");
            return;
        }

        FileSystem::recurse_copy($source, $dest);

        // Replacements ---
        foreach ($replacements as $key => $value) {
            FileSystem::strReplace($dest, $key, $value);
        }

        echo(" Section '$name_secc' ...... OK\n");

        return $name_secc;
    }
    //--------------------------------------------------------------
    public function comm_newmodel($name, $source, array $replacements = array(), $sqlCreate='')
    {
        // Model ---------------
        $name_secc  = strtolower($name);
        $name_model = ucfirst($name_secc);

        $dest = $this->document_root.'/Models/'.$name_model.'.php';
        if (file_exists($dest)) {
            echo("The Model already exists!\n");
            return $name_model;
        }

        // Replacements ---
        $str = file_get_contents($source);

        $replacements["[name_model]"] = $name_model;
        $replacements["[name_table]"] = $name_secc;

        foreach ($replacements as $key => $value) {
            $str = str_replace($key, $value, $str);
        }

        // Copy file ---
        file_put_contents($dest, $str);
        echo(" Model '$name_model' ........ OK\n");

        // DDBB ----------------
        $sqlTable = "
          CREATE TABLE IF NOT EXISTS `$name_secc` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `deleted_at` TIMESTAMP NULL DEFAULT NULL,
              `name` varchar(250) NOT NULL,
              $sqlCreate
              PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;";

        Db_mysql::query($sqlTable);
        echo(" DB table '$name_secc' ..... OK\n");

        return $name_model;
    }
    //--------------------------------------------------------------
}
