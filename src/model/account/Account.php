<?php

class Account {
    // Attributs d'un compte.
    private $nom;
    private $prenom;
    private $login;
    private $mdp;
    private $statut;

    public function __construct($nom, $prenom, $login, $mdp, $statut) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->login = $login;
        $this->mdp = $mdp;
        $this->statut = $statut;
    }
    // Getters
    public function getSurName() {
        return $this->nom;
    }
    
    public function getName() {
        return $this->prenom;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getMdp() {
        return $this->mdp;
    }

    public function getStatut() {
        return $this->statut;
    }


}

?>