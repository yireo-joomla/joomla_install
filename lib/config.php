<?php
class Config
{
    protected $data = array();

    public function __construct($configFile)
    {
        if(file_exists($configFile) && is_readable($configFile)) {
            $configContent = file_get_contents($configFile);
            $this->data = json_decode($configContent, true);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function get($name)
    {
        if(strstr($name, '.')) {
            $name = explode('.', $name);
            if(isset($this->data[$name[0]][$name[1]])) {
                return $this->data[$name[0]][$name[1]];
            }
        }

        if(is_string($name) && isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }
}
