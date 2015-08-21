<?php

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
/*
if (!function_exists('mul'))
{

}
*/
