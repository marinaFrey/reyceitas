<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require 'connect.php';


$str_json = file_get_contents('php://input');
$obj = json_decode($str_json);
echo json_encode($obj->id);
$db = connect();
$sql =<<<EOF
INSERT INTO users (user_id, username, full_name, email, password) VALUES ('$obj->id','$obj->username','$obj->fullname','$obj->email', '$obj->password');
EOF;
$ret = $db->query($sql);
return $ret; 
?>
