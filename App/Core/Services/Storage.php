<?php


namespace App\Core\Services;


class Storage
{

    private static string $UPLOAD_DIR;
    private static int $MAX_UPLOAD_SIZE;

    /**
     * Sets the root upload directory path
     * @param $path
     */
    public static function setUploadDir( $path )
    {
        self::$UPLOAD_DIR = $path;
    }

    /**
     * Gets the root upload directory
     * @return string
     */
    public static function getUploadDir(): string
    {
        return self::$UPLOAD_DIR;
    }

    /**
     * Sets the global maximum upload size
     * @param int $size
     */
    public static function setMaxUploadSize( int $size )
    {
        self::$MAX_UPLOAD_SIZE = $size;
    }

    /**
     * Returns the maximum upload size specified by the user, if none is set,
     * upload sized supported by the php will be returned
     * @return int
     */
    public static function getMaxUploadSize(): int
    {
        if ( isset( self::$MAX_UPLOAD_SIZE ) )
            return self::$MAX_UPLOAD_SIZE;

        return Uploader::getSupportedMaxUploadSize();
    }

}
