<?php

class XMLObject extends SimpleXMLElement
{

    public function __construct(string $data,
                                int $options = 0,
                                bool $dataIsURL = false,
                                string $namespaceOrPrefix = "",
                                bool $isPrefix = false)
    {
        parent::__construct($data, $options, $dataIsURL, $namespaceOrPrefix, $isPrefix);
    }

    public function getFeedItems(): XMLObject {
        return $this->channel->item;
    }

    public function getBuildDate(): string {
        return $this->channel->lastBuildDate;
    }

    public function getDlUrl(): string {
        if( property_exists($this, 'link') )
            return $this->link;
        return '';
    }

    public function getFileName(): string {
        if( property_exists($this, 'link') ) {
            $info = pathinfo($this->link);
            return $info['basename'];
        }
        return '';
    }


}
