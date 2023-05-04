<?php

class MediathekDL
{
    protected $_config = "";
    protected $_baseURL = "https://mediathekviewweb.de/feed?query=";

    public function __construct(Configuration $config)
    {
        $this->_config = $config;
    }

    public function getFeed(): XMLObject|null {
        $data = null;
        $url = $this->_baseURL . urlencode($this->_config->getFeedArgs());
        try {
          $xml = file_get_contents($url);
          $data = new XMLObject($xml, LIBXML_NOCDATA);
        } catch (Exception $e) {
          echo "Error: " . $e->getMessage();
        }
        return $data;
    }

    public function createDirIfNotExisting(XMLObject $objItem): void {
        $path = $this->getTargetPath($objItem);
        if (!file_exists($path)) {
           try{
                if( mkdir($path, 0777, true) )
                    echo "Dir '".$path."' successfully created.\n";
           } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
           }
        } else {
            echo "Dir '".$path."' already exists.\n";
        }
    }

    public function isFileExisting(XMLObject $objItem): bool {
        $path = $this->getTargetFileName($objItem);
        return file_exists($path);
    }

    public function sanitize_filename(string $filepath): string {
        $filepath = str_replace($this->_config->_umlautSearch,$this->_config->_umlautReplace, $filepath);
        $filepath = preg_replace('/[^a-zA-Z0-9_. -]/', '_', trim($filepath));

        return $filepath;
    }

    public function getTargetPath(XMLObject $objItem): string {
        $path = $this->_config->getFilesPath() . DIRECTORY_SEPARATOR .
                $this->sanitize_filename( $objItem->category ) . DIRECTORY_SEPARATOR .
                $this->sanitize_filename( $objItem->title );
        return $path;
    }

    public function getTargetFileName(XMLObject $objItem): string {
        $path = $this->_config->getFilesPath() . DIRECTORY_SEPARATOR .
                $this->sanitize_filename( $objItem->category ) . DIRECTORY_SEPARATOR .
                $this->sanitize_filename( $objItem->title ) . DIRECTORY_SEPARATOR .
                $this->sanitize_filename( $objItem->getFileName() );
        return $path;
    }

    public function getFilesSizeOfRemoteFile(string $url): int {
        $headers = get_headers($url, 1);
        return $headers['Content-Length'];
    }

    public function isFileSizeEqual(string $local, string $url): bool {
        $fsRemote = $this->getFilesSizeOfRemoteFile($url);
        $fsLocal = @filesize($local);
        if( $fsRemote ===  $fsLocal ) {
            echo "Filesize equal, NOT downloading again ($fsRemote, $fsLocal).\n\r";
            return true;
        }
        echo "Filesize unequal, downloading again ($fsRemote, $fsLocal).\n\r";
        return false;
    }

    public function download(XMLObject $objItem) {
        $url = $objItem->getDlUrl();
        $targetFileName = $this->getTargetFileName($objItem);
        $fileName = $objItem->getFileName();
        $interval_count = "0";

        $context = stream_context_create(array(), array(
            'notification' => function ($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max) use ($fileName, $targetFileName, $url, $objItem, &$interval_count) {
                if ($notification_code === STREAM_NOTIFY_FILE_SIZE_IS) {
                    echo "Start downloading file '".$objItem->getFileName()."' (".round($bytes_max/1048576)." MB) \r\n";
                    echo "from ".$url."\r\n";
                    echo "to ".$this->getTargetFileName($objItem)."\r\n";
                }
                if ($notification_code === STREAM_NOTIFY_PROGRESS) {
                    $percent_downloaded = round(($bytes_transferred / $bytes_max) * 100);
                    if( $interval_count !== $percent_downloaded && $percent_downloaded % 5 === 0 )
                        echo "Downloading '".$fileName."'.... $percent_downloaded% complete\r\n";

                    $interval_count = $percent_downloaded;
                }
            }
        ));

        $file_size = copy($url, $targetFileName, $context);
        echo "Downloading $fileName... 100% complete\n";
    }

    public function run(): void {
        $feedObj = $this->getFeed()->getFeedItems();
        echo "Got ".$feedObj->count()." items.\n\r";
        foreach( $feedObj as $item ) {
            echo "-####################-\n\r";
            $this->createDirIfNotExisting($item);
            if( !$this->isFileSizeEqual($this->getTargetFileName($item),$item->getDlUrl()) )
                $this->download($item);
        }
    }

}
