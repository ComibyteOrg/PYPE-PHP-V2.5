<?php

namespace Framework\Model;

use Framework\Database\DatabaseQuery;

/**
 * Django-style Base Model Class
 * Models define the database schema directly using fields
 */
class Model extends DatabaseQuery
{
    /**
     * Table name for this model
     * @var string
     */
    protected static $table;

    /**
     * Primary key column
     * @var string
     */
    protected static $primaryKey = 'id';

    /**
     * Fields definition (Django style)
     * @var array
     */
    protected static $fields = [];

    /**
     * Current attributes/data
     * @var array
     */
    protected $data = [];

    /**
     * Query builder properties (instance-based for chaining)
     */
    protected $querySelect = '*';
    protected $queryJoins = [];
    protected $queryWhere = [];
    protected $queryHaving = '';
    protected $queryHavingValues = [];
    protected $queryOrder = '';
    protected $queryGroup = '';
    protected $queryLimit = '';
    protected $queryOffset = '';
    protected $queryDebug = false;

    /**
     * Constructor
     */
    public function __construct($data = [])
    {
        parent::__construct();
        $this->data = $data;
    }

    /**
     * Define the table schema (override in child models)
     * This is called by migrations to get the table structure
     * 
     * Example:
     * public static function schema($table) {
     *     $table->id();
     *     $table->string('name', 255);
     *     $table->string('email', 255);
     *     $table->text('description')->nullable();
     *     $table->integer('status')->default(1);
     *     $table->timestamps();
     * }
     */
    public static function schema($table)
    {
        // Base implementation - child models should override this
        $table->id();
    }

    /**
     * Get the table name
     */
    public static function getTable()
    {
        if (static::$table) {
            return static::$table;
        }

        $parts = explode('\\', static::class);
        $className = end($parts);
        return strtolower($className) . 's';
    }

    /**
     * Get all fields definition
     */
    public static function getFields()
    {
        return static::$fields;
    }

    /**
     * Get all records from the table
     * User::all()
     */
    public static function all()
    {
        $instance = new static();
        $result = $instance->select(static::getTable());
        $records = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $records[] = new static($row);
            }
        }

        return $records;
    }

    /**
     * Find a record by primary key
     * User::find(1)
     */
    public static function find($id)
    {
        $instance = new static();
        $table = static::getTable();
        $primaryKey = static::$primaryKey;

        $result = $instance->select($table, "*", "$primaryKey = ?", "i", [$id]);

        if ($result && $row = $result->fetch_assoc()) {
            return new static($row);
        }

        return null;
    }

    /**
     * Find by a column value
     * User::findBy('email', 'user@example.com')
     */
    public static function findBy($column, $value)
    {
        $instance = new static();
        $table = static::getTable();

        $result = $instance->select($table, "*", "$column = ?", "s", [$value]);

        if ($result && $row = $result->fetch_assoc()) {
            return new static($row);
        }

        return null;
    }

    /**
     * Filter records with multiple conditions
     * User::filter(['status' => 1, 'is_active' => true])
     */
    public static function filter($conditions = [])
    {
        $instance = new static();
        $table = static::getTable();

        if (empty($conditions)) {
            return static::all();
        }

        $conditionStrings = [];
        $values = [];
        $types = "";

        foreach ($conditions as $column => $value) {
            $conditionStrings[] = "$column = ?";
            $values[] = $value;
            $types .= is_int($value) ? 'i' : 's';
        }

        $condition = implode(' AND ', $conditionStrings);
        $result = $instance->select($table, "*", $condition, $types, $values);

        $records = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $records[] = new static($row);
            }
        }

        return $records;
    }

    /**
     * Get the first record
     * User::first()
     */
    public static function first()
    {
        $records = static::all();
        return !empty($records) ? $records[0] : null;
    }

    /**
     * Get total count of records
     * User::count()
     */
    public static function count()
    {
        $instance = new static();
        $result = $instance->select(static::getTable(), "COUNT(*) as count");

        if ($result && $row = $result->fetch_assoc()) {
            return intval($row['count']);
        }

        return 0;
    }

    /**
     * Create a new record in database
     * User::create(['name' => 'John', 'email' => 'john@example.com'])
     */
    public static function create($data = [])
    {
        $instance = new static();
        $table = static::getTable();

        if ($instance->insert($table, $data)) {
            return new static($data);
        }

        return null;
    }

    /**
     * Save current model instance to database
     * $user = new User(['name' => 'John']);
     * $user->save();
     */
    public function save()
    {
        $table = static::getTable();
        $primaryKey = static::$primaryKey;

        if (empty($this->data[$primaryKey])) {
            // Insert new record
            return $this->insert($table, $this->data);
        } else {
            // Update existing record
            $id = $this->data[$primaryKey];
            $dataToUpdate = $this->data;
            unset($dataToUpdate[$primaryKey]);

            return $this->update($table, $dataToUpdate, "$primaryKey = ?", "i", [$id]);
        }
    }

    /**
     * Update a record by primary key
     * User::updateRecord(1, ['name' => 'Jane'])
     */
    public static function updateRecord($id, $data = [])
    {
        $instance = new static();
        $table = static::getTable();
        $primaryKey = static::$primaryKey;

        return $instance->update($table, $data, "$primaryKey = ?", "i", [$id]);
    }

    /**
     * Delete a record by primary key
     * User::destroy(1)
     */
    public static function destroy($id)
    {
        $instance = new static();
        $table = static::getTable();
        $primaryKey = static::$primaryKey;

        return $instance->delete($table, "$primaryKey = ?", "i", [$id]);
    }

    /**
     * Delete this model instance
     * $user->remove()
     */
    public function remove()
    {
        $table = static::getTable();
        $primaryKey = static::$primaryKey;

        if (!empty($this->data[$primaryKey])) {
            return static::destroy($this->data[$primaryKey]);
        }

        return false;
    }

    /**
     * Get all records and delete them
     * User::truncate()
     */
    public static function truncate()
    {
        $instance = new static();
        $table = static::getTable();

        return $instance->rawQuery("TRUNCATE TABLE $table");
    }

    /**
     * Execute a raw SQL query
     * User::raw("SELECT * FROM users WHERE status = ?", [1])
     * 
     * ORM Methods Quick Reference:
     * Static methods:
     *   - all() - Get all records
     *   - find($id) - Find by primary key
     *   - findBy($column, $value) - Find by column value
     *   - filter($conditions) - Filter with AND conditions
     *   - first() - Get first record
     *   - count() - Count records
     *   - create($data) - Create new record
     *   - updateRecord($id, $data) - Update record by ID
     *   - destroy($id) - Delete record by ID
     *   - truncate() - Delete all records
     *   - raw($query, $params) - Execute raw SQL
     * Instance methods:
     *   - save() - Save instance (insert/update)
     *   - remove() - Delete this instance
     *   - toArray() - Convert to array
     *   - toJson() - Convert to JSON
     */
    public static function raw($query, $params = [], $types = "")
    {
        $instance = new static();
        return $instance->rawQuery($query, $params, $types);
    }

    /**
     * Get attribute value
     */
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Set attribute value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if attribute exists
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Convert model to array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Convert model to JSON
     */
    public function toJson()
    {
        return json_encode($this->data);
    }


    /**
     * Set attribute value
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * ============================================================
     * QUERY BUILDER METHODS - Fluent Interface for Advanced Queries
     * ============================================================
     */

    /**
     * Create a new query instance
     */
    public static function query()
    {
        return new static();
    }

    /**
     * Enable debug mode
     */
    public function debug()
    {
        $this->queryDebug = true;
        return $this;
    }

    /**
     * COLUMNS - Specify columns to select
     */
    public function columns($columns)
    {
        $this->querySelect = $columns;
        return $this;
    }

    /**
     * WHERE - AND condition
     */
    public function where($column, $value, $operator = '=')
    {
        $this->queryWhere[] = ['AND', $column, $operator, $value];
        return $this;
    }

    /**
     * OR WHERE - OR condition
     */
    public function orWhere($column, $value, $operator = '=')
    {
        $this->queryWhere[] = ['OR', $column, $operator, $value];
        return $this;
    }

    /**
     * WHERE NULL
     */
    public function whereNull($column)
    {
        $this->queryWhere[] = ['AND', "$column IS NULL"];
        return $this;
    }

    /**
     * WHERE NOT NULL
     */
    public function whereNotNull($column)
    {
        $this->queryWhere[] = ['AND', "$column IS NOT NULL"];
        return $this;
    }

    /**
     * WHERE IN - Check if column in array
     */
    public function whereIn($column, array $values)
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->queryWhere[] = ['AND', "$column IN ($placeholders)", 'IN', $values];
        return $this;
    }

    /**
     * WHERE NOT IN
     */
    public function whereNotIn($column, array $values)
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->queryWhere[] = ['AND', "$column NOT IN ($placeholders)", 'NOT IN', $values];
        return $this;
    }

    /**
     * WHERE BETWEEN
     */
    public function whereBetween($column, $min, $max)
    {
        $this->queryWhere[] = ['AND', "$column BETWEEN ? AND ?", 'BETWEEN', [$min, $max]];
        return $this;
    }

    /**
     * WHERE NOT BETWEEN
     */
    public function whereNotBetween($column, $min, $max)
    {
        $this->queryWhere[] = ['AND', "$column NOT BETWEEN ? AND ?", 'NOT BETWEEN', [$min, $max]];
        return $this;
    }

    /**
     * WHERE LIKE - Pattern matching
     */
    public function whereLike($column, $value)
    {
        $this->queryWhere[] = ['AND', $column, 'LIKE', "%$value%"];
        return $this;
    }

    /**
     * WHERE NOT LIKE
     */
    public function whereNotLike($column, $value)
    {
        $this->queryWhere[] = ['AND', $column, 'NOT LIKE', "%$value%"];
        return $this;
    }

    /**
     * WHERE STARTS WITH
     */
    public function whereStartsWith($column, $value)
    {
        $this->queryWhere[] = ['AND', $column, 'LIKE', "$value%"];
        return $this;
    }

    /**
     * WHERE ENDS WITH
     */
    public function whereEndsWith($column, $value)
    {
        $this->queryWhere[] = ['AND', $column, 'LIKE', "%$value"];
        return $this;
    }

    /**
     * ORDER BY - Sort results
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->queryOrder = " ORDER BY $column " . strtoupper($direction) . " ";
        return $this;
    }

    /**
     * GROUP BY - Group results
     */
    public function groupBy($column)
    {
        $this->queryGroup = " GROUP BY $column ";
        return $this;
    }

    /**
     * HAVING - Conditions on grouped results
     */
    public function having($column, $operator, $value)
    {
        $operator = strtoupper($operator);
        $this->queryHaving .= ($this->queryHaving ? ' AND ' : ' HAVING ') . "$column $operator ?";
        $this->queryHavingValues[] = $value;
        return $this;
    }

    /**
     * LIMIT - Limit number of results
     */
    public function limit($limit)
    {
        $this->queryLimit = " LIMIT $limit ";
        return $this;
    }

    /**
     * OFFSET - Skip number of results
     */
    public function offset($offset)
    {
        $this->queryOffset = " OFFSET $offset ";
        return $this;
    }

    /**
     * TAKE - Alias for limit
     */
    public function take($limit)
    {
        return $this->limit($limit);
    }

    /**
     * SKIP - Alias for offset
     */
    public function skip($offset)
    {
        return $this->offset($offset);
    }

    /**
     * JOIN - Inner join
     */
    public function join($table, $first, $operator, $second)
    {
        $this->queryJoins[] = " JOIN $table ON $first $operator $second ";
        return $this;
    }

    /**
     * LEFT JOIN
     */
    public function leftJoin($table, $first, $operator, $second)
    {
        $this->queryJoins[] = " LEFT JOIN $table ON $first $operator $second ";
        return $this;
    }

    /**
     * RIGHT JOIN
     */
    public function rightJoin($table, $first, $operator, $second)
    {
        $this->queryJoins[] = " RIGHT JOIN $table ON $first $operator $second ";
        return $this;
    }

    /**
     * INNER JOIN
     */
    public function innerJoin($table, $first, $operator, $second)
    {
        $this->queryJoins[] = " INNER JOIN $table ON $first $operator $second ";
        return $this;
    }

    /**
     * CROSS JOIN
     */
    public function crossJoin($table)
    {
        $this->queryJoins[] = " CROSS JOIN $table ";
        return $this;
    }

    /**
     * DISTINCT - Get distinct/unique results
     */
    public function distinct()
    {
        if ($this->querySelect === '*') {
            $this->querySelect = 'DISTINCT *';
        } else {
            $this->querySelect = 'DISTINCT ' . $this->querySelect;
        }
        return $this;
    }

    /**
     * ONLY - Select specific columns
     */
    public function only($columns)
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this->columns($cols);
    }

    /**
     * EXCEPT - Exclude specific columns from result
     */
    public function except($columns, $data = null)
    {
        $excludedColumns = is_array($columns) ? $columns : [$columns];
        if ($data === null) {
            $data = $this->get();
        }

        if (is_array($data) && count($data) > 0 && is_array($data[0])) {
            foreach ($data as &$row) {
                foreach ($excludedColumns as $col) {
                    unset($row[$col]);
                }
            }
        }

        return $data;
    }

    /**
     * Build WHERE clause for query
     */
    protected function buildWhere(&$bindValues)
    {
        if (empty($this->queryWhere))
            return '';

        $sql = " WHERE ";
        $conditions = [];

        foreach ($this->queryWhere as $index => $w) {
            $condition = '';
            if (count($w) == 2) { // NULL
                $condition = "{$w[0]} {$w[1]}";
            } elseif (in_array($w[2], ['IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'])) {
                $condition = "{$w[1]}";
                if (is_array($w[3])) {
                    foreach ($w[3] as $v) {
                        $bindValues[] = $v;
                    }
                } else {
                    $bindValues[] = $w[3];
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

    /**
     * Build complete query
     */
    protected function buildQuery(&$bindValues)
    {
        $table = static::getTable();
        $where = $this->buildWhere($bindValues);
        $joins = implode('', $this->queryJoins);

        $having = '';
        if (!empty($this->queryHaving)) {
            $having = $this->queryHaving;
            $bindValues = array_merge($bindValues, $this->queryHavingValues);
        }

        $sql = "SELECT " . $this->querySelect . " FROM " . $table . " "
            . $joins . " "
            . $where . " "
            . $this->queryGroup
            . $having
            . $this->queryOrder
            . $this->queryLimit
            . $this->queryOffset;

        return $sql;
    }

    /**
     * Execute query
     */
    protected function executeQuery($sql, $bindValues)
    {
        if ($this->queryDebug) {
            echo "SQL: $sql\n";
            print_r($bindValues);
        }

        $stmt = $this->connection->prepare($sql);
        $result = $stmt->execute($bindValues);
        return $stmt;
    }

    /**
     * Reset query builder
     */
    protected function resetQuery()
    {
        $this->querySelect = '*';
        $this->queryJoins = [];
        $this->queryWhere = [];
        $this->queryHaving = '';
        $this->queryHavingValues = [];
        $this->queryOrder = '';
        $this->queryGroup = '';
        $this->queryLimit = '';
        $this->queryOffset = '';
        $this->queryDebug = false;
    }

    /**
     * GET - Fetch all results
     */
    public function get()
    {
        $bindValues = [];
        $sql = $this->buildQuery($bindValues);
        $stmt = $this->executeQuery($sql, $bindValues);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->resetQuery();
        return $data;
    }

    /**
     * FIRST - Get first result
     */
    public function getFirst()
    {
        $this->limit(1);
        $data = $this->get();
        return $data[0] ?? null;
    }

    /**
     * PLUCK - Get single column as array
     */
    public function pluck($column)
    {
        $this->querySelect = $column;
        $data = $this->get();
        return array_column($data, $column);
    }

    /**
     * GET COLUMNS - Alias for pluck
     */
    public function getColumns($column)
    {
        return $this->pluck($column);
    }

    /**
     * EXISTS - Check if any records match
     */
    public function exists()
    {
        $data = $this->get();
        return count($data) > 0;
    }

    /**
     * SUM - Sum a column
     */
    public function sum($column)
    {
        $this->querySelect = "SUM($column) as total";
        $result = $this->getFirst();
        return $result['total'] ?? 0;
    }

    /**
     * AVG - Average of a column
     */
    public function avg($column)
    {
        $this->querySelect = "AVG($column) as avg";
        $result = $this->getFirst();
        return $result['avg'] ?? 0;
    }

    /**
     * MIN - Minimum value
     */
    public function min($column)
    {
        $this->querySelect = "MIN($column) as min";
        $result = $this->getFirst();
        return $result['min'] ?? 0;
    }

    /**
     * MAX - Maximum value
     */
    public function max($column)
    {
        $this->querySelect = "MAX($column) as max";
        $result = $this->getFirst();
        return $result['max'] ?? 0;
    }

    /**
     * COUNT - Count records
     */
    public function countRows()
    {
        $this->querySelect = "COUNT(*) as count";
        $result = $this->getFirst();
        return intval($result['count'] ?? 0);
    }

    /**
     * FIND OR FAIL - Find by ID or throw exception
     */
    public function findOrFail($id)
    {
        $result = $this->where(static::$primaryKey, $id)->getFirst();
        if (!$result) {
            throw new \Exception("Record with ID $id not found in " . static::getTable());
        }
        return new static($result);
    }

    /**
     * FIND BY OR FAIL - Find by column or throw exception
     */
    public function findByOrFail($column, $value)
    {
        $result = $this->where($column, $value)->getFirst();
        if (!$result) {
            throw new \Exception("Record with $column = $value not found in " . static::getTable());
        }
        return new static($result);
    }

    /**
     * UPDATE - Update records
     */
    public function updateRows($data)
    {
        $table = static::getTable();
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));

        $bindValues = array_values($data);
        $whereClause = $this->buildWhere($bindValues);

        $sql = "UPDATE " . $table . " SET $set " . $whereClause;

        $stmt = $this->executeQuery($sql, $bindValues);
        $this->resetQuery();
        return true;
    }

    /**
     * INCREMENT - Increment a column value
     */
    public function increment($column, $amount = 1)
    {
        $table = static::getTable();
        $bindValues = [$amount];
        $whereClause = $this->buildWhere($bindValues);

        $sql = "UPDATE " . $table . " SET $column = $column + ? " . $whereClause;

        $stmt = $this->executeQuery($sql, $bindValues);
        $this->resetQuery();
        return true;
    }

    /**
     * DECREMENT - Decrement a column value
     */
    public function decrement($column, $amount = 1)
    {
        $table = static::getTable();
        $bindValues = [$amount];
        $whereClause = $this->buildWhere($bindValues);

        $sql = "UPDATE " . $table . " SET $column = $column - ? " . $whereClause;

        $stmt = $this->executeQuery($sql, $bindValues);
        $this->resetQuery();
        return true;
    }

    /**
     * DELETE - Delete records
     */
    public function deleteRows()
    {
        $table = static::getTable();
        $bindValues = [];
        $whereClause = $this->buildWhere($bindValues);

        $sql = "DELETE FROM " . $table . $whereClause;

        $stmt = $this->executeQuery($sql, $bindValues);
        $this->resetQuery();
        return true;
    }

    /**
     * UPDATE OR CREATE - Update if exists, create if not
     */
    public static function updateOrCreate($conditions, $values)
    {
        $instance = new static();
        foreach ($conditions as $col => $val) {
            $instance->where($col, $val);
        }
        $existingRecord = $instance->getFirst();

        if ($existingRecord) {
            $instance2 = new static();
            foreach ($conditions as $col => $val) {
                $instance2->where($col, $val);
            }
            return $instance2->updateRows($values);
        } else {
            $dataToInsert = array_merge($conditions, $values);
            return static::create($dataToInsert);
        }
    }

    /**
     * CHUNK - Process large datasets in batches
     */
    public function chunk($size, callable $callback)
    {
        $page = 1;
        while (true) {
            $this->resetQuery();
            $results = $this->paginate($size, $page);
            if (empty($results)) {
                break;
            }
            if ($callback($results) === false) {
                break;
            }
            $page++;
        }
        return true;
    }

    /**
     * PAGINATE - Get paginated results
     */
    public function paginate($perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $this->limit($perPage)->offset($offset);
        return $this->get();
    }

    /**
     * UPSERT - Batch insert or update
     */
    public static function upsert($data, $uniqueColumns = null)
    {
        if ($uniqueColumns === null) {
            $uniqueColumns = [static::$primaryKey];
        }

        $instance = new static();
        $table = static::getTable();

        if (empty($data)) {
            return false;
        }

        // Ensure data is array of arrays
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $columns = array_keys($data[0]);
        $placeholders = implode(',', array_fill(0, count($data), '(' . implode(',', array_fill(0, count($columns), '?')) . ')'));

        $flatValues = [];
        foreach ($data as $row) {
            foreach ($columns as $col) {
                $flatValues[] = $row[$col] ?? null;
            }
        }

        // Build ON DUPLICATE KEY UPDATE clause
        $updateClause = implode(', ', array_map(
            fn($col) => "$col = VALUES($col)",
            array_diff($columns, $uniqueColumns)
        ));

        $sql = "INSERT INTO " . $table . " (" . implode(',', $columns) . ") VALUES $placeholders";
        if (!empty($updateClause)) {
            $sql .= " ON DUPLICATE KEY UPDATE $updateClause";
        }

        $stmt = $instance->connection->prepare($sql);
        return $stmt->execute($flatValues);
    }

    /**
     * TRANSACTION - Execute query in transaction
     */
    public static function transaction(callable $callback)
    {
        $instance = new static();
        try {
            $instance->connection->beginTransaction();
            $callback($instance);
            $instance->connection->commit();
        } catch (\Exception $e) {
            $instance->connection->rollBack();
            throw $e;
        }
    }
}

