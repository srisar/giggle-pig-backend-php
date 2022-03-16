<?php


namespace App\Core\Resources;


class KeyGenerator
{
    /**
     * Generates unique ID
     * @param string $append
     * @param string $prepend
     * @return string
     */
    public static function generateUID( string $prepend = "", string $append = "" ): string
    {
        $key = md5( microtime() . rand() );
        return sprintf( "%s%s%s", $prepend, $key, $append );
    }
}
