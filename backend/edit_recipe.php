<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    require 'connect.php';
    
    if (isset($_GET['recipe']))
    {
        $recipe = json_decode($_GET['recipe']);
        $db = connect();

        $sql = <<<EOF
            UPDATE recipes
            SET name = :recp_nm, difficulty = :recp_lvl, n_served = :n_served,
                duration = :dur, description = :descr
            WHERE recipe_id = :recp_id;
EOF;

        $db = connect();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_nm', $recipe->name, SQLITE3_TEXT);
        $stmt->bindValue(':recp_lvl', $recipe->difficulty, SQLITE3_INTEGER);
        $stmt->bindValue(':n_served', $recipe->servings, SQLITE3_INTEGER);
        $stmt->bindValue(':dur', $recipe->duration, SQLITE3_TEXT);
        $stmt->bindValue(':descr', $recipe->description, SQLITE3_TEXT);
        $stmt->bindValue(':recp_id', $recipe->id, SQLITE3_INTEGER);
        $ret = $stmt->execute();

        $ret = $db->exec($sql);
        if(!$ret) {
            echo $db->lastErrorMsg();
        }
        
        // Clean the current information.
        $arr_sql_delete = array(
            "DELETE FROM recipe_contributors WHERE src_recipe = :recp_id",
            "DELETE FROM recipe_pictures WHERE src_recipe = :recp_id;",
            "DELETE FROM recipe_ingredients WHERE src_recipe = :recp_id;",
            "DELETE FROM recipe_steps WHERE src_recipe = :recp_id;",
            "DELETE FROM recipe_tags WHERE src_recipe = :recp_id;"
        );
        foreach($arr_sql_delete as $sql)
        {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':recp_id', $recipe->id, SQLITE3_INTEGER);
            $ret = $stmt->execute();
            $ret = $db->exec($sql);
            if(!$ret) {
                echo $db->lastErrorMsg();
            }
        }

        $sql = <<<EOF
            INSERT INTO recipe_pictures (src_recipe, file_name)
            VALUES (:recp_id, :pic);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, SQLITE3_INTEGER);
        foreach($recipe->photos as $photo)
        {
            $stmt->bindValue(':pic', $photo, SQLITE3_TEXT);
            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
        }

        $sql = <<<EOF
            INSERT INTO recipe_ingredients (src_recipe, quantity, unit_name, description)
            VALUES (:recp_id, :amount, :unit, :description);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, SQLITE3_INTEGER);
        foreach($recipe->ingredients as $ingredient)
        {
            $stmt->bindValue(':amount', floatval($ingredient->amount), SQLITE3_FLOAT);
            $stmt->bindValue(':unit', $ingredient->unit, SQLITE3_TEXT);
            $stmt->bindValue(':description', $ingredient->name, SQLITE3_TEXT);

            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
        }

        $numberOfStep = 0;
        $sql = <<<EOF
            INSERT INTO recipe_steps (src_recipe, step_order, description)
            VALUES (:recp_id, :step_numbr, :description);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, SQLITE3_INTEGER);
        foreach($recipe->preparation as $step)
        {
            $stmt->bindValue(':step_numbr', $numberOfStep, SQLITE3_INTEGER);
            $stmt->bindValue(':description', $step, SQLITE3_TEXT);

            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
            $numberOfStep++;
        }

        $sql = <<<EOF
            INSERT INTO recipe_tags (src_recipe, src_tag)
            VALUES (:recp_id, :tag_id);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, SQLITE3_INTEGER);
        foreach($recipe->tags as $tag)
        {
            $stmt->bindValue(':tag_id', $tag, SQLITE3_INTEGER);
            echo $recipe->id;
            echo $tag;
            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
        }


    }

?>