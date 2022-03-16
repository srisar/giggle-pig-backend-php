<?php


namespace App\Core\Services;


use App\Core\Resources\KeyGenerator;
use Exception;

class Uploader
{

    private array $mimes = [
        "supportedMimes" => [],
        "checkMime" => false,
        "preferredExtension" => ".ext",
        "hasPreferredExtension" => false,
    ];

    private int $maxFileSize;
    private array $fileObject;

    private string $baseDir;
    private string $subDir;
    private string $absolutePath;
    private string $relativePath;


    /**
     * Creates an uploader instance with file object and optional parameters.
     * @param $file_object
     * @param float $max_file_size - when 0 is passed, max file size validation is ignored
     * @param string $sub_dir - directory inside the root upload directory
     * @param array $supported_mimes - when empty array is passed, mime validation is ignored
     * @throws Exception
     */
    public function __construct( $file_object, float $max_file_size = 0, string $sub_dir = "", array $supported_mimes = [] )
    {
        $this->baseDir = Storage::getUploadDir();
        $this->subDir = $sub_dir;
        $this->fileObject = $file_object;
        $this->maxFileSize = $max_file_size;

        $this->mimes["supportedMimes"] = $supported_mimes;
        if ( !empty( $this->mimes["supportedMimes"] ) ) $this->mimes["checkMime"] = true;

        if ( !$this->validateFileObject() ) throw new Exception( "Invalid file object given" );
        if ( !$this->validateMime() ) throw new Exception( "Invalid file type uploaded" );
        if ( !$this->validateUploadFileSize() ) throw new Exception( "File size exceeded the limit" );
        if ( !$this->validateUploadPath() ) throw new Exception( "Failed to generate upload path" );
    }


    /**
     * Returns the temp file object uploaded using $_FILE
     * @return mixed
     */
    public function getFile()
    {
        return $this->fileObject["tmp_name"];
    }


    /**
     * Returns absolute file path for the uploaded file.
     * This is the system path originating from the root partition
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }


    /**
     * Returns the path relative to the uploads directory set by
     * Storage::setUploadDir() method.
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->relativePath;
    }


    /**
     * Check if uploaded file size is less than the given max size.
     * @throws Exception
     */
    public function validateUploadFileSize(): bool
    {
        if ( $this->maxFileSize == 0 ) {
            if ( $this->fileObject["size"] < self::getSupportedMaxUploadSize() ) return true;
            return false;
        }
        if ( $this->fileObject["size"] < $this->maxFileSize ) return true;
        return false;
    }


    /**
     * Returns the maximum upload size determined by the php
     * configuration
     * @return int
     */
    public static function getSupportedMaxUploadSize(): int
    {

        $max_upload_size = 1;
        $max_post_size = 1;

        $max_upload_string = ini_get( 'upload_max_filesize' );
        $max_post_string = ini_get( 'post_max_size' );

        $max_upload_unit = substr( $max_upload_string, -1 );
        $max_post_unit = substr( $max_post_string, -1 );

        if ( $max_upload_unit == "G" ) {
            $max_upload_size = (int)( $max_upload_string ) * 1024 * 1024 * 1024;
        } elseif ( $max_upload_unit == "M" ) {
            $max_upload_size = (int)( $max_upload_string ) * 1024 * 1024;
        }

        if ( $max_post_unit == "G" ) {
            $max_post_size = (int)( $max_post_string ) * 1024 * 1024 * 1024;
        } elseif ( $max_post_unit == "M" ) {
            $max_post_size = (int)( $max_post_string ) * 1024 * 1024;
        }

        return min( $max_upload_size, $max_post_size );
    }


    /**
     * Move the uploaded file from temp location and store it with given name,
     * to avoid name conflicts, a unique id is appended to the file while saving.
     * @param string $file_name
     * @param bool $use_mime_extension
     * @param bool $return_absolute_path
     * @return string
     * @throws Exception
     */
    public function storeUploadFile( string $file_name, bool $use_mime_extension = false, bool $return_absolute_path = false ): string
    {

        /*
         * Get an extension from the $newFileName (hello.jpg)
         * if useMimeExtension flag is set, then extension is fetched from mime
         */
        $ext = "";
        if ( $use_mime_extension ) {
            $ext = "." . $this->mimes["preferredExtension"];
        }

        /*
         * Generate the file path, absolute and relative
         */
        $this->generateUniqueFileName( $file_name, $ext );

        /*
         * Move the uploaded file to the generated absolute path
         */
        $result = move_uploaded_file( $this->fileObject["tmp_name"], $this->absolutePath );

        /*
         * Return the relative path, unless returnAbsolutePath flag is set
         */
        if ( $result ) {
            if ( !$return_absolute_path ) return $this->relativePath;
            return $this->absolutePath;
        }

        /*
         * If the flow reaches here, then obviously some error happened,
         * throw and exception
         */
        throw new Exception( "Failed to upload the file" );
    }


    /**
     * Checks if there is an extension in the uploaded file,
     * if user doesnt provide one while saving the uploaded file.
     * @throws Exception
     */
    private function validateExtension( $ext )
    {
        /* 1. check if user provided the extension, forcing to use that extension */
        if ( !empty( $ext ) ) return $ext;

        /* 2. if extension is determined from the uploaded file, use it */
        return $this->mimes["preferredExtension"];
    }


    /**
     * Checks if uploaded file falls within the specified mime types.
     * If no supported mimes are provided, mime check will be ignored,
     * and true will be returned.
     * @return bool
     */
    private function validateMime(): bool
    {
        if ( !$this->mimes["checkMime"] ) return true;

        $fileMime = $this->fileObject["type"];

        foreach ( $this->mimes["supportedMimes"] as $mime => $ext ) {
            if ( $mime == $fileMime ) {
                $this->mimes["preferredExtension"] = $ext;
                $this->mimes["hasPreferredExtension"] = true;
                return true;
            }
        }
        return false;
    }


    /**
     * Checks if given file object is indeed a file object from
     * $_FILES array.
     * @return bool
     */
    private function validateFileObject(): bool
    {
        if (
            array_key_exists( "name", $this->fileObject ) ||
            array_key_exists( "tmp_name", $this->fileObject ) ||
            array_key_exists( "type", $this->fileObject ) ||
            array_key_exists( "size", $this->fileObject )
        ) return true;
        return false;
    }

    /**
     * Check if given path exist before moving the uploaded
     * file from temp location. If upload path is not available,
     * this method will attempt to create one.
     * @return bool
     */
    private function validateUploadPath(): bool
    {
        $path = $this->baseDir . "/" . $this->subDir;
        if ( file_exists( $path ) ) return true;
        return mkdir( $path, "0777", true );
    }


    public function generateUniqueFileName( string $file_name, string $ext )
    {
        $uid = KeyGenerator::generateUID( "", "_" . $file_name );

        $this->relativePath = $this->subDir . "/" . $uid . $ext;
        $this->absolutePath = $this->baseDir . "/" . $this->relativePath;
    }


}
