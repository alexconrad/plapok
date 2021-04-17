<?php


namespace PlaPok\Common;


class Common
{

    public static function link(callable|array $where, array $extraData = []): string
    {


        $ret = (empty($_SERVER['HTTPS']??null) ?'http://':'https://').($_SERVER['HTTP_HOST']??'').'/index.php?a='.$where[1];
        if (!empty($extraData)) {
            $ret .= '&'.http_build_query($extraData);
        }
        return $ret;
    }

}
