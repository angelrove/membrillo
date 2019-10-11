<?php
/**
 * Generador de consultas SQL
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Database;

class GenQuery
{
    use SearchFilters;
    use FormValues;
    use DbUtils;

    public static $executed_queries = [];
}
