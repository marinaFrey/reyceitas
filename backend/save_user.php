<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require 'connect.php';

if (isset($_GET['user']))
{
    $obj = json_decode($_GET['user']);
    $sql = <<<EOF
    INSERT INTO users (username, full_name, email, password, authentication_level)
        VALUES (:usr_nm, :full_nm, :mail, :pwd, 0);
EOF;

    $db = connect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':usr_nm', $obj->username, SQLITE3_TEXT);
    $stmt->bindValue(':full_nm', $obj->fullname, SQLITE3_TEXT);
    $stmt->bindValue(':mail', $obj->email, SQLITE3_TEXT);
    $stmt->bindValue(':pwd', $obj->password, SQLITE3_TEXT);


    $ret = $stmt->execute();
    if(!$ret) {
        echo -1;
        return -1;
    }
    $last_id = $db->lastInsertRowId();
    echo $last_id;
    return $last_id; 
}

if (isset($_GET['user_edit']))
{
    $obj = json_decode($_GET['user_edit']);
    $sql = <<<EOF
    UPDATE users 
    SET full_name = :full_nm, email = :mail, password = :pwd
    WHERE username = :usr_nm ;
EOF;
    $db = connect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':full_nm', $obj->fullname, SQLITE3_TEXT);
    $stmt->bindValue(':mail', $obj->email, SQLITE3_TEXT);
    $stmt->bindValue(':pwd', $obj->password, SQLITE3_TEXT);
    $stmt->bindValue(':usr_nm', $obj->username, SQLITE3_TEXT);

    $ret = $stmt->execute();
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
