<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");

// header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require 'connect.php';
require 'login2.php';

function list_all_groups() 
{
    // No listing groups if you are not root.
    if(!is_logged_already_as("root")) {
        http_response_code(403);
        die();
    }

    if(!is_logged_as_a_valid_user()) {
        http_response_code(403);
        die();
    }

    $db = connect();
    $sql = "SELECT * FROM groups;";
    $ret = $db->query($sql);

    $i = 0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['id']=$row['group_id'];
        $resArrVals[$i]['name']=$row['name'];
        $i += 1;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function rm_group($group_id)
{

    // No removing groups if you are not root
    if(!is_logged_already_as("root")) {
        http_response_code(403);
        die();
    }

    $db = connect();
    $sql = "DELETE FROM groups WHERE group_id = :group_id;"; 
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':group_id', $group_id, PDO::PARAM_INT);
    $ret = $stmt->execute();
    if(!$ret) 
    {
        echo json_encode($stmt->errorInfo());
    }

}
function add_group($groupJson)
{

    // No adding groups if you are not root
    if(!is_logged_already_as("root")) {
        http_response_code(403);
        die();
    }

    $db = connect();

    $group = json_decode($groupJson);
    $sql = "INSERT INTO groups (name) VALUES (:name);";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':name', $group->name, PDO::PARAM_STR);
    $ret = $stmt->execute();
    if(!$ret) 
    {
        echo json_encode($stmt->errorInfo());
    } else {
        $last_id = $db->lastInsertId();
        echo $last_id;
        return $last_id; 
    }

}
function edit_group($groupJson)
{

    // No editing groups if you are not root
    if(!is_logged_already_as("root")) {
        http_response_code(403);
        die();
    }

    $db = connect();
    $group = json_decode($groupJson);
    $sql = "UPDATE groups SET name = :name WHERE group_id = :group_id;";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':group_id', $group->id, PDO::PARAM_INT);
    $stmt->bindValue(':name', $group->name, PDO::PARAM_STR);
    $ret = $stmt->execute();
    if(!$ret) 
    {
        echo json_encode($stmt->errorInfo());
    }
}

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

function get_groups_per_user($username) 
{
    $db = connect();
    $sql = "SELECT * FROM user_groups ug INNER JOIN users u ON ug.user_id = u.user_id INNER JOIN groups g ON ug.group_id = g.group_id WHERE username = :username;";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    $i = 0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['user_group_id']=$row['user_group_id'];
        $resArrVals[$i]['user_id']=$row['user_id'];
        $resArrVals[$i]['user_name']=$row['username'];
        $resArrVals[$i]['group_id']=$row['group_id'];
        $resArrVals[$i]['group_name']=$row['name'];

        $i += 1;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function get_users_per_group($group_name) 
{
    $db = connect();
    $sql = "SELECT * FROM user_groups ug INNER JOIN users u ON ug.user_id = u.user_id INNER JOIN groups g ON ug.group_id = g.group_id WHERE name = :group_name;";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':group_name', $group_name, PDO::PARAM_STR);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    $i = 0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['user_group_id']=$row['user_group_id'];
        $resArrVals[$i]['user_id']=$row['user_id'];
        $resArrVals[$i]['user_name']=$row['username'];
        $resArrVals[$i]['group_id']=$row['group_id'];
        $resArrVals[$i]['group_name']=$row['name'];

        $i += 1;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}


if (isset($_GET['username'])) {
    get_groups_per_user($_GET['username']);
} elseif (isset($_GET['group_name'])) {
    get_users_per_group($_GET['group_name']);
} else if (isset($_GET['get_groups_user'])) {
    get_user_groups($_GET['get_groups_user']);
} elseif (isset($_GET['set_groups_user']) && isset($_GET['groups'])) {
    set_user_groups($_GET['set_groups_user'], $_GET['groups']);
} elseif (isset($_GET['add_group'])) {
    add_group($_GET['add_group']);
} elseif (isset($_GET['edit_group']) ) {
    edit_group($_GET['edit_group']);
} elseif (isset($_GET['rm_group_id'])){
    rm_group($_GET['rm_group_id']);
} else {
    list_all_groups();
}
