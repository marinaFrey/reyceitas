<?php
// header("Access-Control-Allow-Origin: *");

    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    // header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
require 'connect.php';

function list_all_groups() 
{
    $db = connect();
    $sql = "SELECT * FROM groups";
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
list_all_groups();
?>