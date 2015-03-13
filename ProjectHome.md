This is a simple photo gallery for the [Yii PHP framework](http://www.yiiframework.com/).

Photo links can be enhanced to include [lightbox](http://www.huddletogether.com/projects/lightbox2/) or similar by editing `views\gallery.php`. CSS is located at `css\gallery.css`.

EGallery makes use of [Yii's Cache](http://www.yiiframework.com/doc/guide/caching.overview) if available. Currently you have to clear the cache manually. A management section is currently being created.


---


Properties

  * **name** = The name of the gallery
  * **path** = The path to the directory containing the albums (relative to webroot)
  * **id** = the #ID of the gallery container
  * **showNav** = Show the navigation menu
  * **mimeTypes** = Mime types to display
  * **createThumbnails** = Or use a generic icon
  * **thumbnailWidth** = The width of generated thumbnails
  * **thumbnailHeight** = The height of generated thumbnails
  * **displayFoldersAsDates** = Album folders that are in a format parseable by strtotime should be displayed as a formated date
  * **dateFormat** = The date format used for output (only used if foldersAreDates = true)
  * **imagesPerPage** = The number of images to show on each page
  * **imagesPerRow** = The number of images to show on each row
  * **albumsPerPage** = The number of albums to show on each page
  * **albumsPerRow** = The number of albums to show on each row (0 for unlimited)
  * **sort\_order** = Album sort order (asc/desc)