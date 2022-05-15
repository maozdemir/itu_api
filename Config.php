<?php


namespace ITU_API;

require_once "File.php";

use ITU_API\File as File;

class Config
{
    private $config;
    private $config_file;
    private $path = __DIR__;

    function __construct($config_file)
    {
        $this->config_file = $config_file;
        $this->file = new File($this->path, $this->config_file);
        if (!$this->file->exists()) {
            $this->file->create();
        }
    }

    function get($key)
    {
        return $this->config[$key];
    }

    function set($key, $value)
    {
        $this->config[$key] = $value;
    }

    function save()
    {
        $this->file->write(var_export($this->config, true));
    }

    function revert()
    {
        $this->config = parse_ini_file($this->config_file);
    }

    function setDefaults()
    {
        $this->set("database_host", "localhost");
        $this->set("database_name", "itu_api");
        $this->set("database_user", "root");
        $this->set("database_password", "");
        $this->set("database_port", "3306");
        $this->set("database_charset", "utf8");
        $this->set("database_collation", "utf8_general_ci");
        $this->set("database_prefix", "");
        $this->set("api_url", "http://localhost/itu_api/");
        $this->set("log_path", __DIR__ . "/logs/");
        $this->save();
    }

    function __destruct()
    {
        $this->save();
    }
}
