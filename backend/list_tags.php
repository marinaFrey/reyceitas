<?php

header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require 'connect.php';

$db = connect();

$sql =<<<EOF
    SELECT tag_id, name, icon, color FROM tags;
EOF;

$resArrVals = array();
$ret = $db->query($sql);
foreach($ret as $row)
{
    $tag = array( "id" => $row["tag_id"], "name" => $row["name"],
            "icon" => $row["icon"],"color" => $row["color"]);
    $v = array_push($resArrVals, $tag);
}

echo json_encode($resArrVals);
exit;

?>