<?php if(!defined('BASEPATH')) exit('No direct script access allowed.');

class MY_Model extends CI_Model
{
    /**
     * The database table name associated with this model
     */
    protected $_table = '';

    /**
     * The primary key for this table
     */
    protected $_primary_key = 'id';

    /**
     * The field by which this table is sorted
     */
    protected $_order_by = 'id';

    /**
     * The default timezone used when generating timestamps
     */
    protected $_timezone = '';

    /**
     * Set to true to generate timestamps when creating or modifying data
     */    
    protected $_timestamps = false;

    /**
     * The timestamp format to use
     */
    protected $_time_format = 'Y-m-d H:i:s';

    /**
     * Flag records as deleted rather than permenantly deleting them
     */
    protected $_soft_delete = false;

    /**
     * Field names to use for timestamps
     */
    protected $_created_field = 'Created';
    protected $_modified_field = 'Modified';
    protected $_deleted_field = 'Deleted';

    /**
     * By default functions will return an array of objects
     * Setting this to true will return a pure array
     */
    protected $_return_array = false;

    /** 
     * Sets the default timezone to use upon class instantiation 
     * 
     * @return void 
     */  
    public function __construct()
    {
        parent::__construct();
        if ($this->_timezone !== '') {
            date_default_timezone_set($this->_timezone);
        }
    }

    /** 
     * Retrieve a single record
     *
     * @param int|string $key a unique identifier to retrieve record 
     * @return array|object the record as an object or array
     */  
    public function get($key)
    {
        $method = 'row'.($this->_return_array ? '_array' : '');

        $this->db->where($this->_primary_key, $key);
        $this->db->limit(1);
        return $this->db->get($this->_table)->$method();
    }

    /** 
     * Retrieve multiple records
     *
     * @param array $filters key, value pairs used to filter records
     * @param int $limit number of records to retrieve
     * @param int $offset record to start getting subsequent records from
     * @return array|object set of records returned as an array of objects or arrays
     */  
    public function get_many($filters, $limit = null, $offset = null)
    {
        $method = 'result'.($this->_return_array ? '_array' : '');
        
        $this->db->where($filters);
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get($this->_table)->$method();
    }

    /** 
     * Retrieve all records
     *
     * @return array|object all records in this table
     */  
    public function get_all()
    {
        $method = 'result'.($this->_return_array ? '_array' : '');
        return $this->db->get($this->_table)->$method();
    }

    /** 
     * Creates a single record
     *
     * @param array $data an array of fields & values to be inserted
     * @return int ID of new record 
     */ 
    public function create($data)
    {
        if ($this->_timestamps) {
            $now = date($this->_time_format);
            $data[$this->_created_field] = $now;
            $data[$this->_modified_field] = $now;
        }
        $this->db->insert($this->_table, $data);
        return $this->db->insert_id();
    }

    /** 
     * Creates multiple records
     *
     * @param array $data an array of arrays of fields & values to be inserted
     * @return int ID's of the new records
     */ 
    public function create_many($data)
    {
        if ($this->_timestamps) {
            $now = date($this->_time_format);
            $data[$this->_created_field] = $now;
            $data[$this->_modified_field] = $now;
        }
        $ids = array();
        foreach ($data as $row) {
            $this->db->insert($this->_table, $row);
            $ids[] = $this->db->insert_id();
        }
        return $ids;
    }

    /** 
     * Updates a single record
     * 
     * @param int|string $key a unique identifier of record to be updated
     * @param array $data an associative array of values to be updated
     * @return bool success status of the update
     */ 
    public function update($key, $data)
    {
        if ($this->_timestamps) {
            $data[$this->_modified_field] = date($this->_time_format);
        }
        $this->db->where($this->_primary_key, $key);
        $this->db->limit(1);
        return $this->db->update($this->_table, $data);
    }

    /** 
     * Updates multiple records
     * 
     * @param array $filters array of filters to select records to be updated
     * @param array $data an associative array of values to be updated
     * @return bool success status of the update
     */ 
    public function update_many($filters, $data)
    {
        if ($this->_timestamps) {
            $data[$this->_modified_field] = date($this->_time_format);
        }
        $this->db->where($filters);
        return $this->db->update($this->_table, $data);
    }

    /** 
     * Deletes a single record
     * 
     * @param int|string $key a unique idetifier of record to be deleted
     * @param bool $soft whether or not to permenantly delete record
     * @return bool whether or not the deletion was succesful
     */ 
    public function delete($key, $soft = null)
    {
        $soft || $soft = $this->_soft_delete;
        if ($soft === true) {
            if ($this->_timestamps) {
                $now = date($this->_time_format);
                $data[$this->_modified_field] = date($this->_time_format);
            }
            $data[$this->_deleted_field] = ($now || date($this->_time_format));
            return $this->update($key, $date);
        } else {
            $this->db->where($this->_primary_key, $key);
            $this->db->limit(1);
            return $this->db->delete($this->_table);
        }
    }

    /** 
     * Deletes multiple records
     * 
     * @param array $filters array of filters to select records to be updated
     * @param bool $soft whether or not to permenantly delete records
     * @return bool whether or not the deletion was succesful
     */ 
    public function delete_many($filters, $soft = null)
    {
        $soft || $soft = $this->_soft_delete;
        if ($soft === true) {
            if ($this->_timestamps) {
                $now = date($this->_time_format);
                $data[$this->_modified_field] = date($this->_time_format);
            }
            $data[$this->_deleted_field] = ($now || date($this->_time_format));
            return $this->update_many($filters, $data);
        } else {
            $this->db->where($filters);
            return $this->db->delete($this->_table);
        }
    }

    /**
     * Deletes all records in the table
     * 
     * @return bool status of the deletion
     */
    public function delete_all()
    {
        return $this->db->empty_table($this->_table);
    }

    /**
     * Truncates the table
     * 
     * @return bool status of the truncating
     */
    public function truncate()
    {
        return $this->db->truncate($this->_table);
    }

    /**
     * Restore a single record
     *
     * @param int|string $key a unique identifier of record to be restored
     * @return bool status of the restoraton
     */
    public function restore($key)
    {
        $this->db->where($this->_primary_key, $key);
        $this->db->where($this->_deleted_field.'!=', null);
        $this->db->limit(1);
        $data[$this->_deleted_field] = null;
        $data[$this->_modified_field] = date($this->_time_format);
        return $this->update($key, $data);
    }

    /**
     * Restores multiple records
     *
     * @param array $filters key, value pairs used to filter records
     * @return bool status of the restoraton
     */
    public function restore_many($filters)
    {
        $this->db->where($filters);
        $this->db->where($this->_deleted_field.'!=', null);
        $data[$this->_deleted_field] = null;
        $data[$this->_modified_field] = date($this->_time_format);
        return $this->update_many($filters, $data);
    }

    /**
     * Counts the number of records in the table
     *
     * @return int number of records in table
     */
    public function count()
    {
        return $this->db->count_all($this->_table);
    }

    /**
     * Counts the number of records matching a set of filters
     *
     * @param array $filters set of values to filter records
     * @return int number of records matching the filters 
     */
    public function count_many($filters)
    {
        $this->db->where($filters);
        $this->db->from($this->_table);
        return $this->db->count_all_results();
    }

    /**
     * Getter for table name
     *
     * @return string name of the table
     */
    public function table()
    {
        return $this->_table;
    }

    /**
     * Getter for table fields
     *
     * @return array fields in the table
     */
    public function fields()
    {
        return $this->db->list_fields($this->_table);
    }
}
