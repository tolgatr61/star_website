<?php
require_once 'model/image/Image.php';
class ImageBuilder {

    private $data;
    private $errors;

    public function __construct($data) {
        $this->data = $data; // array d'array
        $this->errors = array();
    }

    public function isValid() {
        $fileNames = array_filter($this->data['name']);
        $filetmpNames = array_filter($this->data['tmp_name']);

        for ($i = 0; $i < sizeof($fileNames); $i++) {
            if (exif_imagetype($filetmpNames[$i]) == IMAGETYPE_PNG || exif_imagetype($filetmpNames[$i]) == IMAGETYPE_JPEG) {
                if (getimagesize($filetmpNames[$i])[0] < 400 && getimagesize($filetmpNames[$i])[1] < 400) {
                    continue;
                }
                else return false;
            }
            else return false;
            }
        return true;
    }
    /*
    
    public function createImage($generatedName, $starId) {
        if ($generatedName != "" || $this->starId != null) {
            throw new Exception("Missing fields for account creation.");
        }
        else {
            return new Image($generatedName, $this->starId);
        }
    }

    public function getData($ref) {
        return key_exists($ref, $this->data)? $this->data[$ref] : '';
    }

    public function getNameURLRef() {
        return "nameURL";
    }

    public function getStarIdRef() {
        return "starId";
    }
    */

}
?>