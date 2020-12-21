<?php
require_once 'model/account/AccountStorage.php';
require_once 'model/account/AccountBuilder.php';

class AccountStorageMySQL implements AccountStorage {

    private $bd;

    public function __construct($bd) {
        $this->bd = $bd;
    }

    // On récupère un compte sous forme d'Array.
    public function getAccount($id) {
        $stmt = $this->bd->prepare('SELECT * from `account` where id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;

    }

    // On récupère les comptes sous forme d'Array.
    public function getAccounts() {
        $rq = "SELECT * FROM account";
        $stmt = $this->bd->prepare($rq);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // On récupère un compte sous forme de Classe.
    public function read($id) {
        if ($this->getAccount($id) == null) {
            return null;
        }
        //var_dump($this->getAccount($id));
        $accountBuilder = new AccountBuilder($this->getAccount($id));
        return $accountBuilder->createAccount();
    }

    // On récupère les comptes sous forme de Classe. (double Array pour identifier chaque objet par son id.)
    public function readAll() {
        $tab = array();
        foreach ($this->getAccounts() as $val) {
            $t = new AccountBuilder($val);
            $tab[] = array($val['id'] => $t->createAccount());
        }
        return $tab;
    }

    // Méthode pour ajouter un Compte en BD, (lors de l'inscription).
    public function create(Account $a) {
        $stmt = $this->bd->prepare("INSERT INTO `account` (surname, name, login, password, status) VALUES (:surname, :name, :login, :password, :status)");
        $surname = $a->getSurName();
        $name = $a->getName();
        $login = $a->getLogin();
        $password = $a->getMdp();
        $status = $a->getStatut();
        
        $stmt->execute(array(':surname' => $surname, ':name' => $name, ':login' => $login, ':password' => $password, ':status' => $status));
        // retourne l'Id
        return $this->bd->lastInsertId();
    }

    // Méthode pour vérifier l'authentification.
    public function checkAuth($login, $password) {
        foreach ($this->readAll() as $array) {
            foreach ($array as $key => $value){
            if ($value->getLogin() === $login && password_verify($password, $value->getMdp())) {
                $_SESSION['accountId'] = $key;
                return $this->read($key);
            }
        }
        }
        return null;
    }

}

?>