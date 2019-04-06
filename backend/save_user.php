<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require 'connect.php';
require __DIR__ . '/vendor/autoload.php';



if (isset($_GET['user']))
{
    $obj = json_decode($_GET['user']);

    $db = connect();
    $auth = new \Delight\Auth\Auth($db);
    $userId = -1;
    try {
        $userId = $auth->registerWithUniqueUsername($obj->email, $obj->password, $obj->username);
        echo 'We have signed up a new user with the ID ' . $userId; 
    } catch (\Delight\Auth\InvalidEmailException $e) {
        die('Invalid email address');
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        die('Invalid password');
    }
    catch (\Delight\Auth\UserAlreadyExistsException $e) {
        die('User already exists');
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        die('Too many requests');
    }
    catch(\Delight\Auth\DuplicateUsernameException $e) {
        die('Username already exists');
    }
    return $userId;
}

if (isset($_GET['user_edit']))
{

    $db = connect();
    $auth = new \Delight\Auth\Auth($db);
    
    if($auth->isLoggedIn()) {
        // $real_username = $auth->getUsername();
        $obj = json_decode($_GET['user_edit']);

        $auth->changeEmail($obj->email);
        $auth->changePassword($obj->password);
//         $sql = <<<EOF
//         UPDATE users 
//         SET full_name = :full_nm, email = :mail, password = :pwd
//         WHERE username = :usr_nm ;
// EOF;
//         $db = connect();
//         $stmt = $db->prepare($sql);
//         $stmt->bindValue(':full_nm', $obj->fullname, PDO::PARAM_STR);
//         $stmt->bindValue(':mail', $obj->email, PDO::PARAM_STR);
//         $stmt->bindValue(':pwd', $obj->password, PDO::PARAM_STR);
//         $stmt->bindValue(':usr_nm', $obj->username, PDO::PARAM_STR);

//         $ret = $stmt->execute();
//         if(!$ret) {
//             echo -1;
//             echo $stmt->errorInfo();
//             return -1;
//         }
//         $last_id = $db->lastInsertId();
//         echo $last_id;
//         return $last_id;
            return $auth->getUserId();
    }
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
