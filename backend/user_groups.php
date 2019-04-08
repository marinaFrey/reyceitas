<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
require 'connect.php';

function get_user_groups($user_id)
{
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
