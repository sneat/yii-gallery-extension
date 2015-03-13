## Requirements ##
Yii 1.0 or above

## Installation ##
Extract the release file under `protected/extensions`

## Usage ##
Choose a location to store the photos: eg. /images/gallery
Ensure that this location is writable by the webserver (if you want thumbnails generated for you).

Create some "albums" in the gallery and put your photos (and optional [description.txt](DescriptionTxt.md)) in there eg.

```
+-images/
  +-gallery/
    +-sample/
    | +-image1.jpg
    | +-image2.jpg
    +-sample2/
    | +-image4.jpg
    | +-image5.jpg
    +-sample3/
      +-description.txt
      +-image6.jpg
      +-image7.jpg
```

If you name your folder by date (eg. 20091120) and create a [description.txt](DescriptionTxt.md) you can sort your gallery [by date](EGalleryProperties.md), but show whatever album title you want.

### Add the following code to your view: ###
```
$this->widget('application.extensions.gallery.EGallery',
        array('path' => '/images/gallery',
            // 'other' => 'properties',
            )
    );
```

## Help me! ##

**I'm not getting any thumbnails**
  * Check that your gallery path is writeable.
  * Most likely your host is preventing the background task from being run. You have a [couple of options](Cronjobs.md) available.