<?php
require_once 'model/star/Star.php';

class StarBuilder {

    private $data;
    private $errors;

    public function __construct($data = null) {
        if ($data === null) {
			$data = array(
				"name" => "",
                "bayer" => "",
                "spectralType" => "",
                "magnitude" => "",
                "distance" => "",
			);
		}
        $this->data = $data;
        $this->errors = array();
    }

    // Méthode qui va build une étoile en fonction de $this->data
    public function createStar() {
        if (!key_exists("name", $this->data) || !key_exists("bayer", $this->data) || !key_exists("spectralType", $this->data) || !key_exists("magnitude", $this->data) || !key_exists("distance", $this->data)) {
            throw new Exception("Missing fields for color creation");
        }
		return new Star($this->data["name"], $this->data["bayer"], $this->data['spectralType'], $this->data['magnitude'], $this->data['distance']);
    }

    // Vérification de la validité des données stockés dans $this->data sinon on remplit le tableau d'erreurs.
    public function isValid() {
        $this->errors = array();
        if (!key_exists("name", $this->data) || $this->data["name"] === "")
			$this->errors["name"] = "Vous devez entrer un nom";
		else if (mb_strlen($this->data["name"], 'UTF-8') >= 30)
            $this->errors["name"] = "Le nom doit faire moins de 30 caractères";
        
        if (!key_exists("bayer", $this->data) || $this->data["bayer"] === "")
			$this->errors["bayer"] = "Vous devez entrer le bayer.";
        
        if (!key_exists("spectralType", $this->data) || $this->data["spectralType"] === "")
            $this->errors["spectralType"] = "Vous devez entrer le type de Spectre.";
            
        if (!key_exists("magnitude", $this->data) || $this->data["magnitude"] === "")
			$this->errors["magnitude"] = "Vous devez entrer une magnitude.";
		else if (!preg_match("/^-?\d*\.{0,1}\d+$/i", $this->data["magnitude"]))
            $this->errors["magnitude"] = "Un nombre est attendu.";
        
        if (!key_exists("distance", $this->data) || $this->data["distance"] === "")
			$this->errors["distance"] = "Vous devez entrer une distance.";
		else if (!preg_match("/^[0-9]*$/i", $this->data["distance"]))
			$this->errors["distance"] = "Caractères autorisés : 0123456789.";
		return count($this->errors) === 0;
    }

    // Récuperation d'une donnée en fonction de sa référence.
    public function getData($ref) {
        return key_exists($ref, $this->data)? $this->data[$ref] : '';
    }

    // Récuperation d'une erreur en fonction de sa référence.
    public function getErrors($ref) {
		return key_exists($ref, $this->errors)? $this->errors[$ref]: null;
	}

    // Méthodes retournant les références de nos données.
    public function getNameRef() {
        return "name";
    }

    public function getBayerRef() {
        return "bayer";
    }

    public function getSpectralRef() {
        return "spectralType";
    }

    public function getMagnitudeRef() {
        return "magnitude";
    }

    public function getDistanceRef() {
        return "distance";
    }
}

?>