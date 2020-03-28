<?php
// header("Access-Control-Allow-Origin: *");

header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');


require 'connect.php';
require 'login.php';

function list_all_users() {

    // Only root can list all users.
    // if(!is_logged_already_as("root")) {
    //     http_response_code(403);
    //     die();
    // }

    $db = connect();
    $sql = "SELECT * FROM users";
    $mapIdToData [] = array();
    $ret = $db->query($sql);

    $i=0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['id']=$row['user_id'];
        $resArrVals[$i]['username']=$row['username'];
        // No way we are giving the client any password information.
        $resArrVals[$i]['password']='';//$row['password'];
        $resArrVals[$i]['email']=$row['email'];
        $resArrVals[$i]['fullname']=$row['full_name'];
        $resArrVals[$i]['authenticationLevel']=$row['authentication_level'];
        
        // Keep this reference recipe id -> its place in the array.
        //$mapIdToData[$resArrVals[$i]['id']] = $i;

        $i++;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function get_user_by_id($id) {
    $db = connect();
    $sql = "SELECT * FROM users where user_id = :id" ;
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    $ret = $stmt->execute();
    $ret = $stmt->fetchAll();

    // Who am I logged in as?
    $usr_info = get_current_user_info();
    // I have to be someone.
    if($usr_info == NULL) {
        http_response_code(403);
        die();
    }
    $usr = $usr_info->aud;

    // Root can get anyone.
    if($usr == "root") 
    {
        foreach($ret as $row) {
            // Not even to root we are giving the client any password information.
            $row['password']='';
            echo json_encode($row['username']);
        }
    } else
    {
        // Others can see only themselves.
        foreach($ret as $row)
        {
            // Only give me my own information.
            if(!is_logged_already_as($row['username'])) {
                http_response_code(403);
                die();
            }

            // No way we are giving the client any password information.
            $row['password']='';
            echo json_encode($row['username']);
        }
    }

    
}
function get_user($username) {

    $usr_info = get_current_user_info();
    // I have to be logged in as either root as that user.
    if($usr_info == NULL || ($usr_info->aud != "root" && $usr_info->aud != $username) ) {
        http_response_code(403);
        die();
    }



    $db = connect();
    $sql = "SELECT * FROM users where username = :usr_nm" ;
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':usr_nm', $username, PDO::PARAM_STR);
    $ret = $stmt->execute();
    $ret = $stmt->fetchAll();


    $i=0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['id']=$row['user_id'];
        $resArrVals[$i]['username']=$row['username'];
        // No way we are giving the client any password information.
        $resArrVals[$i]['password']='';//$row['password'];
        $resArrVals[$i]['email']=$row['email'];
        $resArrVals[$i]['fullname']=$row['full_name'];
        $resArrVals[$i]['authenticationLevel']=$row['authentication_level'];
        $resArrVals[$i]['groups'] = array();

        $sql_groups = "SELECT * FROM user_groups where user_id = :usr_id" ;
        $stmt_groups = $db->prepare($sql_groups);
        $stmt_groups->bindValue(':usr_id', $row['user_id'], PDO::PARAM_STR);
        $ret_groups = $stmt_groups->execute();
        $ret_groups = $stmt_groups->fetchAll();
        foreach($ret_groups as $row_groups)
        {
            $v = array_push($resArrVals[$i]['groups'],$row_groups['group_id']);
        }
        $i++;
    }
   

    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
if (isset($_GET['username'])) {
    get_user($_GET['username']);
}elseif (isset($_GET['id'])) {
    get_user_by_id($_GET['id']);
} else {
    list_all_users();
}
?>
