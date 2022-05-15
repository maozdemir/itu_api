<?php

namespace ITU_API\Logger;

use ITU_API\Config\Config as Config;
use ITU_API\File\File as File;

define("LOG_TYPE_INFO", 1);
define("LOG_TYPE_WARNING", 2);
define("LOG_TYPE_ERROR", 3);


class Logger {

    private $config;
    private $file;
    private $path;

    function __construct($config_file) {
        $this->config = new Config($config_file);
        $this->path = $this->config->get("log_path");
        $this->file = new File($this->path, "log.txt");
        if (!$this->file->exists()) {
            $this->file->create();
        }
    }

    function log($message, $type = LOG_TYPE_INFO) {
        $message = "[". date("Y-m-d H:i:s") . "] [". $type ."] : " . $message . "\n";
        $this->file->append($message);
    }

    function error_handler($errno, $errstr, $errfile, $errline) {
        $this->log($errstr, LOG_TYPE_ERROR);
    }
}