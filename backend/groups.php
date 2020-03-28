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
require 'login.php';

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
if (isset($_GET['add_group'])) {
    add_group($_GET['add_group']);
} elseif (isset($_GET['edit_group']) ) {
    edit_group($_GET['edit_group']);
} elseif (isset($_GET['rm_group_id'])){
    rm_group($_GET['rm_group_id']);
} else {
    list_all_groups();
}
