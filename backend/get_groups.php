<?php
header("Access-Control-Allow-Origin: *");
require 'connect.php';

function list_all_groups() 
{
    $db = connect();
    $sql = "SELECT * FROM groups";
    $ret = $db->query($sql);

    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC); $i++)
    {
        $resArrVals[$i]['id']=$row['group_id'];
        $resArrVals[$i]['name']=$row['name'];
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
list_all_groups();
?>
