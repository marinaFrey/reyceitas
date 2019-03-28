<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    require 'connect.php';
    
    if (isset($_GET['id']))
    {
        $id = $_GET['id'];

        $sql = <<<EOF
            DELETE FROM recipes WHERE recipe_id = '$id';
            DELETE FROM recipe_contributors WHERE src_recipe = '$id';
            DELETE FROM recipe_pictures WHERE src_recipe = '$id';
            DELETE FROM recipe_ingredients WHERE src_recipe = '$id';
            DELETE FROM recipe_steps WHERE src_recipe = '$id';
            DELETE FROM recipe_tags WHERE src_recipe = '$id';
EOF;
        $db = connect();
        $ret = $db->exec($sql);
        if(!$ret) {
            echo $db->lastErrorMsg();
        }

    }

?>