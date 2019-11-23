<?php

namespace Directus\Services;

use Exception;
use Zend\Db\Sql\Select;
use Directus\Util\ArrayUtils;
use Directus\Filesystem\Thumbnail;
use Directus\Filesystem\Filesystem;
use Directus\Application\Container;
use function Directus\get_file_root_url;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\Exception\ItemNotFoundException;
use function Directus\get_directus_thumbnail_settings;
use Intervention\Image\ImageManagerStatic as Image;

class AssetService extends AbstractService
{

    /**
     * @var string
     */
    protected $collection;

    /**
     * @var string
     */
    protected $thumbnailParams;

    /**
     * @var string
     */
    protected $thumbnailDir;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * Main Filesystem
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Thumbnail Filesystem
     *
     * @var Filesystem
     */
    private $filesystemThumb;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->collection = SchemaManager::COLLECTION_FILES;
        $this->filesystem =$this->container->get('filesystem');
        $this->filesystemThumb =$this->container->get('filesystem_thumb');
        $this->config = get_directus_thumbnail_settings();
        $this->thumbnailParams = [];
    }

    public function getAsset($fileHashId, array $params = [])
    {
        $tableGateway = $this->createTableGateway($this->collection);
        $select = new Select($this->collection);
        $select->columns(['filename']);
        $select->where([
           'hash_id' => $fileHashId
        ]);
        $select->limit(1);
        $result = $tableGateway->ignoreFilters()->selectWith($select);

        if ($result->count() === 0) {
            throw new ItemNotFoundException();
        }

        $file = $result->current()->toArray();
        if(count($params) === 0) {
           $url=get_file_root_url();
           $img = $this->filesystem->read($file['filename']);
           $result=[];
           $result['mimeType']=Image::make($this->filesystem->read($file['filename']))->mime();;
           $result['image']=isset($img) && $img ? $img : null; 
           return $result;
        }
        else {
           $this->fileName=$file['filename'];
           try {
               return $this->getThumbnail($params);
           }
           catch(Exception $e)
           {
               http_response_code(422);
               echo json_encode([
                   'error' => [
                       'code' => 4,
                       'message' => $e->getMessage()
                   ]
               ]);
               exit(0);
           }
        }
    }

    public function getThumbnail($params)
    {
        $this->validateThumbnailParams($params);

        if (! $this->filesystem->exists($this->fileName)) {
            throw new Exception($this->fileName . ' does not exist.'); 
        }
        $validateWhiteList= isset($params['key']) ? 'thumbnail_whitelist_system' : 'thumbnail_whitelist';
        $this->validateThumbnailWhitelist($params,$validateWhiteList);
        
        $otherParams=$params;
        unset($otherParams['width'],$otherParams['height'],$otherParams['fit'],$otherParams['quality']);
        if(isset($params['key'])) {
          unset($otherParams['key']);
        }
        
        $paramsString=isset($params['key']) ? ',key'.$params['key'] : '';
        if(count($otherParams) > 0) {
            ksort($otherParams);
            foreach($otherParams as $key => $value) {
                $paramsString .= ','. substr($key, 0, 1) . $value;
            }
        }

        $this->thumbnailDir = 'w'.$params['width'] . ',h' . $params['height'] . ',f' . $params['fit'] . ',q' . $params['quality'].$paramsString;
        
        try {
            $image=$this->getExistingThumbnail();
            if (!$image) {
                switch ($this->thumbnailParams['fit']) {
                    case 'contain':
                        $image = $this->contain();
                        break;
                    case 'crop':
                    default:
                        $image = $this->crop();
                }
            }
            $result['mimeType']=$this->getThumbnailMimeType($this->thumbnailDir,$this->fileName);
            $result['image']=$image;
            return $result;
        }
        catch (Exception $e) {
          $this->getDefaultThumbnail();
        }
    }

    /**
     * validate params against thumbnail whitelist
     *
     * @param array $params
     * @throws Exception
     * @return boolean
    */

    public function validateThumbnailWhitelist($params,$validateWhiteList)
    {
        $thumbnailWhitelistEnabled=ArrayUtils::get($this->getConfig(), 'thumbnail_whitelist_enabled');
        if ( 
           ($thumbnailWhitelistEnabled && $validateWhiteList == 'thumbnail_whitelist') || 
           $validateWhiteList == 'thumbnail_whitelist_system'
           )
        {
            $thumbnailWhitelist=ArrayUtils::get($this->getConfig(), $validateWhiteList);
            $thumbnailWhitelist=json_decode($thumbnailWhitelist,true);
            $result=false;
            foreach($thumbnailWhitelist as $key=>$value) {
                if(
                  $value['width'] == $params['width'] && 
                  $value['height'] == $params['height'] &&
                  $value['fit'] == $params['fit'] &&
                  $value['quality'] == $params['quality'] 
                )
                {
                  if($validateWhiteList == 'thumbnail_whitelist_system') {
                      if($value['key'] == $params['key']) {
                         $result=true;
                      } 
                  }
                  else {
                    $result = true;
                  }
                }
            }
            if(!$result){
                throw new Exception(sprintf('Invalid Params.'));
            }
        }
    }

    /**
     * validate params and file extensions and return it
     *
     * @param string $thumbnailUrlPath
     * @param array $params
     * @throws Exception
     * @return array
     */
    public function validateThumbnailParams($params)
    {
        // set thumbnail parameters
        $this->thumbnailParams = [
            'fit' => filter_var(isset($params['fit']) ? $params['fit'] : '', FILTER_SANITIZE_STRING),
            'height' => filter_var(isset($params['height']) ? $params['height'] : '', FILTER_SANITIZE_NUMBER_INT),
            'quality' => filter_var(isset($params['quality']) ? $params['quality'] : '', FILTER_SANITIZE_STRING),
            'width' => filter_var(isset($params['width']) ? $params['width'] : '', FILTER_SANITIZE_NUMBER_INT),
        ];
        
        $this->validate(
                        [
                            'width'     =>   $this->thumbnailParams['width'],
                            'height'    =>   $this->thumbnailParams['height'],
                            'quality'   =>   $this->thumbnailParams['quality'],
                            'fit'       =>   $this->thumbnailParams['fit']
                        ],
                        [
                            'width'     =>  'required',
                            'height'    =>  'required',
                            'quality'   =>  'required',
                            'fit'       =>  'required'
                        ]);

        $ext = pathinfo($this->fileName, PATHINFO_EXTENSION);
        $name = pathinfo($this->fileName, PATHINFO_FILENAME);
        if (! $this->isSupportedFileExtension($ext)) {
            throw new Exception('Invalid file extension.');
        }

        $this->thumbnailParams['fileExt'] = $ext;
        $this->thumbnailParams['fileName'] = $this->fileName;
        $this->thumbnailParams['format'] = strtolower(ArrayUtils::get($params, 'format') ?: $ext);

        if (! $this->isSupportedFileExtension($this->thumbnailParams['format'])) {
            throw new Exception('Invalid file format.');
        }

        if(
            $this->thumbnailParams['format'] !== NULL &&
            strtolower($ext) !== $this->thumbnailParams['format'] &&
            !(
                ($this->thumbnailParams['format'] === 'jpeg' || $this->thumbnailParams['format'] === 'jpg') &&
                (strtolower($ext) === 'jpeg' || strtolower($ext) === 'jpg')
            )
        ) {
            $this->thumbnailParams['thumbnailFileName'] = $name . '.' .$this->thumbnailParams['format'];
        } else {
            $this->thumbnailParams['thumbnailFileName'] = $this->fileName;
        }
    }

    /**
     * Check if given file extension is supported
     *
     * @param int $ext
     * @return boolean
     */
    public function isSupportedFileExtension($ext)
    {
        return in_array(strtolower($ext), $this->getSupportedFileExtensions());
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
     * Merge file and thumbnailer config settings and return
     *
     * @throws Exception
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Return thumbnail as data
     *
     * @throws Exception
     * @return string|null
    */
    public function getExistingThumbnail()
    {
        try {
            if( $this->filesystemThumb->exists($this->thumbnailDir . '/' . $this->thumbnailParams['thumbnailFileName']) ) {
                $img = $this->filesystemThumb->read($this->thumbnailDir . '/' . $this->thumbnailParams['thumbnailFileName']);
            }
            return isset($img) && $img ? $img : null;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Replace PDF files with a JPG thumbnail
     * @throws Exception
     * @return string image content
     */
    public function load () {
        $content = $this->filesystem->read($this->fileName);
        $ext = pathinfo($this->fileName, PATHINFO_EXTENSION);
        if (Thumbnail::isNonImageFormatSupported($ext)) {
            $content = Thumbnail::createImageFromNonImage($content);
        }
        return Image::make($content);
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
            $img = $this->load();
            $img->resize($this->thumbnailParams['width'],$this->thumbnailParams['height'], function ($constraint) {});
            $encodedImg = (string) $img->encode($this->thumbnailParams['format'], ($this->thumbnailParams['quality'] ? $this->thumbnailParams['quality'] : null));
            $this->filesystemThumb->write($this->thumbnailDir . '/' . $this->thumbnailParams['thumbnailFileName'], $encodedImg);

            return $encodedImg;
        }
        catch (Exception $e) {
            throw $e;
        }
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
            $img = $this->load();
            $img->fit($this->thumbnailParams['width'],$this->thumbnailParams['height'], function($constraint){});
            $encodedImg = (string) $img->encode($this->thumbnailParams['format'], ($this->thumbnailParams['quality'] ? $this->thumbnailParams['quality'] : null));
            $this->filesystemThumb->write($this->thumbnailDir . '/' . $this->thumbnailParams['thumbnailFileName'], $encodedImg);
       
            return $encodedImg;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function getDefaultThumbnail()
    {
        $basePath=$this->container->get('path_base');
        $filePath = ArrayUtils::get($this->config, 'thumbnail_not_found_location');
        if (is_string($filePath) && !empty($filePath) && $filePath[0] !== '/') {
            $filePath = $basePath . '/' . $filePath;
        }
    
        if (file_exists($filePath)) {
            $mime = image_type_to_mime_type(exif_imagetype($filePath));
            echo file_get_contents($filePath);
            exit(0);
        } else {
             return 1;
        }
    }

    public function getThumbnailMimeType($path,$fileName)
    {
        try {
            if($this->filesystemThumb->exists($path . '/' . $fileName) ) {
                if(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) === 'webp') {
                    return 'image/webp';
                }
                $img = Image::make($this->filesystemThumb->read($path. '/' . $fileName));
                return $img->mime();
            }
            return 'application/octet-stream';
        }

        catch (Exception $e) {
            throw $e;
        }
    }
}
