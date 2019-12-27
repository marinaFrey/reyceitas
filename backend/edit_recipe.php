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
                duration = :dur, description = :descr, global_authentication_level = :global_authentication_level
            WHERE recipe_id = :recp_id;
EOF;

        $db = connect();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_nm', $recipe->name, PDO::PARAM_STR);
        $stmt->bindValue(':recp_lvl', $recipe->difficulty, PDO::PARAM_INT);
        $stmt->bindValue(':n_served', $recipe->servings, PDO::PARAM_INT);
        $stmt->bindValue(':dur', $recipe->duration, PDO::PARAM_STR);
        $stmt->bindValue(':descr', $recipe->description, PDO::PARAM_STR);
        $stmt->bindValue(':recp_id', $recipe->id, PDO::PARAM_INT);
        $stmt->bindValue(':global_authentication_level', $recipe->globalAuthenticationLevel, PDO::PARAM_INT);
        $ret = $stmt->execute();

        if(!$ret) {
            echo $stmt->errorInfo();
        }
        
        // Clean the current information.
        $arr_sql_delete = array(
            "DELETE FROM recipe_contributors WHERE src_recipe = :recp_id",
            "DELETE FROM recipe_pictures WHERE src_recipe = :recp_id;",
            "DELETE FROM recipe_ingredients WHERE src_recipe = :recp_id;",
            "DELETE FROM recipe_steps WHERE src_recipe = :recp_id;",
            "DELETE FROM recipe_tags WHERE src_recipe = :recp_id;",
            "DELETE FROM recipe_permissions WHERE recipe_id = :recp_id;"
        );
        foreach($arr_sql_delete as $sql)
        {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':recp_id', $recipe->id, PDO::PARAM_INT);
            $ret = $stmt->execute();
            
            if(!$ret) {
                echo $stmt->errorInfo();
            }
        }

        $sql = <<<EOF
            INSERT INTO recipe_pictures (src_recipe, file_name)
            VALUES (:recp_id, :pic);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, PDO::PARAM_INT);
        foreach($recipe->photos as $photo)
        {
            $stmt->bindValue(':pic', $photo, PDO::PARAM_STR);
            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $stmt->errorInfo();
            }
        }

        $sql = <<<EOF
            INSERT INTO recipe_ingredients (src_recipe, quantity, unit_name, description)
            VALUES (:recp_id, :amount, :unit, :description);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, PDO::PARAM_INT);
        foreach($recipe->ingredients as $ingredient)
        {
            // No float parameters in PDO.
            $stmt->bindValue(':amount', $ingredient->amount, PDO::PARAM_STR);
            $stmt->bindValue(':unit', $ingredient->unit, PDO::PARAM_STR);
            $stmt->bindValue(':description', $ingredient->name, PDO::PARAM_STR);

            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $stmt->errorInfo();
            }
        }

        $numberOfStep = 0;
        $sql = <<<EOF
            INSERT INTO recipe_steps (src_recipe, step_order, description)
            VALUES (:recp_id, :step_numbr, :description);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, PDO::PARAM_INT);
        foreach($recipe->preparation as $step)
        {
            $stmt->bindValue(':step_numbr', $numberOfStep, PDO::PARAM_INT);
            $stmt->bindValue(':description', $step, PDO::PARAM_STR);

            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $stmt->errorInfo();
            }
            $numberOfStep++;
        }

        $sql = <<<EOF
            INSERT INTO recipe_tags (src_recipe, src_tag)
            VALUES (:recp_id, :tag_id);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, PDO::PARAM_INT);
        foreach($recipe->tags as $tag)
        {
            $stmt->bindValue(':tag_id', $tag, PDO::PARAM_INT);
            echo $recipe->id;
            echo $tag;
            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $stmt->errorInfo();
            }
        }

        $sql = <<<EOF
            INSERT INTO recipe_permissions (recipe_id, group_id, authentication_level)
            VALUES (:recp_id, :group_id, :authentication_level);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe->id, PDO::PARAM_INT);
        foreach($recipe->groupsAuthenticationLevel as $group)
        {
            $stmt->bindValue(':group_id', $group->groupId, PDO::PARAM_STR);
            $stmt->bindValue(':authentication_level', $group->authenticationLevel, PDO::PARAM_STR);
            $ret2 = $stmt->execute();
            if(!$ret2) {
                echo $stmt->errorInfo();
            }
        }


    }

?>
