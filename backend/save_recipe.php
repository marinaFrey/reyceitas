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
            VALUES ('$recipe->servings', '$recipe->name', '$recipe->difficulty', '$recipe->servings', '$recipe->duration', '$recipe->description' );
EOF;
        $db = connect();
        $ret = $db->query($sql);
        if(!$ret) {
            echo $db->lastErrorMsg();
        }

        $recipe_id = $db->lastInsertRowId();
        
        foreach($recipe->photos as $photo)
        {
            $sql = <<<EOF
            INSERT INTO recipe_pictures (src_recipe, file_name)
            VALUES ('$recipe_id', '$photo');
EOF;
        
            $ret2 = $db->query($sql);
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
        }

        foreach($recipe->ingredients as $ingredient)
        {
            $sql = <<<EOF
            INSERT INTO recipe_ingredients (src_recipe, quantity, unit_name, description)
            VALUES ('$recipe_id', '$ingredient->amount', '$ingredient->unit', '$ingredient->name');
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
            VALUES ('$recipe_id', '$numberOfStep', '$step');
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
            VALUES ('$recipe_id', '$tag');
EOF;
        
            $ret2 = $db->query($sql);
            if(!$ret2) {
                echo $db->lastErrorMsg();
            }
        }

    }

?>