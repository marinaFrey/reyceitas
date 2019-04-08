<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
require 'connect.php';

function list_all_tags() 
{
    $db = connect();
    $sql = "SELECT * FROM tags;";
    $ret = $db->query($sql);

    $i = 0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['id']=$row['tag_id'];
        $resArrVals[$i]['name']=$row['name'];
        $resArrVals[$i]['icon']=$row['icon'];
        $resArrVals[$i]['color']=$row['color'];

        $i += 1;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function rm_tag($tag_id)
{
    $db = connect();
    echo $tag_id;
    $sql = "DELETE FROM tags WHERE tag_id = :tag_id;"; 
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':tag_id', $tag_id, PDO::PARAM_INT);
    $ret = $stmt->execute();
    if(!$ret) 
    {
        echo json_encode($stmt->errorInfo());
    }

}
function add_tag($tagJson)
{
    $db = connect();

    $tag = json_decode($tagJson);
    echo $tagJson;
    $sql = "INSERT INTO tags (name, icon, color) VALUES (:name, :icon, :color);";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':name', $tag->name, PDO::PARAM_STR);
    $stmt->bindValue(':icon', $tag->icon, PDO::PARAM_STR);
    $stmt->bindValue(':color', $tag->color, PDO::PARAM_STR);
    $ret = $stmt->execute();
    if(!$ret) 
    {
        echo json_encode($stmt->errorInfo());
    }

}
function edit_tag($tagId,$tagJson)
{
    $db = connect();
    $tag = json_decode($tagJson);
    $sql = "UPDATE tags SET name = :name, icon = :icon, color = :color WHERE tag_id = :tag_id;";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':tag_id', $tagId, PDO::PARAM_INT);
    $stmt->bindValue(':name', $tag->name, PDO::PARAM_STR);
    $stmt->bindValue(':icon', $tag->icon, PDO::PARAM_STR);
    $stmt->bindValue(':color', $tag->color, PDO::PARAM_STR);
    $ret = $stmt->execute();
    if(!$ret) 
    {
        echo json_encode($stmt->errorInfo());
    }
}
if (isset($_GET['add_tag'])) {
    add_tag($_GET['add_tag']);
} elseif (isset($_GET['edit_tag_id']) && isset($_GET['tag'])) {
    edit_tag($_GET['edit_tag_id'],$_GET['tag']);
} elseif (isset($_GET['rm_tag_id'])){
    rm_tag($_GET['rm_tag_id']);
} else {
    list_all_tags();
}
