<?php
use Directus\Bootstrap;
use Directus\Util\ArrayUtils;
use Directus\Filesystem\Thumbnail;
use Intervention\Image\ImageManagerStatic as Image;
use Exception;

class Thumbnailer {

    /**
     * Directus app instance
     *
     * @var Directus\Application\Application
     */
    private $app = null;

    /**
     * Thumbnail params extracted from url
     *
     * @var array
     */
    private $thumbnailParams = [];
    
    /**
     * Base directory for thumbnail
     * 
     * @var string
     */
    private $thumbnailDir = null;
    
    /**
     * Thumbnailer config
     * 
     * @var array
     */
    private $configFilePath;

    /**
     * Constructor
     *
     * @param string $thumbnailUrlPath            
     */
    public function __construct( $options = [] )
    {
        try {
            $this->app = Bootstrap::get('app');
            $this->configFilePath = ArrayUtils::get($options, 'configFilePath', __DIR__ . '/config.json');
            $this->thumbnailParams = $this->extractThumbnailParams(ArrayUtils::get($options, 'thumbnailUrlPath'));

            // check if dimensions are supported
            if (! $this->isSupportedThumbnailDimension($this->width, $this->height)) {
                throw new Exception('Invalid dimensions.');
            }
            
            // check if action is supported
            if ( $this->action && ! $this->isSupportedAction($this->action)) {
                throw new Exception('Invalid action.');
            }
            
            // check if quality is supported
            if ( $this->quality && ! $this->isSupportedQualityTag($this->quality)) {
                throw new Exception('Invalid quality.');
            }           
            
            $this->thumbnailDir = ArrayUtils::get($this->getConfig(), 'thumbnailDirectory',__DIR__) . '/' . $this->width . '/' . $this->height . ($this->action ? '/' . $this->action : '') . ($this->quality ? '/' . $this->quality : '');
        }
        
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Magic getter for thumbnailParams
     *
     * @param string $key            
     * @return string|int
     */
    public function __get($key)
    {
        if( in_array($key, ['thumbnailDir', 'configFilePath'])) {
            return $this->$key;
        }
        
        return ArrayUtils::get($this->thumbnailParams, $key, null);
    }

    /**
     * Create thumbnail from image and `crop`
     * http://image.intervention.io/api/fit
     * https://css-tricks.com/almanac/properties/o/object-fit/
     *
     * @throws Exception
     * @return string
     */
    public function crop()
    {        
        try {
            // action options
            $options = $this->getSupportedActionOptions($this->action);
            
            // open file a image resource
            $img = Image::make(ArrayUtils::get($this->getConfig(), 'root') . '/' . $this->fileName);
            
            // create directory if needed
            if (! file_exists($this->thumbnailDir)) {
                mkdir($this->thumbnailDir, 0755, true);
            }        

            // resize/crop image
            $img->fit($this->width, $this->height, function($constraint){}, ArrayUtils::get($options, 'position', 'center'))->save($this->thumbnailDir . '/' . $this->fileName, ($this->quality ? $this->translateQuality($this->quality) : null));
            
            return $this->thumbnailDir . '/' . $this->fileName;
        }
        
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create thumbnail from image and `contain` 
     * http://image.intervention.io/api/resize
     * https://css-tricks.com/almanac/properties/o/object-fit/ 
     *
     * @throws Exception
     * @return string
     */
    public function contain()
    {        
        try {            
            // action options
            $options = $this->getSupportedActionOptions($this->action);
            
            // open file a image resource
            $img = Image::make(ArrayUtils::get($this->getConfig(), 'root') . '/' . $this->fileName);
            
            // create directory if needed
            if (! file_exists($this->thumbnailDir)) {
                mkdir($this->thumbnailDir, 0755, true);
            }
            
            // crop image
            $img->resize($this->width, $this->height, function ($constraint) {
                $constraint->aspectRatio();
            });
            
            if( ArrayUtils::get($options, 'resizeCanvas')) {
                $img->resizeCanvas($this->width, $this->height, ArrayUtils::get($options, 'position', 'center'), ArrayUtils::get($options, 'resizeRelative', false), ArrayUtils::get($options, 'canvasBackground', [255, 255, 255, 0]));
            }
              
            // save image
            $img->save($this->thumbnailDir . '/' . $this->fileName, ($this->quality ? $this->translateQuality($this->quality) : null));
            
            return $this->thumbnailDir . '/' . $this->fileName;
        }
        
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Parse url and extract thumbnail params
     *
     * @param string $thumbnailUrlPath            
     * @throws Exception
     * @return array
     */
    public function extractThumbnailParams($thumbnailUrlPath)
    {
        try {
            if ($this->thumbnailParams) {
                return $this->thumbnailParams;
            }
            
            $urlSegments = explode('/', $thumbnailUrlPath);
            
            if (! $urlSegments) {
                throw new Exception('Invalid thumbnailUrlPath.');
            }
            
            // pop off the filename
            $fileName = ArrayUtils::pop($urlSegments);
            
            // make sure filename is valid
            $info = pathinfo($fileName);
            if (! in_array(ArrayUtils::get($info, 'extension'), $this->getSupportedFileExtensions())) {
                throw new Exception('Invalid file extension.');
            }
            
            $thumbnailParams = [
                'fileName' => $fileName,
                'fileExt' => ArrayUtils::get($info, 'extension')
            ];
            
            foreach ($urlSegments as $segment) {
                
                if (! $segment) continue;
                
                // extract width and height
                if (is_numeric($segment)) {
                    
                    if (! ArrayUtils::get($thumbnailParams, 'width')) {
                        ArrayUtils::set($thumbnailParams, 'width', $segment);
                    } else if (! ArrayUtils::get($thumbnailParams, 'height')) {
                        ArrayUtils::set($thumbnailParams, 'height', $segment);
                    }
                }                 

                // extract action and quality
                else {
                    
                    if (! ArrayUtils::get($thumbnailParams, 'action')) {
                        ArrayUtils::set($thumbnailParams, 'action', $segment);
                    } else if (! ArrayUtils::get($thumbnailParams, 'quality')) {
                        ArrayUtils::set($thumbnailParams, 'quality', $segment);
                    }
                }
            }
            
            // validate
            if (! ArrayUtils::contains($thumbnailParams, [
                'width',
                'height'
            ])) {
                throw new Exception('No height or width provided.');
            }
            
            // set default action, if needed
            if (! ArrayUtils::exists($thumbnailParams, 'action')) {
                ArrayUtils::set($thumbnailParams, 'action', null);
            }
            
            // set quality to null, if needed
            if (! ArrayUtils::exists($thumbnailParams, 'quality')) {
                ArrayUtils::set($thumbnailParams, 'quality', null);
            }
            
            return $thumbnailParams;
        } 

        catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Translate quality text to number and return
     *
     * @param string $qualityText            
     * @return number
     */
    public function translateQuality($qualityText)
    {
        return ArrayUtils::get($this->getSupportedQualityTags(), $qualityText, 50);
    }

    /**
     * Check if given file extension is supported
     *
     * @param int $ext
     * @return boolean
     */
    public function isSupportedFileExtension($ext)
    {
        return in_array($ext, $this->getSupportedFileExtensions());
    }

    /**
     * Return supported image file types
     *
     * @return array
     */
    public function getSupportedFileExtensions()
    {
        return Thumbnail::getFormatsSupported();
    }

    /**
     * Check if given dimension is supported
     *
     * @param int $width            
     * @param int $height            
     * @return boolean
     */
    public function isSupportedThumbnailDimension($width, $height)
    {
        return in_array($width . 'x' . $height, $this->getSupportedThumbnailDimensions());
    }

    /**
     * Return supported thumbnail file dimesions
     *
     * @return array
     */
    public function getSupportedThumbnailDimensions()
    {
        return ArrayUtils::get($this->getConfig(), 'supportedThumbnailDimensions');
    }

    /**
     * Check if given action is supported
     *
     * @param int $action        
     * @return boolean
     */
    public function isSupportedAction($action)
    {
        return ArrayUtils::has($this->getSupportedActions(), $action);
    }

    /**
     * Return supported actions
     *
     * @return array
     */
    public function getSupportedActions()
    {
        return ArrayUtils::get($this->getConfig(), 'supportedActions');
    }

    /**
     * Check if given quality is supported
     *
     * @param int $action        
     * @return boolean
     */
    public function isSupportedQualityTag($qualityTag)
    {
        return ArrayUtils::has($this->getSupportedQualityTags(), $qualityTag);
    }

    /**
     * Return supported thumbnail qualities
     *
     * @return array
     */
    public function getSupportedQualityTags()
    {
        return ArrayUtils::get($this->getConfig(), 'supportedQualityTags');
    }
    
    /**
     * Return supported action options as set in config
     * 
     * @param string $action
     */
    public function getSupportedActionOptions($action) 
    {
        return ArrayUtils::get($this->getConfig(), 'supportedActions.' . $action . '.options');
    }
    
    /**
     * Merge file and thumbnailer config settings and return
     * 
     * @throws Exception
     * @return array
     */
    public function getConfig()
    {
        try {            
            $config = json_decode(file_get_contents($this->configFilePath), true);
            
            return array_merge($this->app->files->getConfig(), $config);
        }
        
        catch (Exception $e) {
            throw $e;
        }
    }
}