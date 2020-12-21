<?php
/*
 * On indique que les chemins des fichiers qu'on inclut
 * seront relatifs au répertoire src.
 */
set_include_path("./src");

define('PATH_ROOT', __DIR__);
/* Inclusion des classes utilisées dans ce fichier */
require_once("Router.php");
require_once("model/star/StarStorageMySQL.php");
require_once("model/account/AccountStorageMySQL.php");
require_once("model/image/ImageStorageMySQL.php");
require_once("model/cookie/CookieMySQL.php");
require_once("private/mysql_config.php");

/*
 * Cette page est simplement le point d'arrivée de l'internaute
 * sur notre site. On se contente de créer un routeur
 * et de lancer son main.
 */
try{
    $PDO = new PDO('mysql:host='.MYSQL_HOST.';port='.MYSQL_PORT.';dbname='.MYSQL_DB.';charset=utf8', MYSQL_USER, MYSQL_PASSWORD);
    $starStorage = new StarStorageMySQL($PDO);
    $accountStorage = new AccountStorageMySQL($PDO);
    $imageStorage = new ImageStorageMySQL($PDO);
    $cookieStorage = new CookieMySQL($PDO);
    // $accountStorage = new AccountStorageMySQL($PDO);
    $router = new Router($starStorage, $accountStorage, $imageStorage, $cookieStorage);
    $router->main();
}catch(Exception $e){
    echo"Pdo exception ". $e; 
}


?>