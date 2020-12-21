<?php

class Star {
    // Attributs de notre étoile.
    private $name;
    private $desBayer;
    private $typeSpectre;
    private $magnitude;
    private $distance;

    public function __construct($name, $desBayer, $typeSpectre, $magnitude, $distance) {
        $this->name = $name;
        $this->desBayer = $desBayer;
        $this->typeSpectre = $typeSpectre;
        $this->magnitude = $magnitude;
        $this->distance = $distance;
    }

    // Getters
    public function getName() {
        return $this->name;
    }

    public function getBayer() {
        return $this->desBayer;
    }

    public function getSpectreType() {
        return $this->typeSpectre;
    }

    public function getMagnitude() {
        return $this->magnitude;
    }

    public function getDistance() {
        return $this->distance;
    }
}

?>