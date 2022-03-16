<?php


namespace App\Core\Services;


use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

/**
 * Class ImageProcessor
 * @package App\Core\Services
 *
 * This class is a wrapper class utilizing Intervention Image library to
 * process images.
 *
 */
class ImageProcessor
{


    private Image $imageObject;
    private Uploader $uploaderInstance;


    /**
     * Creates an ImageProcessor object from uploader instance.
     *
     * @param Uploader $uploader_instance
     * @return ImageProcessor
     */
    public static function createImageObject( Uploader $uploader_instance ): ImageProcessor
    {
        $processor = new self();
        $processor->createImageFromUploader( $uploader_instance );

        return $processor;
    }


    /**
     * Crop and resize the image in a smart way. width and height specify the output
     * file dimension. if height is not specified, width will be used (square image)
     * Position is where the image is cropped from. Default value is 'center'.
     *
     * http://image.intervention.io/api/fit
     *
     * @param int $width
     * @param int|null $height
     * @param string $position
     * @return $this
     */
    public function fit( int $width, int $height = null, string $position = "center" ): ImageProcessor
    {
        $this->imageObject->fit( $width, $height, function ( $constraint ) {
            $constraint->upsize();
        }, $position );

        return $this;
    }


    /**
     * Resize the image by width. Height will be automatically calculated
     * based on the aspect ratio of the image.
     *
     * @param int $width
     * @param bool $upsize - false by default. when true, image can be upsized.
     * @return $this
     */
    public function resizeByWidth( int $width, bool $upsize = false ): ImageProcessor
    {

        $this->imageObject->resize( $width, null, function ( $constraint ) use ( &$upsize ) {
            $constraint->aspectRatio();
            if ( !$upsize ) $constraint->upsize();
        } );


        return $this;
    }


    /**
     * Resize the image by height. Width will be automatically calculated based
     * on the aspect ratio of the image.
     *
     * @param int $height
     * @param bool $upsize - false by default. when true, image can be upsized.
     * @return $this
     */
    public function resizeByHeight( int $height, bool $upsize ): ImageProcessor
    {

        $this->imageObject->resize( null, $height, function ( $constraint ) use ( &$upsize ) {
            $constraint->aspectRatio();
            if ( !$upsize ) $constraint->upsize();
        } );

        return $this;
    }


    /**
     * Resize image by height and width in a way the largest side will fit with in the limit.
     *
     * @param int $width
     * @param int $height
     * @param bool $hard
     * @param bool $upsize
     * @return $this
     */
    public function resize( int $width, int $height, bool $hard = false, bool $upsize = false ): ImageProcessor
    {

        $this->imageObject->resize( $width, $height, function ( $constraint ) use ( &$hard, &$upsize ) {
            if ( !$hard ) $constraint->aspectRatio();
            if ( !$upsize ) $constraint->upsize();
        } );

        return $this;
    }


    /**
     * Saves the image with specified format and quality. if quality is not specified, 100 will be used,
     * if format is not specified, file mime format will be used.
     *
     * @param string $fileName
     * @param int|null $quality
     * @param string|null $format
     */
    public function save( string $fileName, int $quality = null, string $format = null )
    {

        $this->uploaderInstance->generateUniqueFileName( $fileName, "" );
        $this->imageObject->save( $this->uploaderInstance->getAbsolutePath(), $quality, $format );
    }


    /**
     * Returns the relative path specified by the uploader with
     * generated file name
     *
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->uploaderInstance->getRelativePath();
    }


    /**
     * Populate private instances of uploaderInstance and imageObject,
     * called by createImageObject function
     * @param Uploader $uploader_instance
     */
    private function createImageFromUploader( Uploader $uploader_instance )
    {
        $this->uploaderInstance = $uploader_instance;
        $this->imageObject = ImageManagerStatic::make( $this->uploaderInstance->getFile() );
    }

}
