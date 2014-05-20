<?php
namespace OneDrive\Entity;

/**
 * Class Entity - Base class for REST OneDrive objects
 * @package OneDrive\Entity
 * @see http://msdn.microsoft.com/en-us/library/dn631843.aspx
 */
abstract class Entity
{
    protected $fields = array();

    public function __construct($fields)
    {
        $this->fields = array_merge($this->fields,$fields);
    }

    public function __get($field)
    {
        if (isset($this->fields[$field])){
            return $this->fields[$field];
        }
        return null;
    }

    public function __set($field,$value)
    {
        if (isset($this->fields[$field])){
            $this->fields[$field] = $value;
        }
        return null;
    }
} 