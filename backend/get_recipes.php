<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require 'connect.php';


function fill_tags_for_recipes(&$mapIdToRecip, &$resArrVals) {
    $sql =<<<EOF
    SELECT src_recipe, src_tag, name, icon, color FROM recipe_tags, tags
        WHERE tags.tag_id == recipe_tags.src_tag
        ORDER BY recipe_tags.src_recipe ASC;
EOF;
    $db = connect();
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $tag_id = $row["src_tag"];
        $i_recip = $mapIdToRecip[$row['src_recipe']];
        $v = array_push($resArrVals[$i_recip]['tags'], $tag_id);
    }
}

function fill_ingredients_for_recipes(&$mapIdToRecip, &$resArrVals) {
    $sql =<<<EOF
    SELECT ingr_id, src_recipe, quantity, unit_name, description
    FROM recipe_ingredients
        ORDER BY src_recipe ASC, ingr_id ASC;
EOF;
    $db = connect();
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $ingred = array( "id" => $row["ingr_id"], "name" => $row["description"],
            "amount" => $row["quantity"], "unit" => $row["unit_name"]);
               
        $i_recip = $mapIdToRecip[$row['src_recipe']];
        array_push($resArrVals[$i_recip]['ingredients'], $ingred);
    }
}

function fill_steps_for_recipes(&$mapIdToRecip, &$resArrVals) {
    $sql =<<<EOF
    SELECT src_recipe, description FROM recipe_steps
        ORDER BY src_recipe ASC, step_id ASC;
EOF;
    $db = connect();
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $i_recip = $mapIdToRecip[$row['src_recipe']];
        $v = array_push($resArrVals[$i_recip]['preparation'],
            $row['description']);
    }
}

function fill_photos_for_recipes(&$mapIdToRecip, &$resArrVals) {
    $sql =<<<EOF
    SELECT picture_id, src_recipe, file_name
    FROM recipe_pictures
        ORDER BY src_recipe ASC, picture_id ASC;
EOF;
    $db = connect();
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $i_recip = $mapIdToRecip[$row['src_recipe']];
        $v = array_push($resArrVals[$i_recip]['photos'],
            $row['file_name']);
    }
}

function list_tags() {
    $sql =<<<EOF
    SELECT tag_id, name, icon, color FROM tags;
EOF;

    $resArrVals = array();
    $db = connect();
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $tag = array( "id" => $row["tag_id"], "name" => $row["name"],
            "icon" => $row["icon"],"color" => $row["color"]);
        $v = array_push($resArrVals, $tag);
    }
    echo json_encode($resArrVals);
}

function list_all_recipes() {
    $sql =<<<EOF
    SELECT * FROM recipes;
EOF;
    $resArrVals [] = array(
        "id" => "NULL",
        "name" => "NULL",
        "photos" => array(),
        "duration" => "NULL",
        "difficulty" => "NULL",
        "servings" => "NULL",
        "description" => "NULL",
        "ingredients" => array(),
        "preparation" => array(),
        "tags" => array()
    );
    $mapIdToData [] = array();
    $db = connect();
    $ret = $db->query($sql);
    // Find recipes.
    $i_recp=0;
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $resArrVals[$i_recp]['id']=$row['recipe_id'];
        $resArrVals[$i_recp]['name']=$row['name'];
        $resArrVals[$i_recp]['duration']=$row['duration'];
        $resArrVals[$i_recp]['difficulty']=$row['difficulty'];
        $resArrVals[$i_recp]['servings']=$row['n_served'];
        $resArrVals[$i_recp]['description']=$row['description'];
        $resArrVals[$i_recp]['ingredients']= array();
        $resArrVals[$i_recp]['preparation']= array();
        $resArrVals[$i_recp]['tags']= array();
        $resArrVals[$i_recp]['photos']= array();
        // Keep this reference recipe id -> its place in the array.
        $mapIdToData[$resArrVals[$i_recp]['id']] = $i_recp;

        $i_recp++;
    }
    
    // Fill values.
    fill_steps_for_recipes($mapIdToData, $resArrVals);
    fill_ingredients_for_recipes($mapIdToData, $resArrVals);
    fill_tags_for_recipes($mapIdToData, $resArrVals);
    fill_photos_for_recipes($mapIdToData, $resArrVals);

    echo json_encode($resArrVals);
}

list_all_recipes();

?>