<?php
header("Access-Control-Allow-Origin: *");

class MyDB extends \PDO {
    function __construct() {
        parent::__construct("sqlite:" . "recipes.db");
        $this->exec('PRAGMA foreign_keys = ON;');
        if(!$this) {
            echo $this->errorInfo();
         } else {
             //echo "Opened database successfully\n";
             //Let's not drop recursive bombs here
         }
    }
}


function connect() {
    $db = new MyDB();
    return $db;
}


?>
