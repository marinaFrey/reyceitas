<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    require 'connect.php';
    
    if (isset($_GET['id']))
    {
        $id = $_GET['id'];

        $db = connect();
/*
        $sql = "DELETE FROM recipes WHERE recip_id = $id";
        $db->query($sql);
        $sql = "DELETE FROM recipe_contributiors WHERE src_recip = $id";
        $db->query($sql);
        $sql = "DELETE FROM recipe_ingredients WHERE src_recip = $id";
        $db->query($sql);
        $sql = "DELETE FROM recipe_steps WHERE src_recip = $id";
        $db->query($sql);
        $sql = "DELETE FROM recipe_tags WHERE src_recip = $id";
        $db->query($sql);*/
    }

?>