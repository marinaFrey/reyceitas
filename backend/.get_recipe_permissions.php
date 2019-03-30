<?php
header("Access-Control-Allow-Origin: *");
require 'connect.php';

function list_all_recipe_permissions() 
{
    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name, rp.authentication_level AS group_authentication_level FROM recipe_permissions AS rp";
    $sql.= " INNER JOIN user_groups AS ug ON rp.group_id = ug.group_id";
    $sql.= " INNER JOIN users AS u ON ug.user_id = u.user_id";
    $sql.= " INNER JOIN groups AS g ON ug.group_id = g.group_id";
    $sql.= " INNER JOIN recipes AS r ON rp.recipe_id = r.recipe_id";
    //$sql.= " WHERE u.username = :username;"
    $ret = $db->query($sql);

    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC); $i++)
    {
        $resArrVals[$i]['recipe_permissions_id']=$row['recipe_permissions_id'];
        $resArrVals[$i]['recipe_id']=$row['recipe_id'];
        $resArrVals[$i]['recipe_name']=$row['recipe_name'];
        $resArrVals[$i]['authentication_level']=$row['group_authentication_level'];
        $resArrVals[$i]['user_group_id']=$row['user_group_id'];
        $resArrVals[$i]['user_id']=$row['user_id'];
        $resArrVals[$i]['user_name']=$row['username'];
        $resArrVals[$i]['group_id']=$row['group_id'];
        $resArrVals[$i]['group_name']=$row['name'];
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function get_recipes_per_user($username) 
{
    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name, rp.authentication_level AS group_authentication_level FROM recipe_permissions AS rp";
    $sql.= " INNER JOIN user_groups AS ug ON rp.group_id = ug.group_id";
    $sql.= " INNER JOIN users AS u ON ug.user_id = u.user_id";
    $sql.= " INNER JOIN groups AS g ON ug.group_id = g.group_id";
    $sql.= " INNER JOIN recipes AS r ON rp.recipe_id = r.recipe_id";
    $sql.= " WHERE u.username = :username;";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $ret= $stmt->execute();

    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC); $i++)
    {
        $resArrVals[$i]['recipe_permissions_id']=$row['recipe_permissions_id'];
        $resArrVals[$i]['recipe_id']=$row['recipe_id'];
        $resArrVals[$i]['recipe_name']=$row['recipe_name'];
        $resArrVals[$i]['authentication_level']=$row['group_authentication_level'];
        $resArrVals[$i]['user_group_id']=$row['user_group_id'];
        $resArrVals[$i]['user_id']=$row['user_id'];
        $resArrVals[$i]['user_name']=$row['username'];
        $resArrVals[$i]['group_id']=$row['group_id'];
        $resArrVals[$i]['group_name']=$row['name'];
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
if (isset($_GET['username'])) {
    get_recipes_per_user($_GET['username']);
} elseif (isset($_GET['group_name'])) {
    get_recipes_per_group($_GET['group_name']);
} else {
    list_all_recipe_permissions();
}
?>
