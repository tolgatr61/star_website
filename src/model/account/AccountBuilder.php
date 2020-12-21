<?php
require_once 'model/account/Account.php';

class AccountBuilder {

    private $data;
    private $errors;

    public function __construct($data = null) {
        if ($data === null) {
			$data = array(
                "surname" => "",
				"name" => "",
                "login" => "",
                "password" => "",
                "status" => "",
			);
		}
        $this->data = $data;
        $this->errors = array();
    }

    // Méthode créatrice de compte déjà existant en fonction des données de $this->data.
    public function createAccount() {
        //var_dump($this->data);
        if (!key_exists("surname", $this->data) || !key_exists("name", $this->data) || !key_exists("login", $this->data) || !key_exists("password", $this->data) || !key_exists("status", $this->data)) {
            throw new Exception("Missing fields for account creation.");
        }
		return new Account($this->data['surname'], $this->data["name"], $this->data["login"], $this->data['password'], $this->data['status']);
    }

    // Méthode créatrice pour les nouveaux comptes.
    public function createNewAccount() {
        if (!key_exists("surname", $this->data) || !key_exists("name", $this->data) || !key_exists("login", $this->data) || !key_exists("password", $this->data)) {
            throw new Exception("Missing fields for account creation.");
        }
		return new Account($this->data['surname'], $this->data["name"], $this->data["login"], password_hash($this->data['password'], PASSWORD_BCRYPT), 'user');
    }

    // Méthode qui vérifie la validité des données et remplit l'array d'erreur sinon.
    public function isValid() {
        $this->errors = array();

        if (!key_exists("surname", $this->data) || $this->data["surname"] === "")
			$this->errors["surname"] = "Vous devez entrer un nom";
		else if (mb_strlen($this->data["surname"], 'UTF-8') >= 20)
            $this->errors["surname"] = "Le nom doit faire moins de 20 caractères";

        if (!key_exists("name", $this->data) || $this->data["name"] === "")
			$this->errors["name"] = "Vous devez entrer un prénom";
		else if (mb_strlen($this->data["name"], 'UTF-8') >= 20)
            $this->errors["name"] = "Le nom doit faire moins de 20 caractères";
        
        if (!key_exists("login", $this->data) || $this->data["login"] === "")
			$this->errors["login"] = "Vous devez entrer un login.";
        
        if (!key_exists("password", $this->data) || $this->data["password"] === "")
            $this->errors["password"] = "Vous devez entrer un mot de passe.";

		return count($this->errors) === 0;
    }

    // Méthode qui renvoie une donnée en fonction de sa référence.
    public function getData($ref) {
        return key_exists($ref, $this->data)? $this->data[$ref] : '';
    }

    // Méthode qui renvoie une erreur en fonction de sa réference.
    public function getErrors($ref) {
		return key_exists($ref, $this->errors)? $this->errors[$ref]: null;
	}

    // Méthodes qui retournent les réferences.
    public function getSurNameRef() {
        return "surname";
    }

    public function getNameRef() {
        return "name";
    }

    public function getLoginRef() {
        return "login";
    }

    public function getPasswordRef() {
        return "password";
    }

    public function getStatutRef() {
        return "status";
    }
}

?>