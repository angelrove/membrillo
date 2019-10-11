<?php
/**
 * SearchFilters
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Database;

use angelrove\utils\FileUploaded;

trait DbUtils
{
    //------------------------------------------------------------------
    public static function getTableProperties(string $table): ?array
    {
        $listFields = \DB::select("SHOW FULL COLUMNS FROM $table");

        $tableProp = [];
        foreach ($listFields as $field) {
            $columnName = $field->Field;
            if ($columnName == 'id') {
                continue;
            }

            $tableProp[$columnName] = new \stdClass();
            $tableProp[$columnName]->title = '';

            // Propiedades a través de MySql
            $tableProp[$columnName]->type = ($field->Type == 'timestamp' || $field->Type == 'datetime') ?
            $field->Type :
            trim(substr($field->Type, 0, 7));

            $tableProp[$columnName]->obligatorio = ($field->Null == 'NO') ? 'true' : '';
            $tableProp[$columnName]->unique      = ($field->Key == 'UNI') ? 'true' : '';

            // Propiedades a través del comentario ---
            if ($field->Comment) {
                $field->Comment = str_replace(";", "&", $field->Comment);
                parse_str($field->Comment, $output);

                if (isset($output['title'])) {
                    $tableProp[$columnName]->title = $output['title'];
                }
                if (isset($output['type'])) {
                    $tableProp[$columnName]->type = $output['type'];
                }
            }
        }

        return $tableProp;
    }
    //------------------------------------------------------------------
    /* Delete uploaded files */
    public static function deleteUploadsById(string $DB_TABLE, $id)
    {
        global $seccCtrl;
        $listFields = self::getTableProperties($DB_TABLE);

        foreach ($listFields as $fieldName => $fieldProp) {
            if ($fieldProp->type != 'file') {
                continue;
            }

            $bbdd_file = \DB::table($DB_TABLE)->where('id', $id)->value($fieldName);
            if (!$bbdd_file) {
                continue;
            }

            $paramsFile = FileUploaded::getInfo($bbdd_file, $seccCtrl->UPLOADS_DIR);
            if ($paramsFile['name']) {
                unlink($paramsFile['path_completo']); // archivo
                @unlink($paramsFile['path_completo_th']); // if thumbnail
            }
        }
    }
    //------------------------------------------------------------------
}
