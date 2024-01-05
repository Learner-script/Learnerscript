<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A Moodle block for creating customizable reports
 * @package   block_learnerscript
 * @copyright 2023 Moodle India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// REMOVE THIS BLOCK - used for DataTables test environment only!
require_once('../../../../config.php');
require_login();
$file = $_SERVER['DOCUMENT_ROOT'] . '/datatables/mysql.php';
if (is_file($file)) {
    include( $file );
}
/** SSP Class */
class SSP {

    /**
     * Create the data output array for the DataTables rows
     *
     * @param  array $columns Column information array
     * @param  array $data    Data from the SQL get
     * @return array          Formatted data in a row based format
     */
    public static function data_output($columns, $data) {
        $out = [];

        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = [];

            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = $data[$i][$columns[$j]['db']];
                }
            }

            $out[] = $row;
        }

        return $out;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @return string SQL limit clause
     */
    public static function limit($request, $columns) {
        $limit = '';

        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }

        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @return string SQL order by clause
     */
    public static function order($request, $columns) {
        $order = '';

        if (isset($request['order']) && count($request['order'])) {
            $orderby = [];
            $dtcolumns = self::pluck($columns, 'dt');

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property.
                $columnidx = intval($request['order'][$i]['column']);
                $requestcolumn = $request['columns'][$columnidx];

                $columnidx = array_search($requestcolumn['data'], $dtcolumns);
                $column = $columns[$columnidx];

                if ($requestcolumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                            'ASC' :
                            'DESC';
                    $orderby[] = '"' . $column['db'] . '"' . $dir;
                }
            }

            $order = 'ORDER BY ' . implode(', ', $orderby);
        }

        return $order;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     * @return string SQL where clause
     */
    public static function filter($request, $columns, &$bindings) {
        $globalsearch = [];
        $columnsearch = [];
        $dtcolumns = self::pluck($columns, 'dt');

        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestcolumn = $request['columns'][$i];
                $columnidx = array_search($requestcolumn['data'], $dtcolumns);
                $column = $columns[$columnidx];

                if ($requestcolumn['searchable'] == 'true') {
                    $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                    $globalsearch[] = "'" . $column['db'] . "' LIKE " . $binding;
                }
            }
        }

        // Individual column filtering.
        for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
            $requestcolumn = $request['columns'][$i];
            $columnidx = array_search($requestcolumn['data'], $dtcolumns);
            $column = $columns[$columnidx];

            $str = $requestcolumn['search']['value'];

            if ($requestcolumn['searchable'] == 'true' &&
                    $str != '') {
                $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                $columnsearch[] = "'" . $column['db'] . "' LIKE " . $binding;
            }
        }

        // Combine the filters into a single string.
        $where = '';

        if (count($globalsearch)) {
            $where = '(' . implode(' OR ', $globalsearch) . ')';
        }

        if (count($columnsearch)) {
            $where = $where === '' ?
                    implode(' AND ', $columnsearch) :
                    $where . ' AND ' . implode(' AND ', $columnsearch);
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }

        return $where;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     * @param  array $request Data sent to server by DataTables
     * @param  array $sqldetails SQL connection details - see sql_connect()
     * @param  string $table SQL table to query
     * @param  string $primarykey Primary key of the table
     * @param  array $columns Column information array
     * @return array Server-side processing response array
     */
    public static function simple($request, $sqldetails, $table, $primarykey, $columns) {
        $bindings = [];
        $db = self::sql_connect($sqldetails);

        // Build the SQL query string from the request.
        $limit = self::limit($request, $columns);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns, $bindings);

        // Main query to actually get the data.
        $data = self::sql_exec($db, $bindings, "SELECT SQL_CALC_FOUND_ROWS '" . implode("', '", self::pluck($columns, 'db')) . "'
			 FROM `$table`
			 $where
			 $order
			 $limit"
        );

        // Data set length after filtering.
        $resfilterlength = self::sql_exec($db, "SELECT FOUND_ROWS()"
        );
        $recordsfiltered = $resfilterlength[0][0];

        // Total data set length.
        $restotallength = self::sql_exec($db, "SELECT COUNT('{$primarykey}')
			 FROM   `$table`"
        );
        $recordstotal = $restotallength[0][0];
        /*
         * Output
         */
        return [
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($recordstotal),
            "recordsFiltered" => intval($recordsfiltered),
            "data" => self::data_output($columns, $data),
        ];
    }

    /**
     * Connect to the database
     *
     * @param  array $sqldetails SQL server connection details array, with the
     *   properties:
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     * @return resource Database connection handle
     */
    public static function sql_connect($sqldetails) {
        try {
            $db = @new PDO(
                    "mysql:host={$sqldetails['host']};dbname={$sqldetails['db']}",
                    $sqldetails['user'], $sqldetails['pass'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            self::fatal(
                    get_string('databaseconnectionerror', 'block_learnerscript').
                    get_String('errorinfo', 'block_learnerscript') . $e->getMessage()
            );
        }

        return $db;
    }

    /**
     * Execute an SQL query on the database
     *
     * @param  resource $db  Database handler
     * @param  array    $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string   $sql SQL query to execute.
     * @return array         Result from the query (all rows)
     */
    public static function sql_exec($db, $bindings, $sql = null) {
        // Argument shifting.
        if ($sql === null) {
            $sql = $bindings;
        }

        $stmt = $db->prepare($sql);
        if (is_array($bindings)) {
            for ($i = 0, $ien = count($bindings); $i < $ien; $i++) {
                $binding = $bindings[$i];
                $stmt->bindValue($binding['key'], $binding['val'], $binding['type']);
            }
        }

        // Execute.
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            self::fatal(get_String('sqlerroroccured', 'block_learnerscript') . $e->getMessage());
        }

        // Return all.
        return $stmt->fetchAll();
    }

    /*     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    public static function fatal($msg) {
        echo json_encode([
            "error" => $msg,
        ]);

        exit(0);
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array $a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    public static function bind(&$a, $val, $type) {
        $key = ':binding_' . count($a);

        $a[] = [
            'key' => $key,
            'val' => $val,
            'type' => $type,
        ];

        return $key;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     * @param  array  $a    Array to get data from
     * @param  string $prop Property to read
     * @return array        Array of property values
     */
    public static function pluck($a, $prop) {
        $out = [];

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }

}
