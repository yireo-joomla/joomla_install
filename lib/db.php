<?php
class Db
{
    protected $config;

    protected $db;

    protected $errors = array();

    public function __construct(Config $config)
    {
        $this->config = $config;

        $username = $this->config->get('mysql.username');
        $password = $this->config->get('mysql.password');

        $host = $this->config->get('mysql.host');
        if(empty($host)) $host = 'localhost';
    
        $dbname = $this->config->get('mysql.db');
        if(empty($dbname)) $dbname = $username;
    
        $this->db = new mysqli($host, $username, $password);
        if ($this->db->connect_errno) {
            $this->errors[] = 'Failed to connect to MySQL: '.$this->db->connect_error;
        }
    }

    public function createDb($dbName)
    {
        $dbName = $this->filterDbName($dbName);
        $this->db->query('CREATE DATABASE '.$dbName);
    }

    public function dropDb($dbName)
    {
        $dbName = $this->filterDbName($dbName);
        $this->db->query('DROP DATABASE '.$dbName);
    }

    protected function filterDbName($dbName)
    {
        $prefix = $this->config->get('mysql.prefix');
        if(preg_match('/^'.$prefix.'/', $dbName) == false) {
            $dbName = $prefix.$dbName;
        }
    
        return $dbName;
    }

    protected function getErrors()
    {
        return $this->errors;
    }
}
