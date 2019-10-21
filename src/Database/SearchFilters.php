<?php
/**
 * SearchFilters
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Database;

use angelrove\utils\UtilsBasic;

trait SearchFilters
{
    //------------------------------------------------------------
    /**
     * Si $conditions contiene "[VALUE]" o es un array, se considera que viene de buscador
     */
    public static function parseFilters(array $filter_conditions, array $filter_data = []): array
    {
        $listWhere = [];

        foreach ($filter_conditions as $key => $condition) {
            /*
             * Viene de Buscador (rango de valores):
             *
             * $conditions['f_deleted'] = [
             *    'default' => "A.deleted_at IS NULL",
             *            1 => "A.deleted_at IS NOT NULL",
             * ];
             */
            if (is_array($condition)) {
                if (isset($filter_data[$key]) && isset($condition[$filter_data[$key]])) {
                    $listWhere[] = $condition[$filter_data[$key]];
                }
                // condición por defecto (key 'default')
                elseif (isset($condition['default'])) {
                    $listWhere[] = $condition['default'];
                }
            }

            /* Viene de Buscador ($condition contiene "[VALUE]") */
            elseif (strpos($condition, '[VALUE]') !== false) {
                if (isset($filter_data[$key]) && $filter_data[$key] != '') {
                    $replaceVal = $filter_data[$key];
                    $listWhere[] = str_replace('[VALUE]', $replaceVal, $condition);
                }
            }

            /* Se incluye siempre */
            else {
                $listWhere[] = $condition;
            }
        }

        return $listWhere;
    }
    //------------------------------------------------------------------
    public static function getSqlWhere(array $filter_conditions, array $filter_data = []): string
    {
        // Parse filters ---
        $listWhere = self::parseFilters($filter_conditions, $filter_data);

        // Implode list ---
        return UtilsBasic::implode(' AND ', $listWhere);
    }
    //------------------------------------------------------------------
    public static function getSqlFilters(array $filter_conditions, array $filter_data = []): string
    {
        if ($sqlFilters = self::getSqlWhere($filter_conditions, $filter_data)) {
            return ' WHERE '.$sqlFilters;
        } else {
            return '';
        }
    }
    //------------------------------------------------------------------
}
