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

        //echo $db->last_insert_rowid();

    }

?>