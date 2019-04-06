<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require 'connect.php';
require __DIR__ . '/vendor/autoload.php';

if (isset($_GET['credentials']))
{
    $creds = json_decode($_GET['credentials']);
    $db = connect();
    $auth = new \Delight\Auth\Auth($db);

    try {
        // Remember user for 30 days.
        $auth->loginWithUsername($creds->username, $creds->password, 2592000);
        $userId = $auth->getUserId();
        echo "Logged is " . $userId;
        
    }
    catch (\Delight\Auth\AmbiguousUsernameException $e) {
        die('the specified username is ambiguous, i.e. there are multiple users with that name');
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        die('Invalid password');
    }
    catch (\Delight\Auth\EmailNotVerifiedException $e) {
        die('the email address has not been verified yet via confirmation email');
    }
    catch (\Delight\Auth\AttemptCancelledException $e) {
        die('the attempt has been cancelled by the supplied callback that is executed before success');
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        die('Too many requests');
    }
}
?>