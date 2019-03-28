<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require 'connect.php';

if (isset($_GET['user']))
{
    $obj = json_decode($_GET['user']);
    $sql = <<<EOF
    INSERT INTO users (username, full_name, email, password) VALUES ('$obj->username','$obj->fullName','$obj->email', '$obj->password');
EOF;
    $db = connect();
    $ret = $db->query($sql);
    if(!$ret) {
        echo -1;
        return -1;
    }
    $last_id = $db->lastInsertRowId();
    echo $last_id;
    return $last_id; 
}

//$str_json = file_get_contents('php://input');
//$obj = json_decode($str_json);
//$db = connect();
//$sql =<<<EOF
//INSERT INTO users (username, full_name, email, password) VALUES ('$obj->username','$obj->fullName','$obj->email', '$obj->password');
//EOF;
//$ret = $db->query($sql);
//$last_id = $db->lastInsertRowId();
//echo $last_id;
//return $ret; 
?>
