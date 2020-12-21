<?php

class Image {

    private $nameURL;
    private $starId;

    public function __construct($nameURL, $starId) {
        $this->nameURL = $nameURL;
        $this->starId = $starId;
    }

    public function getNameURL() {
        return $this->nameURL;
    }

    public function getStarId() {
        return $this->starId;
    }

}