# Directus Thumbnailer 
#### Dynamic Image Thumbnailing System (Standalone Version)

The Directus Thumbnailer is a dynamic image thumbnailing system used by the Directus framework. The functionality can summerized in the following steps:
  - User requests a specific thumbnail of a file using a URL syntax
  - The request is routed through an .htaccess file (or nginx equivalent) to the PHP thumbnailer file that checks if the thumbnail exists
  - If it does exist, it's simply returned
  - If it does NOT exist, then we check the config's Thumbnail Whitelist (listing of allowed thumbnail sizes), generate the thumbnail (using GD Library), and return it
  - For example, `directus.example.com/thumbnail/200/300/crop/best/original-file-name.jpg` will result in a thumbnail that is 200px wide and 300px tall is cropped and created. If you remove the `crop` or `fit` parameter the thumbnail is fit within the bounds (aspect ratio maintained). The image is NEVER stretched. An optional "quality" param is also available, in this case, `best`.
  - The url path will be mirrored when the thumbnail is created.  In the example, `directus.example.com/thumbnail/200/300/crop/best/original-file-name.jpg`, the thumbnail will be created in `thumbnail/200/300/crop/best/original-file-name.jpg`


## Installation & Config
The thumbnailer utilizes Intervention/Image (http://image.intervention.io/) for thumbnail processing.  To get started:
- Install Intervention/Image in the Directus root by running  `composer require intervention/image` 
- Clone the thumbnailer in the Directus root by running `git clone git@github.com:directus/thumbnailer.git`
- Modify settings as needed in. `config.json`

## Examples
The follwing examples can executed by copying the url into the browser.

#### Crop Best 200x300
* URL: `directus.example.com/thumbnail/200/300/crop/best/my-file.jpg`
* RESULT: Cropped file of best quality
* FILE LOCATION: `thumbnail/200/300/crop/best/my-file.jpg`

#### 200x300
* URL: `directus.example.com/thumbnail/200/300/my-file.jpg`
* RESULT: Cropped (default) file of good (default) quality
* FILE LOCATION: `thumbnail/200/300/my-file.jpg`

#### Contain 100x100
* URL: `directus.example.com/thumbnail/100/100/contain/my-file.jpg`
* RESULT: Resized file (aspect ratio preserved) of good (default) quality
* FILE LOCATION: `thumbnail/100/100/contain/my-file.jpg`


## Files
The system consists of the following files:
#### index.php
This is the main controller.  It accepts an http request and uses the thumbnailer to create thumb, or return a `not found` image in the case that one can not be created.
#### config.json
This file contains settings for the thumbnailer, including acceptable file extensions, dimensions and thumbnail location.
#### .htaccess
Redirects to the thumbnailer if a thumbnail can not be found.  
#### Thumbnailer.php (class)
This file contains all the core functionality for the thumbnailer and consists of the following functions:
##### public function __construct( $options = [] )
Constructor.
>**(array) $options['thumbnailUrlPath']** - The (relative) url to the thumbnail. In the example 'http://directus.example.com/thumbnail/200/300/crop/best/original-file-name.jpg', the path would be `200/300/crop/best/original-file-name.jpg`

>**(array) $options['configFilePath']** - Path to the `settings.json` file.  
##### public function __get($key)
PHP magic getter implementation to retrieve extracted thumbnail params as well as other object properties.
>**(string) $key** - Will return either an existing object property, or a `thumbnailParams` setting, i.e - `$thumbnailer->width`   
##### public function get()
Return the image object as a data string ready for display.
##### public function contain()
Resizes the image to fill the container whilst preserving its aspect-ratio with option to pad the container with a background (see `config.json`, `supportedActions` for options).  Please also see http://image.intervention.io/api/resize and https://css-tricks.com/almanac/properties/o/object-fit/.
##### public function crop()
Resize the image to fill the height and width the container, maintaining the aspect ratio and cropping the image as needed.  The image can be optionally positioned (see `config.json`, `supportedActions` for options).  Please also see http://image.intervention.io/api/fit and https://css-tricks.com/almanac/properties/o/object-fit/.
##### public function extractThumbnailParams($thumbnailUrlPath)
Extracts dimensions, action, and qualtiy from url.
>**(string) $thumbnailUrlPath** - The (relative) url to the thumbnail. In the example 'http://directus.example.com/thumbnail/200/300/crop/best/original-file-name.jpg', the path would be `200/300/crop/best/original-file-name.jpg`
##### public function translateQuality($qualityText)
Converts a textual quality, i.e - good, to a number used by the crop/fit functions.
>**(string) $qualityText** - Text to be translated to a quality number, i.e - 'good' = 50, 'best' = 100
##### public function isSupportedFileExtension($ext)
>**(string) $ext** - File extension.
##### public function getSupportedFileExtensions()
Returns supported file extensions as defined in `config.json`.
##### public function isSuppportedThumbnailDimension($width, $height)
Checks if width and height combination is acceptable, as defined in `config.json`.
>**(int) $width** - Width of requested thumbnail.

>**(int) $height** - Height of requested thumbnail.
##### public function getSupportedThumbnailDimensions()
Returns acceptable dimensions, as defined in `config.json`
##### public function isSupportedAction($action)
>**(string) $action** - Action - `crop` or `contain`.
##### public function getSupportedActions()
Returns supported actions as defined in `config.json`.
##### public function isSupportedQualityTag($qualityTag)
>**(string) $action** - Quality tag, i.e - `best`, `good`.
##### public function getSupportedQualityTags()
Returns supported quality tags as defined in `config.json`.
##### public function getSupportedActionOptions()
Returns supported action options as defined in `config.json`.
##### public function getConfig()
Returns thumbnailer config merged with Directus file config.
