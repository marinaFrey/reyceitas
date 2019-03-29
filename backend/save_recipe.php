<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    require 'connect.php';
    
    if (isset($_GET['recipe']))
    {
        $recipe = json_decode($_GET['recipe']);
        //echo json_encode($recipe->name);
        
        $sql = <<<EOF
            INSERT INTO recipes (owner, name, difficulty, n_served, duration, description)
            VALUES (:owner, :name, :difficulty, :n_served, :duration, :description);
EOF;
        $db = connect();
        $stmt = $db->prepare($sql);
        // TODO: Change once auth is added.
        $stmt->bindValue(':owner', $recipe->servings, SQLITE3_INTEGER);
        $stmt->bindValue(':name', $recipe->name, SQLITE3_TEXT);
        $stmt->bindValue(':difficulty', $recipe->difficulty, SQLITE3_INTEGER);
        $stmt->bindValue(':n_served', $recipe->servings, SQLITE3_INTEGER);
        $stmt->bindValue(':duration', $recipe->duration, SQLITE3_TEXT);
        $stmt->bindValue(':description', $recipe->duration, SQLITE3_TEXT);

        $ret = $stmt->execute();
        if(!$ret) {
            echo $db->lastErrorMsg();
        }

        $recipe_id = $db->lastInsertRowId();
        
        $sql = <<<EOF
        INSERT INTO recipe_pictures (src_recipe, file_name)
        VALUES (:recp_id, :file_name);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe_id, SQLITE3_INTEGER);
        foreach($recipe->photos as $photo)
        {
            $stmt->bindValue(':file_name', $recipe->duration, SQLITE3_TEXT);
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
        $stmt->bindValue(':recp_id', $recipe_id, SQLITE3_INTEGER);
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
        $stmt->bindValue(':recp_id', $recipe_id, SQLITE3_INTEGER);
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
        $stmt->bindValue(':recp_id', $recipe_id, SQLITE3_INTEGER);
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

        echo $recipe_id;

    }

?>