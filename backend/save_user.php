<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require 'connect.php';
require 'login.php';

if (isset($_GET['user']))
{
    $obj = json_decode($_GET['user']);
    $sql = <<<EOF
    INSERT INTO users (username, full_name, email, password, authentication_level)
        VALUES (:usr_nm, :full_nm, :mail, :pwd, 0);
EOF;

    $hash = password_hash($obj->password,  PASSWORD_BCRYPT);
    
    $db = connect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':usr_nm', $obj->username, PDO::PARAM_STR);
    $stmt->bindValue(':full_nm', $obj->fullname, PDO::PARAM_STR);
    $stmt->bindValue(':mail', $obj->email, PDO::PARAM_STR);
    $stmt->bindValue(':pwd', $hash, PDO::PARAM_STR);


    error_log("go");
    $ret = $stmt->execute();
    if(!$ret) {
        //error_log($ret);
        error_log("a");
        error_log($stmt->errorCode());
        error_log("a");

        // Conflict such as user already exists.
        if($stmt->errorCode() == 23000) {
            http_response_code(409);
        } else {
            http_response_code(500);
        }

        return -1;
    }
    $last_id = $db->lastInsertId();
    echo $last_id;
    return $last_id; 
}

if (isset($_GET['user_edit']))
{
    $obj = json_decode($_GET['user_edit']);

    // Make sure I am the user I am trying to edit.
    if(!is_logged_already_as($obj->username)) {
        http_response_code(403);
        die();
    }

    $sql = <<<EOF
    UPDATE users 
    SET full_name = :full_nm, email = :mail, password = :pwd
    WHERE username = :usr_nm ;
EOF;
    $db = connect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':full_nm', $obj->fullname, PDO::PARAM_STR);
    $stmt->bindValue(':mail', $obj->email, PDO::PARAM_STR);
    $stmt->bindValue(':pwd', $obj->password, PDO::PARAM_STR);
    $stmt->bindValue(':usr_nm', $obj->username, PDO::PARAM_STR);

    $ret = $stmt->execute();
    if(!$ret) {
        echo -1;
        echo $stmt->errorInfo();
        return -1;
    }
    $last_id = $db->lastInsertId();
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
//$last_id = $db->lastInsertId();
//echo $last_id;
//return $ret; 
?>
