<?php

class Faculty
{
    private $name;
    private $id;
    private $building;
    private $code;

    function getName()
    {
        return $this->name;
    }
    function setName($name)
    {
        $this->name = $name;
    }
    function getBuilding()
    {
        return $this->building;
    }
    function setBuilding($building)
    {
        $this->building = $building;
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
