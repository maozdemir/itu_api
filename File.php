<?php

namespace  ITU_API\File;

class File
{
    private $file;
    private $file_name;
    private $file_path;

    function __construct($file_path, $file_name)
    {
        $this->file_name = $file_name;
        $this->file_path = $file_path;
        $this->file = fopen($this->file_path . $this->file_name, "a");
    }

    // check if file exists
    function exists()
    {
        return file_exists($this->file_path . $this->file_name);
    }

    // check if file is empty
    function isEmpty()
    {
        return filesize($this->file_path . $this->file_name) == 0;
    }

    // create file if it doesn't exist
    function create()
    {
        if (!$this->exists()) {
            $this->file = fopen($this->file_path . $this->file_name, "a");
        }
    }

    // write to file
    function write($data)
    {
        fwrite($this->file, $data);
    }

    //append to file with a new line
    function append($data)
    {
        fwrite($this->file, $data);
    }

    // read from file
    function read()
    {
        return fread($this->file, filesize($this->file_path . $this->file_name));
    }

    // close file
    function close()
    {
        fclose($this->file);
    }

    // destructor
    function __destruct()
    {
        $this->close();
    }
}
