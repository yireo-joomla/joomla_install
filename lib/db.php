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
    
        $this->db = new mysqli($host, $username, $password);
        if ($this->db->connect_errno) {
            die('Failed to connect to MySQL: '.$this->db->connect_error);
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
        $this->db->query('DROP DATABASE IF EXISTS '.$dbName) or die('Error: '.$this->db->error);
    }

    public function runQuery($dbName, $query)
    {
        $dbName = $this->filterDbName($dbName);
        $this->db->select_db($dbName);
        $this->db->query($query);
    }

    protected function filterDbName($dbName)
    {
        $prefix = $this->config->get('mysql.dbprefix');
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
