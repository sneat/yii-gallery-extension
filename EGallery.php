<?php

/**
 * EGallery class file.
 *
 * EGallery provides a simple gallery for use with {@link http://www.yiiframework.com Yii Framework}
 *
 * How do I install this?
 * Extract the release file under `protected/extensions`
 *
 * How do I use this?
 * Choose a location to store the photos: eg. /images/gallery
 *
 * Create some "albums" in the gallery and put your photos in there eg.
 *     +-images/
 *       +-gallery/
 *         +-sample/
 *         | +-image1.jpg
 *         | +-image2.jpg
 *         +-sample2/
 *         | +-image4.jpg
 *         | +-image5.jpg
 *         +-sample3/
 *           +-image6.jpg
 *           +-image7.jpg
 *
 * 
 * If you name your folder by date (eg. 20091120) and create a description.txt you can sort your gallery by date, but show whatever album title you want.
 *
 * ###Format of description.txt
 * Line 1: Album title
 * Lines 2-n: Album description
 *
 * Album title
 * An optional description of the album that will be parsed by Markdown.
 * It can have multiple lines.
 *
 *
 * Add the following code to your view:
 * ~~~
 * [php]
 * $this->widget('application.extensions.gallery.EGallery',
 * 		array('path' => '/images/gallery'),
 * );
 * ~~~
 *
 * How do I add albums?
 * Albums are nothing more than directories with images in them. Simply create
 * directories inside the gallery directory. Sub albums are currently not supported.
 *
 * How do I comment albums?
 * Create a text file named "description.txt" in the album directory where
 * the first line is the album title and the rest of the file is markdown code.
 *
 * How do I have custom album titles?
 * See "How do I comment albums".
 *
 * @version 1.1
 *
 * @todo Add image upload support, caching
 * @author scythah <scythah@gmail.com>
 * @link http://www.yiiframework.com/extension/egallery/
 */

class EGallery extends CWidget {
	
	/**
	 * @var string the name of the gallery
	 */
	public $name = 'Test gallery';

	/**
	 * The path to the directory containing the albums (relative to webroot).
	 *
	 * @var string path to the album directory
	 */
	public $path = '/images/gallery';

	/**
	 * @var string the #ID of the gallery container
	 */
	public $id = 'gallery';

	/**
	 * @var boolean show the navigation menu
	 */
    public $showNav = true;

	/**
	 * {@link http://www.webmaster-toolkit.com/mime-types.shtml Mime types} to display.
	 * Please note, if you want anything other than .gif/.jpg/.png to be shown,
	 * you need to edit the regular expression in {@link getImages()}.
	 *
	 * @var array a list of mime types to display
	 */
	public $mimeTypes = array('image/gif','image/jpeg','image/png');

	/**
	 * @var boolean 'false' will use a generic icon instead
	 */
    public $createThumbnails = true;

	/**
	 * @var boolean album folders are in a format parseable by {@link http://uk3.php.net/strtotime strtotime}
	 */
    public $foldersAreDates = false;

	/**
	 * @var string the {@link http://uk3.php.net/manual/en/function.date.php date format} used for outputting
	 */
    public $dateFormat = 'jS M Y';

	/**
	 * @var integer the number of images to show on each page
	 */
    public $imagesPerPage = 20;

	/**
	 * @var integer the number of images to show on each row
	 */
    public $imagesPerRow = 4;

	/**
	 * @var string sort order asc/desc
	 */
    public $sort_order = 'desc';

	/**
	 * @var string the current directory the gallery is working with
	 */
	private $_path = '';
	private $_images = array();
	private $_albums = array();
	private $_cssFile;
	private $_folderImage;
	private $_blankImage;

	/**
	 * Initialisation method called by Yii when the component is loaded.
	 *
	 * Cleanup the {@see $this->path gallery path} and check that it's valid.
	 * Publish images and CSS.
	 */
	public function init(){
		$this->path = trim($this->path, '/');
		if($this->path != '' && file_exists(getcwd().DIRECTORY_SEPARATOR.$this->path)):
			$cs=Yii::app()->clientScript;
			$am = Yii::app()->getAssetManager();

			$this->_folderImage = $am->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'folder.png');
			$this->_blankImage = $am->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'image.png');
			$this->_cssFile = $am->publish($this->generateCSS(dirname(__FILE__).DIRECTORY_SEPARATOR.'css'));
			$cs->registerCssFile($this->_cssFile);
//			$this->_cssFile = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'gallery.css.php');
//			$cs->registerCss('gal_'.$this->id,$this->_cssFile,'screen');

			parent::init();
		else:
			Yii::log('Path to gallery is not specified or invalid: '.$this->path, 'info', 'app.extensions.EGallery');
			throw new CException('Path to gallery is not specified or invalid.');
		endif;
	}

	/**
	 * Executes the widget.
	 */
	public function run()
	{
		$this->_path = $_GET['dir'];

		if(!$this->_path)
		{
			$this->_albums = $this->getAlbums();
		}
		else
		{
			$this->_images = $this->getImages($this->_path);

			$pages=new CPagination(count($this->_images));
			$pages->pageSize=$this->imagesPerPage;
			$this->_images = $this->splitImages($this->_images);
		}
		
		$this->render('gallery',array(
			'id'=>$this->id,
			'name'=>$this->name,
			'showNav'=>$this->showNav,
			'pages'=>$pages,
			'imagesPerRow'=>$this->imagesPerRow,
			'details'=>$this->getDetails($this->_path),
			'albums'=>$this->_albums,
			'images'=>$this->_images,
			)
		) ;
	}

	/**
	 * Gets a list of albums, sorted according to {@see $sort_order} with
	 * the first image as a thumbnail or the {@see $folderImage default folder image}
	 * if a thumbnail hasn't been created yet.
	 *
	 * @return array the list of albums
	 */
	private function getAlbums()
	{
		function cmp($a, $b)
		{
			return strcmp($a['name'], $b['name']);
		}

		$goodfiles = array();

		$albums = new DirectoryIterator(getcwd().DIRECTORY_SEPARATOR.$this->path);
		foreach ($albums as $album) {
			if ($album->isDir() && !$album->isDot() && substr($album->getFilename(), 0, 1) != '.') {
				$thumb = $this->getImages($album->getFilename(), true);
				$goodfiles[] = array('title'=>$this->getTitle($album->getFilename()),
								'name'=>$album->getFilename(),
								'thumb'=>($thumb[0]['thumb'] == $this->_blankImage)?$this->_folderImage:$thumb[0]['thumb'],
							);
			}
		}

		usort($goodfiles, 'cmp');
		if($this->sort_order == 'desc')
		{
			$goodfiles = array_reverse($goodfiles);
		}
		
		return $goodfiles;
	}

	/**
	 * Takes a directory name and tries to get it's title from "description.txt".
	 *
	 * @param string the name of the directory
	 * @return string the title
	 */
	private function getTitle($name)
	{
		$_realpath = getcwd().DIRECTORY_SEPARATOR.$this->path.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
		$file = $_realpath.'description.txt';
		if(file_exists($file))
		{
			$name = $this->readDescription($_realpath.'description.txt', true);
		}

		return $name;
	}

	/**
	 * Gets a list of images in the specified directory that match {@see $mimeTypes}
	 * and pass the regular expression (default allows .gif .jpg .jpeg .png).
	 * Creates thumbnails for those images that don't have one yet (this process
	 * could be quite slow if you have lots of images).
	 * 
	 * @param string $path to the directory
	 * @param boolean $single whether to get the first image only
	 * @return array the images in the directory
	 */
	private function getImages($path, $single = false)
	{
		$_images = array();
		if(strstr($path, '../'))
		{
			Yii::log('Possible hacking attempt occured when trying to getImages(). Path requested: '.$path, 'info', 'app.extensions.EGallery');
			return $_images;
		}

		$_needsThumbs = array();
		$_realpath = getcwd().DIRECTORY_SEPARATOR.$this->path.DIRECTORY_SEPARATOR.$path;

		if (!file_exists($_realpath))
		{
			Yii::log('Invalid path specified for getImages(): '.$path, 'info', 'app.extensions.EGallery');
			return $_images;
		}

		$images = new DirectoryIterator($_realpath);
		foreach ($images as $image) {
			if ($image->isFile() && $image->isReadable() && preg_match("/\.(jpe?g|gif|png)$/i",$image->getFilename())) {
				$mime = getimagesize($image->getPathname());
				$mime = $mime['mime'];

				if(in_array($mime,$this->mimeTypes))
				{
					if(file_exists($_realpath.DIRECTORY_SEPARATOR.'thumbs'.DIRECTORY_SEPARATOR.$image->getFilename())){
						$thumb = '/'.$this->path.'/'.$path.'/'.'thumbs'.'/'.$image->getFilename();
					}
					else
					{
						$thumb = $this->_blankImage;
						$_needsThumbs[] = array('folder'=>$_realpath.DIRECTORY_SEPARATOR.'thumbs','image'=>$image->getFilename());
					}

					$pathinfo = pathinfo($image->getPathname());
					$_images[] = array(
						'url' => '/'.$this->path.'/'.$path.'/'.$image->getFilename(),
						'thumb' => $thumb,
						'alt' => $pathinfo['filename'],
					);
					
					if($single)
					{
						break;
					}
				}
			}
		}

		if(!empty($_needsThumbs))
		{
			$this->generateThumbnails($_needsThumbs);
		}

		return $_images;
	}

	/**
	 * Splits the image array based on the current page.
	 *
	 * @param array $_images the original images
	 * @return array the split images
	 */
	private function splitImages(array $_images)
	{
		if(count($_images) > $this->imagesPerPage)
		{
			$page = isset($_GET['page'])? (int)$_GET['page']:1;

			$offset = $this->imagesPerPage * ($page - 1);
			$_images = array_slice($_images, $offset, $this->imagesPerPage);
		}

		return $_images;
	}

	/**
	 * Gets the album title and description from "description.txt". Title defaults
	 * to the album name.
	 *
	 * @param string $path the album requested
	 * @return array the details
	 */
	private function getDetails($path)
	{
		$_details = array();
		if(strstr($path, '../'))
		{
			Yii::log('Possible hacking attempt occured when trying to getDetails(). Path requested: '.$path, 'info', 'app.extensions.EGallery');
			return $_details;
		}

		$_realpath = getcwd().DIRECTORY_SEPARATOR.$this->path.DIRECTORY_SEPARATOR.$path;

		if (!file_exists($_realpath))
		{
			return $_details;
		}

		$file = $_realpath.DIRECTORY_SEPARATOR.'description.txt';
		if(!file_exists($file))
		{
			$_details['name'] = $path;
			$_details['description'] = '';

			return $_details;
		}

		$_details['name'] = $this->readDescription($file, true);
		$_details['description'] = $this->readDescription($file);
		
		return $_details;
	}

	/**
	 * Reads the specified file and returns either the {@link http://uk.php.net/htmlentities htmlentities} of title
	 * or the {@link http://daringfireball.net/projects/markdown/ markdown} parsed description.
	 *
	 * @param string $file the path to the file
	 * @param boolean $title whether to just get the title
	 * @return string the text
	 *
	 */
	private function readDescription($file, $title=false)
	{
		$i = 1;
		$fp = fopen($file, 'r');

		while (!feof($fp))
		{
			$buffer = fgets($fp, 10240);
			if($i == 1)
			{
				if($title)
				{
					return htmlentities($buffer, ENT_QUOTES);
				}
				$buffer = '';
			}
			$i++;
		}
		
		$parser=new CMarkdownParser;
		$buffer=$parser->safeTransform($buffer);
		return $buffer;
	}

	/**
	 * @return string the dynamic CSS needed for {@link EGallery}
	 */
	private function generateCSS($folder)
	{
		$contents = '/*
 * This file is automatically generated. You should edit gallery.css.php instead.
 */

';

		if (is_file($folder.DIRECTORY_SEPARATOR.'gallery.css.php')) {
			ob_start();
			include $folder.DIRECTORY_SEPARATOR.'gallery.css.php';
			$contents .= ob_get_contents();
			ob_end_clean();
		}

		if (is_writable($folder.DIRECTORY_SEPARATOR.'gallery.css'))
		{
			$fp = fopen($folder.DIRECTORY_SEPARATOR.'gallery.css', 'w');
			fwrite($fp, $contents);
			fclose($fp);
		}

		return $folder.DIRECTORY_SEPARATOR.'gallery.css';
	}

	/**
	 * Create thumbnails for the specified images. Logs failed images.
	 * 
	 * @param array $images that need thumbnails
	 */
	private function generateThumbnails($images)
	{
		if(!$this->createThumbnails)
		{
			return;
		}

		$new_w = 128;
		$new_h = 128;

		foreach($images as $image)
		{
			if(!file_exists($image['folder']))
			{
				if (!@mkdir($image['folder'], 0754, true))
				{
					Yii::log('Unable to create empty directory: '.$image['folder'], 'info', 'app.extensions.EGallery');
					continue;
				}
			}

			$pathToOriginal = realpath($image['folder'].DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$image['image']);

			$dimensions = getimagesize($pathToOriginal);
			$orig_w = $dimensions[0];
			$orig_h = $dimensions[1];

			$w_ratio = ($new_w / $orig_w);
			$h_ratio = ($new_h / $orig_h);

			if ($orig_w > $orig_h ) {//landscape
				$crop_w = round($orig_w * $h_ratio);
				$crop_h = $new_h;
			} elseif ($orig_w < $orig_h ) {//portrait
				$crop_h = round($orig_h * $w_ratio);
				$crop_w = $new_w;
			} else {//square
				$crop_w = $new_w;
				$crop_h = $new_h;
			}
			$dest_img = imagecreatetruecolor($new_w,$new_h);

			// Determine format from MIME-Type
			$format = strtolower(preg_replace('/^.*?\//', '', $dimensions['mime']));

			switch($format) {
				case 'jpg':
				case 'jpeg':
					$source_img = imagecreatefromjpeg($pathToOriginal);
				break;
				case 'png':
					$source_img = imagecreatefrompng($pathToOriginal);
				break;
				case 'gif':
					$source_img = imagecreatefromgif($pathToOriginal);
				break;
				default:
					// Unsupported format
					Yii::log('Unsupported format: '.$image['folder'].DIRECTORY_SEPARATOR.$image['image'].'('.$dimensions['mime'].')', 'info', 'app.extensions.EGallery');
				continue 2;
			}

			imagecopyresampled($dest_img, $source_img, 0 , 0 , 0, 0, $crop_w, $crop_h, $orig_w, $orig_h);

			if(imagejpeg($dest_img, $image['folder'].DIRECTORY_SEPARATOR.$image['image'])) {
				imagedestroy($dest_img);
				imagedestroy($source_img);
			} else {
				Yii::log('Could not make thumbnail image: '.$image['folder'].DIRECTORY_SEPARATOR.$image['image'], 'info', 'app.extensions.EGallery');
			}
		}
	}
}
?>