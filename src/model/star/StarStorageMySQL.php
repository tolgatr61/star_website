<?php
require_once 'model/star/StarBuilder.php';

class StarStorageMySQL implements StarStorage {

    private $bd;

    public function __construct($bd) {
        $this->bd = $bd;
    }

    // On récupère une étoile sous forme d'Array.
    public function getStar($id) {
        $stmt = $this->bd->prepare('SELECT * from `stars` where id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;

    }

    // On récupère les étoiles sous forme d'Array.
    public function getStars() {
        $rq = "SELECT * FROM stars";
        $stmt = $this->bd->prepare($rq);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // On récupère une étoile sous forme de Classe.
    public function read($id) {
        if ($this->getStar($id) == null) {
            return null;
        }
        $starBuilder = new StarBuilder($this->getStar($id));
        return $starBuilder->createStar();
    }

    // On récupère une étoile sous forme de Classe. (double Array pour identifier chaque objet par son id.)
    public function readAll() {
        $tab = array();
        foreach ($this->getStars() as $val) {
            $t = new StarBuilder($val);
            $tab[] = array($val['id'] => $t->createStar());
        }
        return $tab;
    }

    // Insertion d'une étoile en BD.
    public function create(Star $s) {
        $stmt = $this->bd->prepare("INSERT INTO `stars` (name, bayer, spectralType, magnitude, distance, accountId) VALUES (:name, :bayer, :spectralType, :magnitude, :distance, :accountId)");
        $name = $s->getName();
        $bayer = $s->getBayer();
        $spectreType = $s->getSpectreType();
        $magnitude = $s->getMagnitude();
        $distance = $s->getDistance();
        
        $stmt->execute(array(':name' => $name, ':bayer' => $bayer, ':spectralType' => $spectreType, ':magnitude' => $magnitude, ':distance' => $distance, ':accountId' => $_SESSION['accountId']));
        // retourne l'Id
        return $this->bd->lastInsertId();
    }

    // Maj d'une étoile en BD.
    public function update($id, Star $s) {
        if (empty($this->read($id))) {
            return false;
        }

        $sql = "UPDATE `stars` SET name = :name, bayer = :bayer, spectralType = :spectralType, magnitude = :magnitude, distance = :distance WHERE id= :id";
        $stmt = $this->bd->prepare($sql);

        $name = $s->getName();
        $bayer = $s->getBayer();
        $spectreType = $s->getSpectreType();
        $magnitude = $s->getMagnitude();
        $distance = $s->getDistance();

        $stmt->execute(array(':name' => $name, ':bayer' => $bayer, ':spectralType' => $spectreType, ':magnitude' => $magnitude, ':distance' => $distance, ':id' => $id));
        return true;
        
    }

    // Supprimer une étoile.
    public function delete($id) {
        $stmt = $this->bd->prepare('DELETE FROM `stars` WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    
    // Supprimer toutes les étoiles.
    public function deleteAll() {
        $stmt = $this->bd->prepare('DELETE FROM `stars`');
        $stmt->execute();
    }

    // Récuperer l'identifiant de compte d'une étoile précise.
    public function getAStarAccountId($id) {
        $stmt = $this->bd->prepare('SELECT accountId from `stars` where id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    // On vérifie si la session qui définit l'identifiant de compte est égale à celle d'une étoile.
    public function verifyAccountId($id) {
        if (strval($_SESSION['accountId']) == $this->getAStarAccountId($id)['accountId']) {
            return true;
        }
        return false;
    }
    

}

?>