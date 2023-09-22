## MediathekViewWeb Feed Downloader

This is a feed downloader for the [mediathekviewweb.de](https://mediathekviewweb.de/) (project is also on [GitHub](https://github.com/mediathekview/mediathekviewweb)) written in PHP since I don't know any shel scripting. Intention was to use it in a cron job.

This project is not optimised or beautified, it was written with the intention of working.
If you can't stand the mess I've made, feel free to make a pull request.

### Requirements

- Project was written and tested with *PHP 8.0.6*. It may work with lower PHP versions.
- Make sure PHP is in your PATH and callable from anywhere.
- Extension ``` extension=openssl ``` must be activated.

### Features

- Configurable download folder (relative and absolute paths).
- Checks the file sizes of already downloader files and skips DL on match. Restarts download of file when mismatch. 

### How to use

```
php index.php '#anstalt !zdf >30'
```