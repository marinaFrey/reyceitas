<?php
header("Access-Control-Allow-Origin: *");
require 'connect.php';

function list_all_users() {
    $db = connect();
    $sql = "SELECT * FROM users";
    $mapIdToData [] = array();
    $ret = $db->query($sql);

    $i=0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['id']=$row['id'];
        $resArrVals[$i]['username']=$row['username'];
        $resArrVals[$i]['password']=$row['password'];
        $resArrVals[$i]['email']=$row['email'];
        $resArrVals[$i]['fullname']=$row['username'];
        $resArrVals[$i]['authenticationLevel']= 1; //$row['authentication_level'];
        
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
    $sql = "SELECT * FROM users where id = :id" ;
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    $ret = $stmt->execute();
    $ret = $stmt->fetchAll();

    foreach($ret as $row)
    {
    echo json_encode($row['username']);
    }
}
function get_user($username) {
    $db = connect();
    $sql = "SELECT * FROM users where username = :usr_nm" ;
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':usr_nm', $username, PDO::PARAM_STR);
    $ret = $stmt->execute();
    $ret = $stmt->fetchAll();


    $i=0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['id']=$row['id'];
        $resArrVals[$i]['username']=$row['username'];
        $resArrVals[$i]['password']=$row['password'];
        $resArrVals[$i]['email']=$row['email'];
        $resArrVals[$i]['fullname']= $row['username']; // $row['full_name'];
        $resArrVals[$i]['authenticationLevel']= 1;//$row['authentication_level'];
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
