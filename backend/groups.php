<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
require 'connect.php';

function list_all_groups() 
{
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
    $db = connect();
    echo $group_id;
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
    $db = connect();

    $group = json_decode($groupJson);
    echo $groupJson;
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
