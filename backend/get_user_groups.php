<?php
header("Access-Control-Allow-Origin: *");
require 'connect.php';

function list_all_user_groups() 
{
    $db = connect();
    $sql = "SELECT * FROM user_groups";
    $sql_users= "SELECT username FROM users WHERE user_id = :user_id";
    $stmt_users= $db->prepare($sql_users);
    $sql_groups= "SELECT name FROM groups WHERE group_id = :group_id";
    $stmt_groups= $db->prepare($sql_groups);
    $ret = $db->query($sql);

    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC); $i++)
    {
        $resArrVals[$i]['user_group_id']=$row['user_group_id'];
        $resArrVals[$i]['user_id']=$row['user_id'];
        $resArrVals[$i]['group_id']=$row['group_id'];

        // Get user name
        $stmt_users->bindValue(':user_id', $row['user_id'], SQLITE3_TEXT);
        $ret_users = $stmt_users->execute();
        $user_row = $ret_users->fetchArray(SQLITE3_ASSOC);
        $resArrVals[$i]['user_name']=$user_row['username'];

        //Get group Name
        $stmt_groups->bindValue(':group_id', $row['group_id'], SQLITE3_TEXT);
        $ret_groups = $stmt_groups->execute();
        $user_row = $ret_groups->fetchArray(SQLITE3_ASSOC);
        $resArrVals[$i]['group_name']=$user_row['name'];

    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function get_groups_per_user($username) 
{
    $db = connect();
    $sql = "SELECT * FROM user_groups";

    // Get user id
    $sql_user= "SELECT user_id FROM users WHERE username = :username";
    $stmt_user= $db->prepare($sql_user);
    $stmt_user->bindValue(':username', $username, SQLITE3_TEXT);
    $ret_user = $stmt_user->execute();
    $user_row = $ret_user->fetchArray(SQLITE3_ASSOC);
    $user_id = $user_row['user_id'];

    $sql_groups= "SELECT name FROM groups WHERE group_id = :group_id";
    $stmt_groups= $db->prepare($sql_groups);
    $ret = $db->query($sql);

    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC);)
    {
        if($row['user_id'] == $user_id)
        {
            $resArrVals[$i]['user_group_id']=$row['user_group_id'];
            $resArrVals[$i]['group_id']=$row['group_id'];

            //Get group Name
            $stmt_groups->bindValue(':group_id', $row['group_id'], SQLITE3_TEXT);
            $ret_groups = $stmt_groups->execute();
            $user_row = $ret_groups->fetchArray(SQLITE3_ASSOC);
            $resArrVals[$i]['group_name']=$user_row['name'];
            $i++;
        }

    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function get_users_per_group($group_name) 
{
    $db = connect();
    $sql = "SELECT * FROM user_groups";

    // Get group id
    $sql_group= "SELECT group_id FROM groups WHERE name = :group_name";
    $stmt_group= $db->prepare($sql_group);
    $stmt_group->bindValue(':group_name', $group_name, SQLITE3_TEXT);
    $ret_group = $stmt_group->execute();
    $group_row = $ret_group->fetchArray(SQLITE3_ASSOC);
    $group_id = $group_row['group_id'];

    $sql_users= "SELECT username FROM users WHERE user_id = :user_id";
    $stmt_users= $db->prepare($sql_users);
    $ret = $db->query($sql);

    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC);)
    {
        if($row['group_id'] == $group_id)
        {
            $resArrVals[$i]['user_group_id']=$row['user_group_id'];
            $resArrVals[$i]['user_id']=$row['user_id'];

            // Get user name
            $stmt_users->bindValue(':user_id', $row['user_id'], SQLITE3_TEXT);
            $ret_users = $stmt_users->execute();
            $user_row = $ret_users->fetchArray(SQLITE3_ASSOC);
            $resArrVals[$i]['user_name']=$user_row['username'];
            $i++;
        }

    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
if ($_GET['username']) {
    get_groups_per_user($_GET['username']);
} elseif ($_GET['group_name']) {
    get_users_per_group($_GET['group_name']);
} else {
    list_all_user_groups();
}
?>
