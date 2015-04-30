<?php

define('SelectRegex', '/SELECT\s\*\sFROM\s(?<type>[A-z]*)\sWHERE\sContain\((?<dimensions>[0-9\s\.\,]*)\)\;/');
define('InsertRegex', '/INSERT\sINTO\s(?<type>[A-z]*)\sVALUES\s\((?<value>[A-z0-9]*)\,Contain\((?<dimensions>[0-9\s\.\,]*)\)\)\;/');
define('DimensionsRegex', '/[0-9]*\.[0-9]*\,[0-9]*\.[0-9]*/');
define('PointRegex', '/[0-9]*\.[0-9]*/');
class Interpreter
{
    public function parse($query)
    {
        $data = array();
        if(preg_match(SelectRegex,$query,$data))
        {
            return $this->parseSelect($data);
        }else if(preg_match(InsertRegex,$query,$data))
        {
            return $this->parseInsert($data);
        }

        return "error";
    }

    public function parseSelect($data)
    {
        $geometry = array();

        $nDim = $this->parseDimensions($data['dimensions'], $geometry);
        if($nDim == 0)return "error";

        $out = array(
            "function" => 0,
            "type" => $data['type'],
            "dimensions" => $nDim,
            "geometry" => $geometry
        );
    }

    public function parseInsert($data)
    {
        $geometry = array();

        $nDim = $this->parseDimensions($data['dimensions'], $geometry);
        if($nDim == 0)return "error";

        $out = array(
            "function" => 1,
            "type" => $data['type'],
            "value" => $data['value'],
            "dimensions" => $nDim,
            "geometry" => $geometry
        );
    }

    public  function parseDimensions($data, &$geometry)
    {
        preg_match_all(DimensionsRegex, $data, $dimensions);

        $geometry = array();
        foreach($dimensions[0] as $dimension)
        {
            preg_match_all(PointRegex, $dimension, $points);
            $geometry += $points;
        }

        return count($dimension);
    }
}