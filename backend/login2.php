<?php
// header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);

// header("Access-Control-Allow-Origin: * ");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

header("Access-Control-Allow-Origin: http://localhost:8000/login2.php");
// header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require_once 'connect.php';
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;


function verify($jwt_access_token) {
    $info_signed = NULL;
    try {
        $key = file_get_contents('./secrets/keys/pubkey.pem');
        $decoded = JWT::decode($jwt_access_token, $key, array('RS256'));
        $info_signed = $decoded;
    } catch(Exception $e) {
        error_log("JWT Error caught: " . $e->getMessage());
        error_log(get_class($e));
    }
    
    return $info_signed;
}


function generate_jwt($user_id,  $user_pwd, $stored_info, $valid_time_sec, $delay_start=0){

    $secret_key = file_get_contents('./secrets/keys/privkey.pem');
    $issuer_claim = "luatech"; // this can be the servername
    $audience_claim = $user_id;
    $issuedat_claim = time(); // issued at
    $notbefore_claim = $issuedat_claim + $delay_start; //not before in seconds
    $expire_claim = $issuedat_claim + $valid_time_sec; // expire time in seconds
    $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => $stored_info,
        );

    $encoded = JWT::encode($token, $secret_key, 'RS256');
    return $encoded;
}


function get_current_user_info()
{
    $info = NULL;
    error_log("getting current user info");
    if ( isset($_COOKIE['ME']) )
    {
        error_log("cookie set");
        $info = verify(base64_decode($_COOKIE['ME']));
    }
    return $info;
}


function is_logged_as_a_valid_user()
{
    $info = get_current_user_info();
    return $info != NULL;
}

function is_logged_already_as($username)
{
    $is_logged = FALSE;
    if ( isset($_COOKIE['ME']) )
    {
        // error_log($_COOKIE['ME']);

        // error_log('c');
        $info_signed = verify( base64_decode($_COOKIE['ME']));
        // error_log($o->access_token);
        // error_log('c');

        if($info_signed != NULL && $info_signed->aud == $username) {
            $is_logged = TRUE;
        }

    }
    return $is_logged;
}




// $k_ok = generate_jwt($user_id="user1",  $user_pwd="123", 
//     $stored_info="lol", $valid_time_sec=10);

// $a = verify(NULL); 
// $b = verify("{}");
// $c = verify("");
// $d = verify(-1);
// $e = verify($k_ok);
// $f = verify($k_ok . ".");

// $r = array("a" => $a, "b" => $b, "c" => $c, "d" => $d, "e" => $e, "f" => $f);
// echo json_encode($r);

function get_username_of($user_id) {

    $sql = "SELECT username from users where user_id == :user_id";
    $db = connect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $ret = $stmt->execute();

    // If query failed, tell why.
    $result = NULL;
    if(!$ret) {
        error_log($stmt->errorInfo());
        http_response_code(500);
    }

    $ret = $stmt->fetchAll();
    if (count($ret) == 0) {
        // User does not exist.
        // echo "USER DOES NOT EXIST";
        http_response_code(404);
    } else if(count($ret) > 1) {
        // More than one user with that username! Should not happen!
        // echo "NOT UNIQUE???";
        http_response_code(409);
    } else {
        $row = $ret[0];
        $result = $row['username'];
    }
    return $result;
}

function get_current_authentication_level()
{
    $info =  get_current_user_info();
    if($info == NULL) {
        http_response_code(403);
        return NULL;
    }
    $sql = "SELECT authentication_level from users where username == :username";
    $db = connect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':username', $info->aud, PDO::PARAM_STR);
    $ret = $stmt->execute();

    // If query failed, tell why.
    $result = NULL;
    if(!$ret) {
        error_log($stmt->errorInfo());
        http_response_code(500);
    }

    $ret = $stmt->fetchAll();
    if (count($ret) == 0) {
        // User does not exist.
        // echo "USER DOES NOT EXIST";
        http_response_code(404);
    } else if(count($ret) > 1) {
        // More than one user with that username! Should not happen!
        // echo "NOT UNIQUE???";
        http_response_code(409);
    } else {
        $row = $ret[0];
        $result = $row['authentication_level'];
    }
    return $result;

}

function login_with_credentials($username, $password) {
    $sql = "SELECT * from users where username == :usr_nm";
    $db = connect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':usr_nm', $username, PDO::PARAM_STR);
    $ret = $stmt->execute();

    // If query failed, tell why.
    if(!$ret) {
        error_log($stmt->errorInfo());
        http_response_code(500);
        die();
    }

    $ret = $stmt->fetchAll();
    if (count($ret) == 0) {
        // User does not exist.
        // echo "USER DOES NOT EXIST";
        http_response_code(404);
        die();
    } else if(count($ret) > 1) {
        // More than one user with that username! Should not happen!
        // echo "NOT UNIQUE???";
        http_response_code(409);
        die();
    } else {
        $row = $ret[0];
        $hash = $row['password'];

        if (password_verify($password, $hash)) {
            $res = array(
                "id" => $row['user_id'],
                "username" => $row['username'],
                "password" => "NULL",
                "email" => $row['email'],
                "fullname" => $row['full_name'],
                // "authenticationLevel" => $row['authentication_level'],
                // "groups" => []
            );
            // Generate the cookie.
            // error_log("GEN TOKEN");
            
            $len_seconds_valid = 86400 * 30;

            $tkn = generate_jwt($row['username'], $hash, $res, $len_seconds_valid);
            // error_log($tkn);
            
            // The last "" is for testing on localhost.
            $r = setcookie("ME", base64_encode($tkn), time() + $len_seconds_valid, "", '.localhost');

            // Return the user information.
            echo json_encode($res);
        } else {
            http_response_code(401);
            die();
        }
    }

}

function log_out() {
    // Clear cookie if it is there.
    if (isset($_COOKIE['ME'])) {
        unset($_COOKIE['ME']); 
        setcookie("ME", "", time() - 3600);
    }
}

if (isset($_POST['credentials']) ) {
    // In case we are changing user.
    log_out();

    error_log("cred");
    $content = $_POST["credentials"];
    $obj = json_decode($content);
    login_with_credentials($obj->username, $obj->password);
} else if (isset($_GET['cookie'])) {
    // error_log("cookie");
    $info = get_current_user_info();
    if($info == NULL) {
        http_response_code(404);
        die();
    } else {
        echo json_encode($info->data);
    }
}  else if (isset($_GET['log_out'])) {
    log_out();
}


?>