<?php
header("Access-Control-Allow-Origin: *");
require 'get_recipes.php';

function list_all_recipe_permissions() 
{
    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name, rp.authentication_level AS group_authentication_level FROM recipe_permissions AS rp";
    $sql.= " INNER JOIN user_groups AS ug ON rp.group_id = ug.group_id";
    $sql.= " INNER JOIN users AS u ON ug.user_id = u.user_id";
    $sql.= " INNER JOIN groups AS g ON ug.group_id = g.group_id";
    $sql.= " INNER JOIN recipes AS r ON rp.recipe_id = r.recipe_id";
    $ret = $db->query($sql);

    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC); $i++)
    {
        $resArrVals[$i]['recipe_permissions_id']=$row['recipe_permissions_id'];
        $resArrVals[$i]['recipe_id']=$row['recipe_id'];
        $resArrVals[$i]['recipe_name']=$row['recipe_name'];
        $resArrVals[$i]['authentication_level']=$row['group_authentication_level'];
        $resArrVals[$i]['global_authentication_level']=$row['global_authentication_level'];
        $resArrVals[$i]['user_group_id']=$row['user_group_id'];
        $resArrVals[$i]['user_id']=$row['user_id'];
        $resArrVals[$i]['user_name']=$row['username'];
        $resArrVals[$i]['group_id']=$row['group_id'];
        $resArrVals[$i]['group_name']=$row['name'];
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function get_public_recipes()
{
    $db = connect();
    $sql = "SELECT * FROM recipes";
    $sql.= " WHERE global_authentication_level != 0";
    $stmt= $db->prepare($sql);
    $ret= $stmt->execute();

    $mapIdToData [] = array();
    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC);)
    {
        $resArrVals[$i]['id']=$row['recipe_id'];
        $resArrVals[$i]['name']=$row['name'];
        $resArrVals[$i]['duration']=$row['duration'];
        $resArrVals[$i]['difficulty']=$row['difficulty'];
        $resArrVals[$i]['servings']=$row['n_served'];
        $resArrVals[$i]['description']=$row['description'];
        $resArrVals[$i]['ingredients']= array();
        $resArrVals[$i]['preparation']= array();
        $resArrVals[$i]['tags']= array();
        $resArrVals[$i]['photos']= array();
        $resArrVals[$i]['global_authentication_level']=$row['global_authentication_level'];
        $mapIdToData[$resArrVals[$i]['id']] = $i;
        $i++;
    }
    if($i>0)
    {
        // Fill values.
        fill_steps_for_recipes($mapIdToData, $resArrVals);
        fill_ingredients_for_recipes($mapIdToData, $resArrVals);
        fill_tags_for_recipes($mapIdToData, $resArrVals);
        fill_photos_for_recipes($mapIdToData, $resArrVals);

        echo json_encode($resArrVals);
    }
}
function get_recipes_per_user($username) 
{
    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name, rp.authentication_level AS group_authentication_level FROM recipe_permissions AS rp";
    $sql.= " INNER JOIN user_groups AS ug ON rp.group_id = ug.group_id";
    $sql.= " INNER JOIN users AS u ON ug.user_id = u.user_id";
    $sql.= " INNER JOIN groups AS g ON ug.group_id = g.group_id";
    $sql.= " INNER JOIN recipes AS r ON rp.recipe_id = r.recipe_id";
    $sql.= " WHERE u.username = :username";
    $sql.= " ORDER BY group_authentication_level desc";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $ret= $stmt->execute();

    $mapIdToData [] = array();
    for($i = 0; $row = $ret->fetchArray(SQLITE3_ASSOC);)
    {
        //$resArrVals[$i]['recipe_permissions_id']=$row['recipe_permissions_id'];
        //$resArrVals[$i]['recipe_id']=$row['recipe_id'];
        //$resArrVals[$i]['recipe_name']=$row['recipe_name'];
        //$resArrVals[$i]['user_group_id']=$row['user_group_id'];
        //$resArrVals[$i]['user_id']=$row['user_id'];
        //$resArrVals[$i]['user_name']=$row['username'];
        //$resArrVals[$i]['group_id']=$row['group_id'];
        //$resArrVals[$i]['group_name']=$row['name'];
        for($repeated=0, $j = 0; $j< $i;$j++) //SQLITE has no DISTINCT ON...
        {
            if($resArrVals[$j]['id']==$row['recipe_id'])
            {
                $repeated = 1;
            }
        }
        if($repeated == 0)
        {
            $resArrVals[$i]['id']=$row['recipe_id'];
            $resArrVals[$i]['name']=$row['recipe_name'];
            $resArrVals[$i]['duration']=$row['duration'];
            $resArrVals[$i]['difficulty']=$row['difficulty'];
            $resArrVals[$i]['servings']=$row['n_served'];
            $resArrVals[$i]['description']=$row['description'];
            $resArrVals[$i]['ingredients']= array();
            $resArrVals[$i]['preparation']= array();
            $resArrVals[$i]['tags']= array();
            $resArrVals[$i]['photos']= array();
            $resArrVals[$i]['authentication_level']=$row['group_authentication_level'];
            $resArrVals[$i]['global_authentication_level']=$row['global_authentication_level'];
            $mapIdToData[$resArrVals[$i]['id']] = $i;
            $i++;
        }

    }
    if($i>0)
    {
        // Fill values.
        fill_steps_for_recipes($mapIdToData, $resArrVals);
        fill_ingredients_for_recipes($mapIdToData, $resArrVals);
        fill_tags_for_recipes($mapIdToData, $resArrVals);
        fill_photos_for_recipes($mapIdToData, $resArrVals);

        echo json_encode($resArrVals);
    }
}
if (isset($_GET['username'])) {
    get_recipes_per_user($_GET['username']);
} elseif (isset($_GET['group_name'])) {
    get_recipes_per_group($_GET['group_name']);
} else {
    get_public_recipes();
    //list_all_recipe_permissions();
}
?>
