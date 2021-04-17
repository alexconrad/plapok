<?php


namespace PlaPok\Controllers\Globals;


class Variable
{

    public function post($key)
    {
        return $_POST[$key] ?? null;
    }

    public function get($key)
    {
        return $_GET[$key] ?? null;
    }


}
