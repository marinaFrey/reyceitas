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
        $resArrVals[$i]['id']=$row['user_id'];
        $resArrVals[$i]['username']=$row['username'];
        $resArrVals[$i]['password']=$row['password'];
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
        $resArrVals[$i]['id']=$row['user_id'];
        $resArrVals[$i]['username']=$row['username'];
        $resArrVals[$i]['password']=$row['password'];
        $resArrVals[$i]['email']=$row['email'];
        $resArrVals[$i]['fullname']=$row['full_name'];
        $resArrVals[$i]['authenticationLevel']=$row['authentication_level'];
        
        $i++;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
if ($_GET) {
    get_user($_GET['username']);
} else {
    list_all_users();
}
?>
