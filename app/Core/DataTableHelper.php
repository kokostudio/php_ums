<?php

namespace App\Core;

use PDO;

class DataTableHelper
{
    /**
     * Process DataTables server-side request.
     *
     * @param PDO $db The PDO database instance
     * @param array $request The $_GET or $_POST array from DataTables
     * @param string $baseQuery The SELECT and FROM/JOIN part of the query (e.g., "SELECT u.id, u.name FROM users u")
     * @param array $searchableColumns Columns to search against when the global search is used
     * @param array $orderableColumns Map of index => column name for ordering (e.g., [0 => 'u.id', 1 => 'u.name'])
     * @param string $defaultOrderColumn The column to order by default (e.g., 'u.id')
     * @param array $extraWhereConditions Any additional custom WHERE conditions (e.g., ["u.status = 'active'"])
     * @param array $extraParams Any additional PDO bindings for the extra WHERE conditions
     * @param string $countQuery Optional query to count total records (e.g. "SELECT COUNT(id) FROM users"). If null, a generic one will be attempted.
     * @return array The structured array to return as JSON to DataTables
     */
    public static function process(
        PDO $db,
        array $request,
        string $baseQuery,
        array $searchableColumns,
        array $orderableColumns,
        string $defaultOrderColumn = 'id',
        string $defaultOrderDir = 'DESC',
        array $extraWhereConditions = [],
        array $extraParams = [],
        ?string $countQuery = null
    ): array {
        $draw = $request['draw'] ?? 1;
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $searchValue = $request['search']['value'] ?? '';

        $params = $extraParams;
        $whereConditions = $extraWhereConditions;

        // 1. Global Search
        if (!empty($searchValue) && !empty($searchableColumns)) {
            $searchConditions = [];
            // Escape อักขระพิเศษของ LIKE (% _ \) ให้เป็นตัวอักษรธรรมดา —
            // ไม่งั้นผู้ใช้พิมพ์ "%" หนึ่งตัวจะกลายเป็น wildcard จับคู่ทุกแถว
            $escapedSearch = addcslashes($searchValue, '\\%_');
            foreach ($searchableColumns as $i => $column) {
                $paramName = "dt_search_{$i}";
                $searchConditions[] = "{$column} LIKE :{$paramName}";
                $params[$paramName] = "%{$escapedSearch}%";
            }
            $whereConditions[] = "(" . implode(" OR ", $searchConditions) . ")";
        }

        $whereClause = "";
        if (!empty($whereConditions)) {
            $whereClause = "WHERE " . implode(" AND ", $whereConditions);
        }

        // 2. Ordering
        $orderColumnIndex = $request['order'][0]['column'] ?? 0;
        $orderDir = $request['order'][0]['dir'] ?? $defaultOrderDir;
        $orderColumn = $orderableColumns[$orderColumnIndex] ?? $defaultOrderColumn;
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        // 3. Count Total Records (Without filters)
        // If no explicit count query is provided, we try to deduce it from the base query 
        // by replacing SELECT ... FROM with SELECT COUNT(*) FROM (Very basic deduction, might fail on complex queries)
        if (!$countQuery) {
            $fromPos = stripos($baseQuery, 'FROM');
            $countQuery = "SELECT COUNT(*) " . substr($baseQuery, $fromPos);
        }
        $totalRecords = $db->query($countQuery)->fetchColumn();

        // 4. Count Filtered Records
        $fromPos = stripos($baseQuery, 'FROM');
        $fromClause = substr($baseQuery, $fromPos);
        $totalFilteredQuery = "SELECT COUNT(*) {$fromClause} {$whereClause}";
        
        $stmtFiltered = $db->prepare($totalFilteredQuery);
        $stmtFiltered->execute($params);
        $totalFiltered = $stmtFiltered->fetchColumn();

        // 5. Fetch Data
        // length = -1 (ตัวเลือก "All" ของ DataTables) ใช้ LIMIT ไม่ได้ — ดึงทั้งหมดแทน
        $isFetchAll = ((int) $length) < 0;
        $query = $isFetchAll
            ? "{$baseQuery} {$whereClause} ORDER BY {$orderColumn} {$orderDir}"
            : "{$baseQuery} {$whereClause} ORDER BY {$orderColumn} {$orderDir} LIMIT :start, :length";

        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        if (!$isFetchAll) {
            $stmt->bindValue(':start', (int) $start, PDO::PARAM_INT);
            $stmt->bindValue(':length', (int) $length, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
    }
}
