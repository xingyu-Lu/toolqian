<?php


namespace Cilex\Tools;


class Time
{
    /**
     * 获取当前毫秒数
     * @return int
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());

        return sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}