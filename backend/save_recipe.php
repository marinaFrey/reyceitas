<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    require 'connect.php';
    
    if (isset($_GET['recipe']))
    {
        $recipe = json_decode($_GET['recipe']);
        
        
        $sql = <<<EOF
            INSERT INTO recipes (owner, name, difficulty, n_served, duration, description, global_authentication_level)
            VALUES (:owner, :name, :difficulty, :n_served, :duration, :description, :global_authentication_level);
EOF;
        $db = connect();
        $stmt = $db->prepare($sql);
        // TODO: Change once auth is added.
        $stmt->bindValue(':owner', $recipe->userId, PDO::PARAM_INT);
        $stmt->bindValue(':name', $recipe->name, PDO::PARAM_STR);
        $stmt->bindValue(':difficulty', $recipe->difficulty, PDO::PARAM_INT);
        $stmt->bindValue(':n_served', $recipe->servings, PDO::PARAM_INT);
        $stmt->bindValue(':duration', $recipe->duration, PDO::PARAM_STR);
        $stmt->bindValue(':description', $recipe->description, PDO::PARAM_STR);
        $stmt->bindValue(':global_authentication_level', $recipe->globalAuthenticationLevel, PDO::PARAM_INT);

        $ret = $stmt->execute();
        if(!$ret) {
            echo $stmt->errorInfo();
        }
        
        $recipe_id = $db->lastInsertId();
        
        $sql = <<<EOF
        INSERT INTO recipe_pictures (src_recipe, file_name)
        VALUES (:recp_id, :file_name);
EOF;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':recp_id', $recipe_id, PDO::PARAM_INT);
        foreach($recipe->photos as $photo)
        {
            $stmt->bindValue(':file_name', $recipe->duration, PDO::PARAM_STR);
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
        $stmt->bindValue(':recp_id', $recipe_id, PDO::PARAM_INT);
        foreach($recipe->ingredients as $ingredient)
        {
            $stmt->bindValue(':amount', strval($ingredient->amount), PDO::PARAM_STR);
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
        $stmt->bindValue(':recp_id', $recipe_id, PDO::PARAM_INT);
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
        $stmt->bindValue(':recp_id', $recipe_id, PDO::PARAM_INT);
        foreach($recipe->tags as $tag)
        {
            $stmt->bindValue(':tag_id', $tag, PDO::PARAM_INT);
            // echo $recipe->id;
            // echo $tag;
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
        $stmt->bindValue(':recp_id', $recipe_id, PDO::PARAM_INT);
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
