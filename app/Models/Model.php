<?php


namespace App\Models;


use App\Extensions\Database;

/**
 * Class Model
 * @package App\Models
 */
abstract class Model
{
    /**
     * @var string Table in DB for model
     */
    protected string $table;

    /**
     * @var string Fields for query, like "Select", etc.
     */
    protected string $fields = '*';

    /**
     * @var array Current array of model fields
     */
    protected array $row_data = [];

    /**
     * @var string "Join" condition for sql
     */
    protected string $join = '';

    /**
     * @var string "Where" condition for sql
     */
    protected string $where = '';

    /**
     * @var string "Limit" condition for sql
     */
    protected string $limit = '';

    /**
     * @var string "Order" condition for sql
     */
    protected string $order = '';

    /**
     * @var bool Enable it if table has timestamps
     */
    protected bool $enable_timestamps = false;

    public function __construct($id = false)
    {
        if ($id) {
            $this->row_data['id'] = $id;
            $this->where([["id='{$id}'", "AND"]])
                ->first(true);
        }
    }


    /**
     * Fetch all rows
     * @return mixed mysqli assoc array if possible
     */
    public function all()
    {
        return (new Database())
            ->query($this->select())
            ->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get first row from query
     * @return Model|array
     */
    public function first($pipe = false)
    {
        $row = (new Database())
            ->query($this->select())
            ->fetch_assoc();
        $this->row_data = $row ?? [];
        return $pipe ? $this : $row;
    }

    /**
     * Set fields to $row_data
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        $this->row_data[$name] = $value;
    }

    /**
     * Get fields to $row_data
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->row_data[$name] ?? false;
    }

    /**
     * Save Model to DB
     *
     * If model has id, we update row, else insert
     */
    public function save()
    {
        $db = new Database();

        $keys = [];
        $values = [];
        $fields = [];

        // remove times to update method
        if ($this->id) {
            unset($this->row_data['time_create']);
            unset($this->row_data['time_edit']);
        }

        // Combine keys and values for query
        foreach ($this->row_data as $key => $value) {
            $value = $value === null ? 'NULL' : $value;
            $value = $db->real_escape_string($value);
            $value = preg_match("/\(|null/suix", $value) != false ? $value : "'{$value}'";
            $key = "`{$key}`";

            if ($this->id) {
                $fields[] = "{$key} = {$value}";
            } else {
                $keys[] = $key;
                $values[] = $value;
            }
        }

        // Add additional keys for insert or update methods
        if ($this->enable_timestamps && !$this->id) {
            $keys[] = 'time_create';
            $keys[] = 'time_edit';

            $values[] = 'CURRENT_DATE()';
            $values[] = 'CURRENT_DATE()';
        } elseif ($this->enable_timestamps && $this->id) {
            $fields[] = 'time_edit = CURRENT_DATE()';
        }

        // Convert all into String
        $keys = implode(', ', $keys);
        $values = implode(', ', $values);
        $fields = implode(', ', $fields);

        // Build SQL
        if ($this->id) {
            $sql = "UPDATE `{$this->table}` SET {$fields} WHERE `id` = {$this->id};";
        } else {
            $sql = "INSERT INTO `{$this->table}` ({$keys}) VALUES ({$values});";
            // DEBUG:
            //            $fs = fopen('sql.txt', 'w');
            //            fwrite($fs, $sql);
            //            fclose($fs);
        }
        // Make query and return result
        $db = new Database();
        $insert = $db->query($sql);
        $this->row_data['id'] = $this->row_data['id'] ?? $db->getInsertID();
        return $insert;
    }

    public function fields(array $fields){
        $this->fields = implode(', ', $fields);
        return $this;
    }

    /**
     * Remove row from table. Non-safe remove
     * @param $id
     * @param string $column
     * @return bool|\mysqli_result
     */
    public function remove($id, string $column = 'id')
    {
        $sql = "DELETE FROM {$this->table} WHERE {$column} = {$id}";
        return (new Database())->query($sql);
    }

    /**
     *
     * @param string $field field for order
     * @param string $mode DESC or ASC
     * @return $this for pipe
     */
    public function orderBy(string $field, string $mode = 'desc'): Model
    {
        $order = &$this->order;
        $order .= $order ? ' AND ' : 'ORDER BY ';
        $order .= $field . ' ' . $mode;

        return $this;
    }

    /**
     * Function for left jon
     * @param string $table join table
     * @param string $join_on join condition
     * @return $this
     */
    public function leftJoin(string $table, string $join_on)
    {
        $this->join .= " LEFT JOIN {$table} ON {$join_on} ";
        return $this;
    }

    /**
     * Build "Where" condition
     *
     * @param array $fields example: [ ["'title' LIKE '%test%'", 'AND'], ...[] ]
     * @return $this for pipe
     */
    public function where(array $fields): Model
    {
        $where = &$this->where;
        for ($i = 0; $i < count($fields); $i++) {
            $separator = $where ? ' ' . trim(($fields[$i][1] ?? 'AND')) . ' ' : 'WHERE ';
            $where .= $separator . $fields[$i][0] . ' ';
        }

        return $this;
    }

    /**
     * Build "Select" sql
     * @return string
     */
    private function select(): string
    {
        return "SELECT {$this->fields} FROM {$this->table} {$this->join} {$this->where} {$this->limit} {$this->order}";
    }
}