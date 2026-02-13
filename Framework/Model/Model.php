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
     * Get attribute value
     */
    public function get($name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    /**
     * Set attribute value
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }
}
