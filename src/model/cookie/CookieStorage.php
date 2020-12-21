<?php

interface CookieStorage {
    public function read();
    public function update($lifetime);
    public function setCookie();
    public function changeLifetime($lifetime, $account);
}

?>