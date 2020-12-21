<?php
require_once 'model/account/Account.php';

class AuthenticationManager {

    private $compte;

    public function __construct($compte) {
        $this->compte = $compte;
    }

    // Si le compte est différent de null. Donc si il dispose d'une instance valide suite à une connection valide. 
    // Alors on sauvegarde en session le compte actif.
    public function verifyConnection() {
        if ($this->compte != null) {
            $_SESSION['user'] = $this->compte;
            return true;
        }
        return false;
    }

    // Méthode qui permet de vérifier si un utilisateur est connecté.
    public function isUserConnected() {
        if (isset($_SESSION['user']) && $_SESSION['user']->getStatut() === 'user') {
            return true;
        }
        return false;
    }

    // Méthode qui permet de vérifier si un administratur est connecté.
    public function isAdminConnected() {
        if (isset($_SESSION['user']) && $_SESSION['user']->getStatut() === 'admin') {
            return true;
        }
        return false;
    }

    /*

    public function getUserName() {
        try {
            $userName = $_SESSION['login'];
            return $userName;
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    */

    // Méthode qui détruit toutes les sessions, afin d'effectuer une déconnexion complète.
    public function disconnectUser() {
        session_unset();
        session_destroy();
    }

}
?>