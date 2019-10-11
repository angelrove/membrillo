<?php
/**
 * SearchFilters
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Database;

trait DbUtils
{
    //------------------------------------------------------------------
    private static function getTableProperties(string $table): ?array
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
}
