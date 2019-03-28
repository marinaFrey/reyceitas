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
            SET name = '$recipe->name', difficulty = '$recipe->difficulty', n_served = '$recipe->servings', duration = '$recipe->duration', description = '$recipe->description'
            WHERE recipe_id = '$recipe->id';

            DELETE FROM recipe_contributors WHERE src_recipe = '$recipe->id';
            DELETE FROM recipe_pictures WHERE src_recipe = '$recipe->id';
            DELETE FROM recipe_ingredients WHERE src_recipe = '$recipe->id';
            DELETE FROM recipe_steps WHERE src_recipe = '$recipe->id';
            DELETE FROM recipe_tags WHERE src_recipe = '$recipe->id';
EOF;
        $ret = $db->exec($sql);
        if(!$ret) {
            echo $db->lastErrorMsg();
        }

        foreach($recipe->ingredients as $ingredient)
        {
            $sql = <<<EOF
            INSERT INTO recipe_ingredients (src_recipe, quantity, unit_name, description)
            VALUES ('$recipe->id', '$ingredient->amount', '$ingredient->unit', '$ingredient->name');
EOF;
            $ret2 = $db->query($sql);
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
        }

        $numberOfStep = 0;
        foreach($recipe->preparation as $step)
        {
            $sql = <<<EOF
            INSERT INTO recipe_steps (src_recipe, step_order, description)
            VALUES ('$recipe->id', '$numberOfStep', '$step');
EOF;
        
            $ret2 = $db->query($sql);
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
            $numberOfStep++;
        }

        foreach($recipe->tags as $tag)
        {
            $sql = <<<EOF
            INSERT INTO recipe_tags (src_recipe, src_tag)
            VALUES ('$recipe->id', '$tag');
EOF;
        
            $ret2 = $db->query($sql);
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
        }


    }

?>