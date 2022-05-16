<?php

class Program
{
    private $id;
    private $name;
    private $faculty;
    private $code;

    function getName()
    {
        return $this->name;
    }
    function setName($name)
    {
        $this->name = $name;
    }
    function getFaculty()
    {
        return $this->faculty;
    }
    function setFaculty($faculty)
    {
        $this->faculty = $faculty;
    }
    function getCode()
    {
        return $this->code;
    }
    function setCode($code)
    {
        $this->code = $code;
    }
    function getId()
    {
        return $this->id;
    }
    function setId($id)
    {
        $this->id = $id;
    }
}
