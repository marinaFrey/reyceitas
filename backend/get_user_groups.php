<?php
header("Access-Control-Allow-Origin: *");
require 'connect.php';

function list_all_user_groups() 
{
    $db = connect();
    $sql = "SELECT * FROM user_groups ug INNER JOIN users u ON ug.user_id = u.user_id INNER JOIN groups g ON ug.group_id = g.group_id;";
    $ret = $db->query($sql);

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
} else {
    list_all_user_groups();
}
?>
