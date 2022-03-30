<?php

$uri = "";
$exp = explode("/", $_SERVER["REQUEST_URI"]);
foreach( $exp as $dir ) {
    if( strlen($dir) > 0 ) {
        $uri .= $dir . "/";
    }
}

define("PROJECT", $uri);
define("ROOT", "./");
define("WWW", "http://" . $_SERVER["HTTP_HOST"] . "/" . PROJECT . "/");

define("DB_HOST", "localhost");
define("DB_PORT", "3306");
define("DB_USER", "root");
define("DB_PSWD", "");
define("DB_NAME", "teste_php");
