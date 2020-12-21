<?php
require_once 'model/image/Image.php';
require_once 'model/image/ImageBuilder.php';
class ImageStorageMySQL implements ImageStorage {
    
    private $bd;

    public function __construct($bd) {
        $this->bd = $bd;
    }

    // On récupère toutes les images sous forme d'Array en fonction de l'id d'une étoile.
    public function getImages($id) {
        $stmt = $this->bd->prepare('SELECT * from `image` where starId = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }

    // On récupère toutes les images en fonction d'un id d'étoile sous forme de Classe. (double Array pour identifier chaque objet par son id.)
    public function readAll($id) {
        $tab = array();
        foreach ($this->getImages($id) as $val) {
            $tab[] = array($val['id'] => new Image($val['urlName'], $val['starId']));
        }
        return $tab;
    }

    // Insertion d'une image en BD.
    public function create(Image $i) {
        $stmt = $this->bd->prepare("INSERT INTO `image` (urlName, starId) VALUES (:urlName, :starId)");
        $nameURL = $i->getNameURL();
        $starId = $i->getStarId();  
        $stmt->execute(array(':urlName' => $nameURL, ':starId' => $starId));
        // retourne l'Id
        return $this->bd->lastInsertId();
    }

    // Maj d'une image en BD.
    public function update($id, Image $i) {
        if (empty($this->readAll($id))) {
            return false;
        }
        $this->create($i);
    }

    // Supprimer les images d'une étoile.
    public function delete($id) {
        $stmt = $this->bd->prepare('DELETE FROM `image` WHERE starId = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function checkImage($data, $starId) {
        if (empty($data)) {
            return false;
        }
        $fileNames = array_filter($_FILES['files']['name']);
        $filetmpNames = array_filter($_FILES['files']['tmp_name']);

    for ($i = 0; $i < sizeof($fileNames); $i++) {
        if (exif_imagetype($filetmpNames[$i]) == IMAGETYPE_PNG || exif_imagetype($filetmpNames[$i]) == IMAGETYPE_JPEG) {
            if (getimagesize($filetmpNames[$i])[0] < 1800 && getimagesize($filetmpNames[$i])[1] < 1800) {
                if (move_uploaded_file($filetmpNames[$i], "./upload/" . $fileNames[$i])) {
                    $im = new Image("./upload/" . $fileNames[$i], $starId);
                    $this->create($im);
                    }
                }
            }
        }
    }

    public function checkUpdateImage($data, $starId) {
        if (empty($data)) {
            return false;
        }
        $fileNames = array_filter($_FILES['files']['name']);
        $filetmpNames = array_filter($_FILES['files']['tmp_name']);

    for ($i = 0; $i < sizeof($fileNames); $i++) {
        if (exif_imagetype($filetmpNames[$i]) == IMAGETYPE_PNG || exif_imagetype($filetmpNames[$i]) == IMAGETYPE_JPEG) {
            if (getimagesize($filetmpNames[$i])[0] < 1800 && getimagesize($filetmpNames[$i])[1] < 1800) {
                if (move_uploaded_file($filetmpNames[$i], "./upload/" . $fileNames[$i])) {
                    $im = new Image("./upload/" . $fileNames[$i], $starId);
                    $this->create($im);
                    }
                }
            }
        }
    }

}
?>