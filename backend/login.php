<?php
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Credentials: true');
// header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, Authorization, X-Requested-With');

// header('Access-Control-Allow-Headers: Content-Type, *');

//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Headers: *");
//header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

require_once 'connect.php';
// error_log($_SERVER['HTTP_ORIGIN']);


function verify($jwt_access_token) {

    error_log("verify!!");    

    // Returns NULL if it is not verified.
    $info_signed = NULL;    
    $separator = '.';
    if (2 !== substr_count($jwt_access_token, $separator)) {
            error_log("Incorrect substr!!");
            throw new Exception("Incorrect access token format");
    }

    list($header, $payload, $signature) = explode($separator, $jwt_access_token);
    $decoded_signature = base64_decode(str_replace(array('-', '_'), array('+', '/'), $signature));

    // The header and payload are signed together
    $payload_to_verify = utf8_decode($header . $separator . $payload);

    // however you want to load your public key
    $public_key = file_get_contents('./secrets/keys/pubkey.pem');

    // default is SHA256
    $verified = openssl_verify($payload_to_verify, $decoded_signature, $public_key, OPENSSL_ALGO_SHA256);
    if ($verified !== 1) {
            error_log("Incorrect signature!");
            throw new Exception("Cannot verify signature");
    } else {
            $info_signed = json_decode(base64_decode($payload));
            error_log(base64_decode($payload));
            error_log("YAAAYY!!");
    }
    return $info_signed;
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
        error_log($_COOKIE['ME']);
        // $o = json_decode($_COOKIE['ME']);

        error_log('c');
        $info_signed = verify( base64_decode($_COOKIE['ME']));
        // error_log($o->access_token);
        error_log('c');

        if($info_signed != NULL && $info_signed->aud == $username) {
            $is_logged = TRUE;
        }

    }
    return $is_logged;
}


function generate_jwt($user_id,  $user_pwd, $stored_info){
    //ini_set('display_errors',1);error_reporting(E_ALL);

    $_POST["client_secret"] = $user_pwd;
    // error_log($_POST["client_secret"]);
    // error_log("                                        ");
    // error_log($user_pwd);
    

    require_once('./oauth2-server-php/src/OAuth2/Autoloader.php');
    OAuth2\Autoloader::register();

    $publicKey  = file_get_contents('./secrets/keys/pubkey.pem');
    $privateKey = file_get_contents('./secrets/keys/privkey.pem');

    // create storage
    $storage = new OAuth2\Storage\Memory(array(
        'keys' => array(
        'public_key'  => $publicKey,
        'private_key' => $privateKey,
        ),
        // add a Client ID for testing
        'client_credentials' => array(
            $user_id => array('client_secret' => $user_pwd)
        ) ,
        'default_scope' => $stored_info,
    ));

    $server = new OAuth2\Server($storage, array(
        'use_jwt_access_tokens' => true,
    ));
    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

    $result = $server->handleTokenRequest(OAuth2\Request::createFromGlobals());

    $token = $result->getParameter('access_token');
    error_log('v');
    error_log($token);
    error_log('v');

    return $token;
    // return json_decode($result->getResponseBody());
    // return $result->__toString();
}




if (isset($_GET['cookie'])) {
    error_log("cookie");
    $info = get_current_user_info();
    if($info == NULL) {
        http_response_code(404);
        die();
    } else {
        echo json_encode($info->scope);
    }

} else if (isset($_POST['credentials']) ) {
    $content = $_POST["credentials"];
    $obj = json_decode($content);

    if ( is_logged_already_as($obj->username) ) {
        error_log("SHORT!");
        // return "";
    }


    $sql = "SELECT * from users where username == :usr_nm";
    $db = connect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':usr_nm', $obj->username, PDO::PARAM_STR);
    $ret = $stmt->execute();

    
    // If query failed, tell why.
    if(!$ret) {
        echo -1;
        echo $stmt->errorInfo();
        return -1;
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
        if (password_verify($obj->password, $hash)) {

            $res = array(
                "id" => $row['user_id'],
                "username" => $row['username'],
                "password" => "NULL",
                "email" => $row['email'],
                "fullname" => $row['full_name'],
                "authenticationLevel" => $row['authentication_level'],
                "groups" => []
            );
            // Generate the cookie.
            // error_log("GEN TOKEN");
            $tkn = generate_jwt($row['username'], $hash, $res);
            // error_log($tkn);
            
            // The last "" is for testing on localhost.
            $r = setcookie("ME", base64_encode($tkn), time() + (86400 * 30), "",
            '.localhost');
            // error_log("CK? ");
            // error_log($r);


            //$cookie_info = generate_jwt($row['user_id'], $hash);
            //$res["lol"] = $cookie_info; 


            // Return the user information.
            echo json_encode($res);
        } else {
            http_response_code(401);
            die();
        }
    }
}



?>