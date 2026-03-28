<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    public function download()
    {
        $dbName   = config('database.connections.mysql.database');
        $now      = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$dbName}_{$now}.sql";

        $sql = "-- ============================================================\n";
        $sql .= "-- Database Backup: {$dbName}\n";
        $sql .= "-- Generated: " . now()->format('F d, Y h:i A') . "\n";
        $sql .= "-- System: Enhance Voting System\n";
        $sql .= "-- ============================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$dbName}";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey;

            // DROP + CREATE TABLE
            $sql .= "-- ------------------------------------------------------------\n";
            $sql .= "-- Table: {$table}\n";
            $sql .= "-- ------------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql    = $createResult[0]->{'Create Table'};
            $sql .= $createSql . ";\n\n";

            // INSERT DATA
            $rows = DB::table($table)->get();
            if ($rows->isEmpty()) {
                $sql .= "-- (no data)\n\n";
                continue;
            }

            $columns = array_keys((array) $rows->first());
            $colList = implode('`, `', $columns);

            $sql .= "INSERT INTO `{$table}` (`{$colList}`) VALUES\n";

            $valueRows = [];
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    if ($value === null) return 'NULL';
                    if (is_numeric($value)) return $value;
                    return "'" . addslashes($value) . "'";
                }, (array) $row);

                $valueRows[] = '(' . implode(', ', $values) . ')';
            }

            $sql .= implode(",\n", $valueRows) . ";\n\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $sql .= "-- ============================================================\n";
        $sql .= "-- End of Backup\n";
        $sql .= "-- ============================================================\n";

        AdminDashboardController::logAction(
            'downloaded_backup',
            "Downloaded database backup: {$filename}"
        );

        return response($sql, 200, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length'      => strlen($sql),
        ]);
    }
}