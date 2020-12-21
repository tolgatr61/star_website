<?php

class CookieMySQL implements CookieStorage {

    private $bd;

    public function __construct($bd) {
        $this->bd = $bd;
    }

    public function read() {
        $stmt = $this->bd->prepare('SELECT lifetime from `cookie` where id = :id');
        $stmt->bindValue(':id', 1, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function update($lifetime) {
        $sql = "UPDATE `cookie` SET lifetime = :lifetime WHERE id= :id";
        $stmt = $this->bd->prepare($sql);
        $id = 1;
        $stmt->execute(array(':lifetime' => $lifetime, ':id' => $id));
        return true;
    }

    public function setCookie() {
        $lifetime = $this->read();
        //session_set_cookie_params($lifetime);
        session_name('user');
    }

    public function changeLifetime($lifetime, $account) {
        if ($account->getStatut() === 'admin') {
            $this->update($lifetime);
            session_set_cookie_params($lifetime);
        }
    }


}

?>