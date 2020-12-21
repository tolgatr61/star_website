<?php
require_once 'model/account/Account.php';

class AccountStorageStub implements AccountStorage {

    private $comptes;

    public function __construct() {
        $this->comptes = array(
            array(
                'login' => 'toto',
                'password' => '$2y$10$vecze/V//nVxqjpk2VqMOuk46PoPs/ol.xdB4.0OTtj1Z.ee0W4a.',
                'name' => 'Toto Dupont',
                'status' => 'admin',
            ),
            array(
                'login' => 'testeur',
                'password' => '$2y$10$Lj0O5fP9xARQvYuo5/dd7.PLAVm9mPo5zwPEohMogU3XwIGN6ZY2C',
                'name' => 'Jean-Michel Testeur',
                'status' => 'user',
            ),
            array(
                'login' => 'martine',
                'password' => '$2y$10$yZ6Wvlp1ylaRK6IwjY0CzuJ.eSQJyao/iMWbHT1SMDKkJ6WEBCnr6',
                'name' => 'Martine Dubois',
                'status' => 'user',
            ),
            array(
                'login' => 'raymond',
                'password' => '$2y$10$X1HrGzMVPYiOeV6UibjGnuDd/MoGnm0.hwhiwWDmyzjjHlfZpsOlm',
                'name' => 'Raymond Martin',
                'status' => 'user',
            ),
        );
    }

    public function checkAuth($login, $password) {
        foreach ($this->comptes as $key => $value) {
            if ($this->comptes[$key]['login'] === $login && password_verify($password, $this->comptes[$key]['password'])) {
                $account = new Account($this->comptes[$key]['name'], $this->comptes[$key]['login'], $this->comptes[$key]['password'], $this->comptes[$key]['status']);
                return $account;
                }
        }
        return null;
    }

    public function getAccounts() {
        return $this->comptes;
    }
}

?>