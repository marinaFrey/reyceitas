<?php
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