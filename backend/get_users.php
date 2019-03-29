<?php
header("Access-Control-Allow-Origin: *");
require 'connect.php';

function list_all_users() {
    $db = connect();
    $sql =<<<EOF
    SELECT * FROM users; 
EOF;
    $mapIdToData [] = array();
    $ret = $db->query($sql);

    $i=0;
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $resArrVals[$i]['id']=$row['user_id'];
        $resArrVals[$i]['username']=$row['username'];
        $resArrVals[$i]['password']=$row['password'];
        $resArrVals[$i]['email']=$row['email'];
        $resArrVals[$i]['fullname']=$row['full_name'];
        
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
    $sql =<<<EOF
    SELECT * FROM users where username = '$username'; 
EOF;
    $ret = $db->query($sql);

    $i=0;
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $resArrVals[$i]['id']=$row['user_id'];
        $resArrVals[$i]['username']=$row['username'];
        $resArrVals[$i]['password']=$row['password'];
        $resArrVals[$i]['email']=$row['email'];
        $resArrVals[$i]['fullname']=$row['full_name'];
        
        // Keep this reference recipe id -> its place in the array.
        //$mapIdToData[$resArrVals[$i]['id']] = $i;

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
