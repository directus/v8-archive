<?php

namespace Directus\Filesystem;

use Directus\Application\Application;
use Directus\Util\ArrayUtils;
use Directus\Util\DateTimeUtils;
use Directus\Util\Formatting;

class Files
{
    /**
     * @var array
     */
    private $configs = [];

    /**
     * @var array
     */
    private $filesSettings = [];

    /**
     * @var FilesystemManager
     */
    private $filesystemManager = null;

    /**
     * @var array
     */
    private $defaults = [
        'description' => '',
        'tags' => '',
        'location' => ''
    ];

    /**
     * Hook Emitter Instance
     *
     * @var \Directus\Hook\Emitter
     */
    protected $emitter;

    public function __construct($filesystemManager, $configs, array $settings, $emitter)
    {
        $this->filesystemManager = $filesystemManager;
        $this->configs = $configs;
        $this->emitter = $emitter;
        $this->filesSettings = $settings;
    }

    // @TODO: remove exists() and rename() method
    // and move it to Directus\Filesystem Wrapper
    public function exists($path, $storage = null)
    {
        $filesystem = $this->getFilesystem($storage);

        return $filesystem->getAdapter()->has($path);
    }

    public function rename($path, $newPath, $replace = false, $storage = null)
    {
        $filesystem = $this->getFilesystem($storage);
        if ($replace === true && $filesystem->exists($newPath)) {
            $filesystem->getAdapter()->delete($newPath);
        }

        return $filesystem->getAdapter()->rename($path, $newPath);
    }

    public function delete($file, $storage = null)
    {
        if ($this->exists($file['filename'], $storage)) {
            $filesystem = $this->getFilesystem($storage);
            $this->emitter->run('files.deleting', [$file]);
            $filesystem->getAdapter()->delete($file['filename']);
            $this->emitter->run('files.deleting:after', [$file]);
        }
    }

    /**
     * Copy $_FILES data into directus media
     *
     * @param array $file $_FILES data
     * @param string|null $storage
     *
     * @return array directus file info data
     */
    public function upload(array $file, $storage = null)
    {
        $filePath = $file['tmp_name'];
        $fileName = $file['name'];

        $fileData = array_merge($this->defaults, $this->processUpload($filePath, $fileName, $storage));

        return [
            'type' => $fileData['type'],
            'name' => $fileData['name'],
            'title' => $fileData['title'],
            'tags' => $fileData['tags'],
            'description' => $fileData['caption'],
            'location' => $fileData['location'],
            'charset' => $fileData['charset'],
            'size' => $fileData['size'],
            'width' => $fileData['width'],
            'height' => $fileData['height'],
            //    @TODO: Returns date in ISO 8601 Ex: 2016-06-06T17:18:20Z
            //    see: https://en.wikipedia.org/wiki/ISO_8601
            'date_uploaded' => $fileData['date_uploaded'],// . ' UTC',
            'storage_adapter' => $fileData['storage_adapter']
        ];
    }

    /**
     * Get URL info
     *
     * @param string $url
     *
     * @return array
     */
    public function getLink($url)
    {
        // @TODO: use oEmbed
        // @TODO: better provider url validation
        // checking for 'youtube.com' for a valid youtube video is wrong
        // we can also be using youtube.com/img/a/youtube/image.jpg
        // which should fallback to ImageProvider
        // instead checking for a url with 'youtube.com/watch' with v param or youtu.be/
        $app = Application::getInstance();
        $embedManager = $app->getContainer()->get('embed_manager');
        try {
            $info = $embedManager->parse($url);
        } catch (\Exception $e) {
            $info = $this->getImageFromURL($url);
        }

        if ($info) {
            $info['upload_date'] = DateTimeUtils::nowInUTC()->toString();
            $info['storage_adapter'] = $this->getConfig('adapter');
            $info['charset'] = isset($info['charset']) ? $info['charset'] : '';
        }

        return $info;
    }

    /**
     * Gets the mime-type from the content type
     *
     * @param $contentType
     *
     * @return string
     */
    protected function getMimeTypeFromContentType($contentType)
    {
        // split the data type if it has charset or boundaries set
        // ex: image/jpg;charset=UTF8
        if (strpos($contentType, ';') !== false) {
            $contentType = array_map('trim', explode(';', $contentType));
        }

        if (is_array($contentType)) {
            $contentType = $contentType[0];
        }

        return $contentType;
    }

    /**
     * Get Image from URL
     *
     * @param $url
     * @return array
     */
    protected function getImageFromURL($url)
    {
        stream_context_set_default([
            'http' => [
                'method' => 'HEAD'
            ]
        ]);

        $urlHeaders = get_headers($url, 1);

        stream_context_set_default([
            'http' => [
                'method' => 'GET'
            ]
        ]);

        $info = [];

        $contentType = $this->getMimeTypeFromContentType($urlHeaders['Content-Type']);

        if (strpos($contentType, 'image/') === false) {
            return $info;
        }

        $urlInfo = parse_url_file($url);
        $content = file_get_contents($url);
        if (!$content) {
            return $info;
        }

        list($width, $height) = getimagesizefromstring($content);

        $data = 'data:' . $contentType . ';base64,' . base64_encode($content);
        $info['title'] = $urlInfo['filename'];
        $info['name'] = $urlInfo['basename'];
        $info['size'] = isset($urlHeaders['Content-Length']) ? $urlHeaders['Content-Length'] : 0;
        $info['type'] = $contentType;
        $info['width'] = $width;
        $info['height'] = $height;
        $info['data'] = $data;
        $info['charset'] = 'binary';

        return $info;
    }

    /**
     * Get base64 data information
     *
     * @param $data
     *
     * @return array
     */
    public function getDataInfo($data)
    {
        if (strpos($data, 'data:') === 0) {
            $parts = explode(',', $data);
            $data = $parts[1];
        }

        $info = $this->getFileInfoFromData(base64_decode($data));

        return array_merge(['data' => $data], $info);
    }

    /**
     * Copy base64 data into Directus Media
     *
     * @param string $fileData - base64 data
     * @param string $fileName - name of the file
     * @param bool $replace
     * @param string|null $storage
     *
     * @return array
     */
    public function saveData($fileData, $fileName, $replace = false, $storage = null)
    {
        $fileData = base64_decode($this->getDataInfo($fileData)['data']);

        // @TODO: merge with upload()
        $fileName = $this->getFileName($fileName, $replace !== true, $storage);

        $filePath = $this->getConfig('root', $storage) . '/' . $fileName;

        $this->emitter->run('files.saving', ['name' => $fileName, 'size' => strlen($fileData)]);
        $this->write($fileName, $fileData, $replace, $storage);
        $this->emitter->run('files.saving:after', ['name' => $fileName, 'size' => strlen($fileData)]);

        unset($fileData);

        $fileData = $this->getFileInfo($fileName, false, $storage);
        $fileData['title'] = Formatting::fileNameToFileTitle($fileName);
        $fileData['filename'] = basename($filePath);
        $fileData['upload_date'] = DateTimeUtils::nowInUTC()->toString();
        $fileData['storage_adapter'] = $storage;

        $fileData = array_merge($this->defaults, $fileData);

        return [
            'type' => $fileData['type'],
            'filename' => $fileData['filename'],
            'title' => $fileData['title'],
            'tags' => $fileData['tags'],
            'description' => $fileData['description'],
            'location' => $fileData['location'],
            'charset' => $fileData['charset'],
            'filesize' => $fileData['size'],
            'width' => $fileData['width'],
            'height' => $fileData['height'],
            'storage_adapter' => $fileData['storage_adapter']
        ];
    }

    /**
     * Save embed url into Directus Media
     *
     * @param array $fileInfo - File Data/Info
     * @param bool $replace
     * @param string|null $storage
     *
     * @return array - file info
     */
    public function saveEmbedData(array $fileInfo, $replace = false, $storage = null)
    {
        if (!array_key_exists('type', $fileInfo) || strpos($fileInfo['type'], 'embed/') !== 0) {
            return [];
        }

        $fileName = isset($fileInfo['filename']) ? $fileInfo['filename'] : md5(time()) . '.jpg';
        $imageData = $this->saveData($fileInfo['data'], $fileName, $replace, $storage);

        return array_merge($imageData, $fileInfo, [
            'filename' => $fileName
        ]);
    }

    /**
     * Get file info
     *
     * @param string $path - file path
     * @param bool $outside - if the $path is outside of the adapter root path.
     * @param string $storage
     *
     * @throws \RuntimeException
     *
     * @return array file information
     */
    public function getFileInfo($path, $outside = false, $storage = null)
    {
        if ($outside === true) {
            $buffer = file_get_contents($path);
        } else {
            $buffer = $this->getFilesystem($storage)->getAdapter()->read($path);
        }

        return $this->getFileInfoFromData($buffer);
    }

    public function getFileInfoFromData($data)
    {
        if (!class_exists('\finfo')) {
            throw new \RuntimeException('PHP File Information extension was not loaded.');
        }

        $finfo = new \finfo(FILEINFO_MIME);
        $type = explode('; charset=', $finfo->buffer($data));

        $mime = $type[0];
        $charset = $type[1];
        $typeTokens = explode('/', $mime);

        $info = [
            'type' => $mime,
            'format' => $typeTokens[1],
            'charset' => $charset,
            'size' => strlen($data),
            'width' => null,
            'height' => null
        ];

        if ($typeTokens[0] == 'image') {
            $meta = [];
            // @TODO: use this as fallback for finfo?
            $imageInfo = getimagesizefromstring($data, $meta);

            $info['width'] = $imageInfo[0];
            $info['height'] = $imageInfo[1];

            if (isset($meta['APP13'])) {
                $iptc = iptcparse($meta['APP13']);

                if (isset($iptc['2#120'])) {
                    $info['caption'] = $iptc['2#120'][0];
                }

                if (isset($iptc['2#005']) && $iptc['2#005'][0] != '') {
                    $info['title'] = $iptc['2#005'][0];
                }

                if (isset($iptc['2#025'])) {
                    $info['tags'] = implode(',', $iptc['2#025']);
                }

                $location = [];
                if (isset($iptc['2#090']) && $iptc['2#090'][0] != '') {
                    $location[] = $iptc['2#090'][0];
                }

                if (isset($iptc['2#095'][0]) && $iptc['2#095'][0] != '') {
                    $location[] = $iptc['2#095'][0];
                }

                if (isset($iptc['2#101']) && $iptc['2#101'][0] != '') {
                    $location[] = $iptc['2#101'][0];
                }

                $info['location'] = implode(', ', $location);
            }
        }

        unset($data);

        return $info;
    }

    /**
     * Get file settings
     *
     * @param string $key - Optional setting key name
     *
     * @return mixed
     */
    public function getSettings($key = '')
    {
        if (!$key) {
            return $this->filesSettings;
        } else if (array_key_exists($key, $this->filesSettings)) {
            return $this->filesSettings[$key];
        }

        return false;
    }

    /**
     * Get filesystem config
     *
     * @param string $key - Optional config key name
     * @param string|null $storage
     *
     * @return mixed
     */
    public function getConfig($key = '', $storage = null)
    {
        if ($storage) {
            $config = ArrayUtils::get($this->configs, $storage);
        } else {
            $config = reset($this->configs);
        }

        if (!$key) {
            return $config;
        } else if (array_key_exists($key, $config)) {
            return $config[$key];
        }

        return false;
    }

    /**
     * Writes the given data in the given location
     *
     * @param $location
     * @param $data
     * @param bool $replace
     * @param string|null $storage
     *
     * @throws \RuntimeException
     */
    public function write($location, $data, $replace = false, $storage = null)
    {
        $this->getFilesystem($storage)->write($location, $data, $replace);
    }

    /**
     * Reads and returns data from the given location
     *
     * @param $location
     * @param string|null $storage
     *
     * @return bool|false|string
     *
     * @throws \Exception
     */
    public function read($location, $storage = null)
    {
        try {
            return $this->getFilesystem($storage)->getAdapter()->read($location);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Creates a new file for Directus Media
     *
     * @param string $filePath
     * @param string $targetName
     * @param string|null $storage
     *
     * @return array file info
     */
    private function processUpload($filePath, $targetName, $storage = null)
    {
        // set true as $filePath it's outside adapter path
        // $filePath is on a temporary php directory
        $fileData = $this->getFileInfo($filePath, true);
        $mediaPath = $this->getFilesystem($storage)->getPath();

        $fileData['title'] = Formatting::fileNameToFileTitle($targetName);

        $targetName = $this->getFileName($targetName, true, $storage);
        $finalPath = rtrim($mediaPath, '/') . '/' . $targetName;
        $data = file_get_contents($filePath);

        $this->emitter->run('files.saving', ['name' => $targetName, 'size' => strlen($data)]);
        $this->write($targetName, $data);
        $this->emitter->run('files.saving:after', ['name' => $targetName, 'size' => strlen($data)]);

        $fileData['name'] = basename($finalPath);
        $fileData['date_uploaded'] = DateTimeUtils::nowInUTC()->toString();
        $fileData['storage_adapter'] = $storage;

        return $fileData;
    }

    /**
     * Sanitize title name from file name
     *
     * @param string $fileName
     *
     * @return string
     */
    private function sanitizeName($fileName)
    {
        // do not start with dot
        $fileName = preg_replace('/^\./', 'dot-', $fileName);
        $fileName = str_replace(' ', '_', $fileName);

        return $fileName;
    }

    /**
     * Add suffix number to file name if already exists.
     *
     * @param string $fileName
     * @param string $targetPath
     * @param int $attempt - Optional
     * @param string|null $storage
     *
     * @return bool
     */
    private function uniqueName($fileName, $targetPath, $attempt = 0, $storage = null)
    {
        $info = pathinfo($fileName);
        // @TODO: this will fail when the filename doesn't have extension
        $ext = $info['extension'];
        $name = basename($fileName, ".$ext");

        $name = $this->sanitizeName($name);

        $fileName = "$name.$ext";
        if ($this->getFilesystem($storage)->exists($fileName)) {
            $matches = [];
            $trailingDigit = '/\-(\d)\.(' . $ext . ')$/';

            if (preg_match($trailingDigit, $fileName, $matches)) {
                // Convert "fname-1.jpg" to "fname-2.jpg"
                $attempt = 1 + (int)$matches[1];
                $newName = preg_replace($trailingDigit, "-{$attempt}.$ext", $fileName);
                $fileName = basename($newName);
            } else {
                if ($attempt) {
                    $name = rtrim($name, $attempt);
                    $name = rtrim($name, '-');
                }
                $attempt++;
                $fileName = $name . '-' . $attempt . '.' . $ext;
            }

            return $this->uniqueName($fileName, $targetPath, $attempt, $storage);
        }

        return $fileName;
    }

    /**
     * Get file name based on file naming setting
     *
     * @param string $fileName
     * @param bool $unique
     * @param string|null $storage
     *
     * @return string
     */
    private function getFileName($fileName, $unique = true, $storage = null)
    {
        switch ($this->getSettings('file_naming')) {
            case 'file_hash':
                $fileName = $this->hashFileName($fileName);
                break;
        }

        if ($unique) {
            $fileName = $this->uniqueName($fileName, $this->getFilesystem($storage)->getPath(), 0, $storage);
        }

        return $fileName;
    }

    /**
     * Hash file name
     *
     * @param string $fileName
     *
     * @return string
     */
    private function hashFileName($fileName)
    {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileHashName = md5(microtime() . $fileName);
        return $fileHashName . '.' . $ext;
    }

    /**
     * Get string between two string
     *
     * @param string $string
     * @param string $start
     * @param string $end
     *
     * @return string
     */
    private function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Get URL info
     *
     * @param string $link
     *
     * @return array
     */
    public function getLinkInfo($link)
    {
        $fileData = [];
        $width = 0;
        $height = 0;

        $urlHeaders = get_headers($link, 1);
        $contentType = $this->getMimeTypeFromContentType($urlHeaders['Content-Type']);

        if (strpos($contentType, 'image/') === 0) {
            list($width, $height) = getimagesize($link);
        }

        $urlInfo = pathinfo($link);
        $linkContent = file_get_contents($link);
        $url = 'data:' . $contentType . ';base64,' . base64_encode($linkContent);

        $fileData = array_merge($fileData, [
            'type' => $contentType,
            'name' => $urlInfo['basename'],
            'title' => $urlInfo['filename'],
            'charset' => 'binary',
            'size' => isset($urlHeaders['Content-Length']) ? $urlHeaders['Content-Length'] : 0,
            'width' => $width,
            'height' => $height,
            'data' => $url,
            'url' => ($width) ? $url : ''
        ]);

        return $fileData;
    }

    /**
     * Returns the given storage or default filesystem
     *
     * @param $storage
     *
     * @return Filesystem
     */
    protected function getFilesystem($storage)
    {
        if ($storage) {
            $filesystem = $this->filesystemManager->get($storage);
        } else {
            $filesystem = $this->filesystemManager->getDefault();
        }

        return $filesystem;
    }
}
