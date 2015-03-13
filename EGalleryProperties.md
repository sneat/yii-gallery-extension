# Gallery Properties #

Properties documented in [PHPDoc](http://www.phpdoc.org/) format.

```
/**
 * @var string the name of the gallery
 */
public $name = 'Test gallery';
```
```
/**
 * The path to the directory containing the albums.
 * A leading '/' means absolute URL.
 * No leading '/' means relative to Yii's bootstrap index.php file.
 * Do not include a trailing '/'.
 *
 * @var string path to the album directory
 */
public $path = '/images/gallery';
```
```
/**
 * @var string the #ID of the gallery container
 */
public $id = 'gallery';
```
```
/**
 * @var boolean show the navigation menu
 */
public $showNav = true;
```
```
/**
 * {@link http://www.webmaster-toolkit.com/mime-types.shtml Mime types} to display.
 * Please note, if you want anything other than .gif/.jpg/.jpeg/.png to be shown,
 * you need to edit the regular expression in {@link getImages()}.
 *
 * @var array a list of mime types to display
 */
public $mimeTypes = array('image/gif','image/jpeg','image/png');
```
```
/**
 * @var boolean 'false' will use a generic icon instead
 */
public $createThumbnails = true;
```
```
/**
 * @var integer the width of the generated thumbnails
 */
public $thumbnailWidth = 128;
```
```
/**
 * @var integer the height of the generated thumbnails
 */
public $thumbnailHeight = 128;
```
```
/**
 * @var boolean album folders that are in a format parseable by {@link http://uk3.php.net/strtotime strtotime} should be displayed as a formated date
 */
public $displayFoldersAsDates = true;
```
```
/**
 * @var string the {@link http://uk3.php.net/manual/en/function.date.php date format} used for outputting
 */
public $dateFormat = 'jS M Y';
```
```
/**
 * @var boolean whether to display the number of images in an album
 */
public $displayNumImages = true;
```
```
/**
 * @var integer the number of images to show on each page
 */
public $imagesPerPage = 20;
```
```
/**
 * @var integer the number of images to show on each row (0 for unlimited)
 */
public $imagesPerRow = 4;
```
```
/**
 * @var integer the number of albums to show on each page
 */
public $albumsPerPage = 20;
```
```
/**
 * @var integer the number of albums to show on each row (0 for unlimited)
 */
public $albumsPerRow = 0;
```
```
/**
 * @var string sort order asc/desc
 */
public $sort_order = 'desc';
```