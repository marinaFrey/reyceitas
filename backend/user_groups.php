<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");

header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require 'connect.php';
require 'login2.php';

function get_user_groups($user_id)
{

    // At least be logged in.
    if(!is_logged_as_a_valid_user()) {
        http_response_code(403);
        die();
    }

    $db = connect();
    $sql = "SELECT group_id FROM user_groups WHERE user_id = :user_id;"; 
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $ret = $stmt->execute();
    $ret = $stmt->fetchAll();
    $i = 0;
    foreach($ret as $row)
    {
        $resArrVals[$i]=$row['group_id'];
        $i++;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }

}
function set_user_groups($user_id, $groupsJson)
{

    // Only root can edit groups.
    $usr_info = get_current_user_info();
    if($usr_info == NULL || $usr_info->aud != "root") {
        http_response_code(403);
        die();
    }

    $db = connect();
    $sql = "DELETE FROM user_groups WHERE user_id = :usr_id";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':usr_id', $user_id, PDO::PARAM_INT);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    $sql = "INSERT INTO user_groups (user_id, group_id) VALUES (:usr_id, :group_id);";
    $stmt2= $db->prepare($sql);
    $stmt2->bindValue(':usr_id', $user_id, PDO::PARAM_INT);
    $groups = json_decode($groupsJson);
    foreach($groups as $group_id)
    {
        $stmt2->bindValue(':group_id', $group_id, PDO::PARAM_INT);
        $ret2 = $stmt2->execute();
        if(!$ret2) 
        {
            echo json_encode($stmt2->errorInfo());
        }
    }

}





if (isset($_GET['get_groups_user'])) {
    get_user_groups($_GET['get_groups_user']);
} elseif (isset($_GET['set_groups_user']) && isset($_GET['groups'])) {
    set_user_groups($_GET['set_groups_user'], $_GET['groups']);
}
