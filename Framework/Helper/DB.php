<?php

namespace Framework\Helper;

use Framework\Database\Connect;
use PDO;

class DB extends Connect
{
    protected static $table;
    protected static $select = '*';
    protected static $joins = [];
    protected static $where = [];
    protected static $order = '';
    protected static $group = '';
    protected static $limit = '';
    protected static $offset = '';

    public bool $debug = false;

    public function __construct()
    {
        parent::__construct();
    }

    // Reset after each query
    protected static function reset()
    {
        self::$table = null;
        self::$select = '*';
        self::$joins = [];
        self::$where = [];
        self::$order = '';
        self::$group = '';
        self::$limit = '';
        self::$offset = '';
    }

    // 🔥 Shortcuts: DB::users()
    public static function __callStatic($table, $args)
    {
        return self::table($table);
    }

    public static function table($table)
    {
        $instance = new static();
        self::$table = $table;
        return $instance;
    }

    public function debug()
    {
        $this->debug = true;
        return $this;
    }

    // SELECT
    public function select($columns)
    {
        self::$select = $columns;
        return $this;
    }

    // WHERE
    public function where($column, $value, $operator = '=')
    {
        error_log("DB::where called with column: $column, value: $value, operator: $operator");
        self::$where[] = ['AND', $column, $operator, $value];
        return $this;
    }

    public function orWhere($column, $value, $operator = '=')
    {
        self::$where[] = ['OR', $column, $operator, $value];
        return $this;
    }

    public function whereNull($column)
    {
        self::$where[] = ['AND', "$column IS NULL"];
        return $this;
    }

    public function whereNotNull($column)
    {
        self::$where[] = ['AND', "$column IS NOT NULL"];
        return $this;
    }

    public function whereIn($column, array $values)
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        self::$where[] = ['AND', "$column IN ($placeholders)", 'IN', $values];
        return $this;
    }

    // ORDER BY
    public function orderBy($column, $direction = 'ASC')
    {
        self::$order = " ORDER BY $column $direction ";
        return $this;
    }

    // GROUP BY
    public function groupBy($column)
    {
        self::$group = " GROUP BY $column ";
        return $this;
    }

    // LIMIT
    public function limit($limit)
    {
        self::$limit = " LIMIT $limit ";
        return $this;
    }

    public function offset($offset)
    {
        self::$offset = " OFFSET $offset ";
        return $this;
    }

    // JOIN
    public function join($table, $first, $operator, $second)
    {
        self::$joins[] = " JOIN $table ON $first $operator $second ";
        return $this;
    }

    public function leftJoin($table, $first, $operator, $second)
    {
        self::$joins[] = " LEFT JOIN $table ON $first $operator $second ";
        return $this;
    }

    // RAW Queries
    public function raw($sql, $bindValues = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($bindValues);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // WHERE builder
    protected function buildWhere(&$bindValues)
    {
        if (empty(self::$where))
            return '';

        $sql = " WHERE ";
        $conditions = [];

        foreach (self::$where as $index => $w) {
            $condition = '';
            if (count($w) == 2) { // NULL
                $condition = "{$w[0]} {$w[1]}";
            } elseif ($w[2] === 'IN') {
                $condition = "{$w[1]}";
                foreach ($w[3] as $v) {
                    $bindValues[] = $v;
                }
            } else {
                $condition = "{$w[0]} {$w[1]} {$w[2]} ?";
                $bindValues[] = $w[3];
            }

            // Remove leading AND/OR for the first condition
            if ($index === 0) {
                $condition = preg_replace('/^(AND|OR)\s+/i', '', trim($condition));
            }

            $conditions[] = $condition;
        }

        return $sql . implode(' ', $conditions);
    }

    // Build query
    protected function buildQuery(&$bindValues)
    {
        $where = $this->buildWhere($bindValues);
        $joins = implode('', self::$joins);

        $sql = "SELECT " . self::$select . " FROM " . self::$table . " "
            . $joins . " "
            . $where . " "
            . self::$group
            . self::$order
            . self::$limit
            . self::$offset;

        return $sql;
    }

    // Execute query
    protected function run($sql, $bindValues)
    {
        error_log("DB::run executing SQL: $sql with bind values: " . json_encode($bindValues));

        if ($this->debug) {
            echo "SQL: $sql\n";
            print_r($bindValues);
        }

        $stmt = $this->connection->prepare($sql);
        $result = $stmt->execute($bindValues);
        error_log("DB::run execute result: " . ($result ? 'true' : 'false'));
        return $stmt;
    }    // FETCH ALL
    public function get()
    {
        error_log("DB::get called on table " . self::$table);
        $bindValues = [];
        $sql = $this->buildQuery($bindValues);
        error_log("SQL Query: $sql");
        $stmt = $this->run($sql, $bindValues);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Query result count: " . count($data));

        self::reset();
        return $data;
    }

    // FETCH FIRST ROW
    public function first()
    {
        error_log("DB::first called on table " . self::$table);
        $this->limit(1);
        $data = $this->get();
        $result = $data[0] ?? null;
        error_log("DB::first result: " . json_encode($result));
        return $result;
    }

    // FIND BY ID
    public function find($id)
    {
        error_log("DB::find called on table " . self::$table . " with id: $id");
        $result = $this->where('id', $id)->first();
        error_log("DB::find result: " . json_encode($result));
        return $result;
    }

    // COUNT
    public function count()
    {
        self::$select = "COUNT(*) as count";
        return $this->first()['count'] ?? 0;
    }

    // EXISTS
    public function exists()
    {
        return $this->count() > 0;
    }

    // PLUCK
    public function pluck($column)
    {
        self::$select = $column;
        $data = $this->get();
        return array_column($data, $column);
    }

    // AGGREGATES
    public function sum($column)
    {
        self::$select = "SUM($column) as total";
        return $this->first()['total'];
    }

    public function avg($column)
    {
        self::$select = "AVG($column) as avg";
        return $this->first()['avg'];
    }

    public function min($column)
    {
        self::$select = "MIN($column) as min";
        return $this->first()['min'];
    }

    public function max($column)
    {
        self::$select = "MAX($column) as max";
        return $this->first()['max'];
    }

    // PAGINATION
    public function paginate($perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $this->limit($perPage)->offset($offset);
        return $this->get();
    }

    // INSERT
    public function insert($data)
    {
        error_log("DB::insert called on table " . self::$table . " with data: " . json_encode($data));
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO " . self::$table . " ($columns) VALUES ($placeholders)";
        error_log("SQL: $sql");
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->execute(array_values($data));
        error_log("Insert result: " . ($result ? 'true' : 'false'));

        $id = $this->connection->lastInsertId();
        error_log("Last insert ID: $id");

        self::reset();
        return $id;
    }

    // UPDATE
    public function update($data, $where)
    {
        error_log("DB::update called on table " . self::$table . " with data: " . json_encode($data) . " and where: " . json_encode($where));
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));

        $instance = new static();
        foreach ($where as $c => $v) {
            $instance->where($c, $v);
        }

        $bindValues = array_values($data);
        $whereClause = $instance->buildWhere($bindValues);

        $sql = "UPDATE " . self::$table . " SET $set $whereClause";
        error_log("SQL: $sql");

        $stmt = $this->run($sql, $bindValues);

        self::reset();
        return true;
    }    // DELETE
    public function delete($where)
    {
        $instance = new static();
        foreach ($where as $c => $v) {
            $instance->where($c, $v);
        }

        $bindValues = [];
        $whereClause = $instance->buildWhere($bindValues);

        $sql = "DELETE FROM " . self::$table . $whereClause;

        $stmt = $this->run($sql, $bindValues);

        self::reset();
        return true;
    }

    // TRANSACTIONS
    public function transaction(callable $callback)
    {
        try {
            $this->connection->beginTransaction();
            $callback($this);
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
