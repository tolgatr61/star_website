<?php
require_once 'model/account/Account.php';

interface AccountStorage {
    public function read($id);
    public function readAll();
    public function create(Account $a);
    public function checkAuth($login, $password);
}

?>