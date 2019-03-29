<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    require 'connect.php';

    if (isset($_GET['id']))
    {
        $db = connect();

        $arr_sqls = array("DELETE FROM recipes WHERE recipe_id = :r_id ;", 
            "DELETE FROM recipe_contributors WHERE src_recipe = :r_id ;",
            "DELETE FROM recipe_pictures WHERE src_recipe = :r_id ;",
            "DELETE FROM recipe_ingredients WHERE src_recipe = :r_id ;",
            "DELETE FROM recipe_steps WHERE src_recipe = :r_id ;",
            "DELETE FROM recipe_tags WHERE src_recipe = :r_id ;);" );
        foreach ($arr_sqls as $sql) {
            $stmt = $db->prepare($sql);
            $r_id = intval($_GET['id']);
            $stmt->bindValue(':r_id', $r_id, SQLITE3_INTEGER);
            $ret = $stmt->execute();
        }

        // if(!$ret) {
        //     echo $db->lastErrorMsg();
        // }

    }

?>