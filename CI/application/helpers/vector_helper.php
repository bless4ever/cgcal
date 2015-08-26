<?php
if (!function_exists('normAbsPropWeight'))
{
    function normAbsPropWeight($a, $b)
    {
        if ( ! is_array($a) || ! is_array($b) || count($a) != count($b) ) {
            return array();
        }
        $result = 0;
        $diff = minus($a, $b);
        $weight = array(1,1,2,2,2,5,5);//TBD!!!!!
        foreach ($diff as $key => $value) {
            $result += abs($value)*$weight[$key];
        }
        return $result/count($diff);
    }
}
if (!function_exists('normAbs'))
{
    function normAbs($a, $b)
    {
        if ( ! is_array($a) || ! is_array($b) || count($a) != count($b) ) {
            return array();
        }
        $result = 0;
        $diff = minus($a, $b);
        foreach ($diff as $key => $value) {
            $result += abs($value);
        }
        return $result/count($diff);
    }
}
if (!function_exists('normSquare'))
{
    function normSquare($a, $b)
    {
        if ( ! is_array($a) || ! is_array($b) || count($a) != count($b) ) {
            return array();
        }
        $result = 0;
        $diff = minus($a, $b);
        foreach ($diff as $key => $value) {
            $result += abs($value*$value);
        }
        return sqrt($result)/count($diff);
    }
}
if (!function_exists('mul'))
{
    function mul($a, $b)
    {
        if ( is_array($a) || ! is_array($b) || count($b) == 0 ) {
            return array();
        }
        $result = array();
        foreach ($b as $key => $value) {
            $result[$key] = $a * $b[$key];
        }
        return $result;
    }
}
if (!function_exists('minus'))
{
    function minus($a, $b)
    {
        if ( ! is_array($a) || ! is_array($b) || count($a) != count($b) ) {
            return array();
        }
        $result = array();
        foreach ($a as $key => $value) {
            $result[$key] = $a[$key] - $b[$key];
        }
        return $result;
    }
}
if (!function_exists('plus'))
{
    function plus($a, $b)
    {
        if ( ! is_array($a) || ! is_array($b) || count($a) != count($b) ) {
            return array();
        }
        $result = array();
        foreach ($a as $key => $value) {
            $result[$key] = $a[$key] + $b[$key];
        }
        return $result;
    }
}
if (!function_exists('int'))
{
    function int($a)
    {
        foreach ($a as $key => &$value) {
            $value = intval($value);
        }
        return $a;
    }
}
if (!function_exists('up0'))
{
    function up0($a)
    {
        foreach ($a as $key => &$value) {
            $value = max($value, 0);
        }
        return $a;
    }
}
/*
if (!function_exists('mul'))
{

}
*/
