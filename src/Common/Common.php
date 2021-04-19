<?php


namespace PlaPok\Common;


class Common
{

    public static function link(callable|array $where, array $extraData = []): string
    {
        $ret = (empty($_SERVER['HTTPS']??null) ?'http://':'https://').($_SERVER['HTTP_HOST']??'').'/';

        $prefix = '?';
        if ($where[1] !== 'index') {
            $ret .= 'index.php?a='.$where[1];
            $prefix = '&';
        }
        if (!empty($extraData)) {
            $ret .= $prefix.http_build_query($extraData);
        }
        return $ret;
    }

}
