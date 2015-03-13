If you find that you aren't getting any thumbnails generated then your host may be blocking the [exec](http://php.net/exec) or [shell\_exec](http://php.net/shell_exec) commands. You can either try contacting your host to see if they will enable them, or you could try using one of the following options (these may not be available to you on your host, if they aren't you're out of luck, find another host).

If you are having any problems getting this to work when you think it should be working, please [submit an issue](http://code.google.com/p/yii-gallery-extension/issues/list).

# Any server #

Your host may have a web interface that allows you to run a script every x minutes. If they do, you can add
```
php /filesystem/path/to/yii/protected/extensions/gallery/EGalleryProcessQueue.php '/web/path/to/gallery' 'width' 'height'
```

eg.
```
php /var/www/username/protected/extensions/gallery/EGalleryProcessQueue.php '/gallery' '128' '128'
```

as the command to run. You can choose any length of time that you would like to run it. I would recommend running it every 5 minutes.

# (`*`)unix servers #

On unix servers (including linux and Mac OS) you can use [cron](http://en.wikipedia.org/wiki/Cron) to get the thumbnail generator script to run.

**Using crontab**

From your shell run the following command:
```
crontab -e
```

This will open up your crontab. Add the following line to the list (if there is one):
```
*/5 * * * * php /filesystem/path/to/yii/protected/extensions/gallery/EGalleryProcessQueue.php '/web/path/to/gallery' 'width' 'height'
```
Replace the paths and options as appropriate. This command will run the processing script every 5 minutes. You can read more into [how to use crontab](http://adminschoice.com/crontab-quick-reference) if you want to change how often it runs.

# Windows servers #

**Using scheduled tasks**

Instructions taken from [b2evolution](http://manual.b2evolution.net/Set_up_a_Windows_Scheduled_Task)

For starters, we need to get out our Task Scheduler. From the task bar, click Start -> Programs -> Accessories -> System Tools -> Scheduled Tasks. You may not have any scheduled tasks defined yet, in which case you'll see only an icon to add a scheduled task, but don't click it! I know it's tempting, but that opens one of those fancy "wizards" and we would rather do things the unwizard way, thank you very much. A numbered list will walk us through the next steps. I give you - Mr. Numbered List!

  1. Right-click on an empty spot in the Scheduled Task window and select New -> Scheduled Task (Also accessible via File -> New -> Scheduled Task)
  1. Name the new task.
  1. Double-click the new task to open the properties window (or File -> Properties)
  1. Under the Task tab, enter the command listed near the top of this page (eg. `php /var/www/username/protected/extensions/gallery/EGalleryProcessQueue.php '/gallery' '128' '128'`)
  1. Go to the Schedule tab and enter when and how often the task should run. The schedule defaults to run once daily.
  1. The rest of the fields can be left as-is, unless you're an ace and know what you're doing.
  1. Click OK and we're done!