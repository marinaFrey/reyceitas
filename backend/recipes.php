<?php

require 'connect.php';
require 'login.php';

function list_all_recipe_permissions($do_echo=TRUE) 
{
    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name, rp.authentication_level AS group_authentication_level FROM recipe_permissions AS rp";
    $sql.= " INNER JOIN user_groups AS ug ON rp.group_id = ug.group_id";
    $sql.= " INNER JOIN users AS u ON ug.user_id = u.user_id";
    $sql.= " INNER JOIN groups AS g ON ug.group_id = g.group_id";
    $sql.= " INNER JOIN recipes AS r ON rp.recipe_id = r.recipe_id";
    $ret = $db->query($sql);


    $i = 0;
    foreach($ret as $row)
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
        $i += 1;
    }
    if($i>0)
    {
        if($do_echo) {
            echo json_encode($resArrVals);
        } else {
            return $resArrVals;
        }
    }
}

function get_public_recipes()
{
    error_log("GET public");

    $db = connect();
    $sql = "SELECT * FROM recipes";
    $sql.= " WHERE global_authentication_level != 0";
    $stmt= $db->prepare($sql);
    $ret = $stmt->execute();
    $ret = $stmt->fetchAll();

    $mapIdToData [] = array();
    $resArrVals [] = array();

    $i = 0;
    foreach($ret as $row)
    {
        error_log($i);
        error_log($row['name']);

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
        $resArrVals[$i]['userId']= $row['owner'];
        $resArrVals[$i]['groupsAuthenticationLevel']= array();
        $resArrVals[$i]['globalAuthenticationLevel']=$row['global_authentication_level'];
        $mapIdToData[$resArrVals[$i]['id']] = $i;
        $i++;
    }

    if($i>0)
    {

        error_log("some");


        // Fill values.
        fill_steps_for_recipes($mapIdToData, $resArrVals);
        fill_ingredients_for_recipes($mapIdToData, $resArrVals);
        fill_tags_for_recipes($mapIdToData, $resArrVals);
        fill_photos_for_recipes($mapIdToData, $resArrVals);

        echo json_encode($resArrVals);
        
    } else 
    {
        error_log("no prublic");
        echo "[]";
    }
}

function get_owned_recipes_per_user($user_id, $do_echo=TRUE) 
{
    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name FROM recipes AS r" ;
    $sql.= " INNER JOIN users AS u ON u.user_id = r.owner";
    $sql.= " WHERE u.user_id = :user_id";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    $i=0;
    foreach($ret as $row)
    {
        $resArrVals[$i]=$row['recipe_id'];
        $i++;
    }
    if($i>0)
    {
        if($do_echo) {
            echo json_encode($resArrVals);
        } else {
            return $resArrVals;
        }
        
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
    //$sql.= " JOIN recipes AS r2 ";
    $sql.= " WHERE u.username = :username OR r.global_authentication_level > 0";
    $sql.= " ORDER BY group_authentication_level desc";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    $mapIdToData [] = array();
    $i = 0;
    foreach($ret as $row)
    {
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
            $resArrVals[$i]['userId']= $row['owner'];
            $resArrVals[$i]['groupsAuthenticationLevel']= array();
            //$resArrVals[$i]['authenticationLevel']=$row['group_authentication_level'];
            $resArrVals[$i]['globalAuthenticationLevel']=$row['global_authentication_level'];
            $mapIdToData[$resArrVals[$i]['id']] = $i;
            $i++;
        }
    }
    $sql = "SELECT *, r.name AS recipe_name FROM recipes AS r" ;
    $sql.= " INNER JOIN users AS u ON u.user_id = r.owner";
    $sql.= " WHERE u.username = :username";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    foreach($ret as $row)
    {
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
            $resArrVals[$i]['userId']= $row['owner'];
            $resArrVals[$i]['groupsAuthenticationLevel']= array();
            //$resArrVals[$i]['authenticationLevel']=$row['group_authentication_level'];
            $resArrVals[$i]['globalAuthenticationLevel']=$row['global_authentication_level'];
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
        fill_groups_for_recipes($mapIdToData, $resArrVals, $username);
        echo json_encode($resArrVals);
    }
}
function get_group_per_recipes($recipe_name)
{
    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name, g.name AS group_name, rp.authentication_level AS group_authentication_level FROM recipe_permissions AS rp";
    $sql.= " INNER JOIN groups AS g ON rp.group_id = g.group_id";
    $sql.= " INNER JOIN recipes AS r ON rp.recipe_id = r.recipe_id";
    $sql.= " WHERE recipe_name = :recipe_name";
    $sql.= " ORDER BY group_authentication_level desc";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':recipe_name', $recipe_name, PDO::PARAM_STR);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    $i = 0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['groupId']=$row['group_id'];
        $resArrVals[$i]['groupName']=$row['group_name'];
        $resArrVals[$i]['authenticationLevel']=$row['group_authentication_level'];
        $i += 1;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function get_permission_per_user_recipe($recp_id,$usr_id)
{

    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name, rp.authentication_level AS group_authentication_level FROM recipe_permissions AS rp";
    $sql.= " INNER JOIN user_groups AS ug ON rp.group_id = ug.group_id";
    $sql.= " INNER JOIN users AS u ON ug.user_id = u.user_id";
    $sql.= " INNER JOIN groups AS g ON ug.group_id = g.group_id";
    $sql.= " INNER JOIN recipes AS r ON rp.recipe_id = r.recipe_id";
    $sql.= " WHERE u.user_id = :usr_id AND r.recipe_id = :recp_id" ;
    $sql.= " ORDER BY group_authentication_level desc";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':usr_id', $usr_id, PDO::PARAM_INT);
    $stmt->bindValue(':recp_id', $recp_id, PDO::PARAM_INT);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();
    $permission = 0;
    foreach($ret as $row)
    {
        if($row['group_authentication_level'] > $permission)
        {
            $permission = $row['group_authentication_level'];
        }
    }
    $sql = "SELECT * FROM recipes";
    $sql.= " WHERE recipe_id = :recp_id" ;
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':recp_id', $recp_id, PDO::PARAM_INT);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();
    if($ret[0]['global_authentication_level'] > $permission)
    {
        $permission = $ret[0]['global_authentication_level'];
    }
    if($ret[0]['owner'] == $usr_id)
    {
        $permission = 2; 
    }
    //echo $permission;
    return $permission;

}
function fill_groups_for_recipes(&$mapIdToRecip, &$resArrVals, $username) 
{
    $db = connect();
    $sql = "SELECT *, r.name AS recipe_name, g.name AS group_name, rp.authentication_level AS group_authentication_level FROM recipe_permissions AS rp";
    $sql.= " INNER JOIN groups AS g ON rp.group_id = g.group_id";
    $sql.= " INNER JOIN recipes AS r ON rp.recipe_id = r.recipe_id";
    $sql.= " INNER JOIN user_groups AS ug ON rp.group_id = ug.group_id";
    $sql.= " INNER JOIN users AS u ON ug.user_id = u.user_id";
    $sql.= " ORDER BY group_authentication_level desc";
    $stmt= $db->prepare($sql);
    //$stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();
    $i = 0;
    foreach($ret as $row)
    {
        if(array_key_exists($row['recipe_id'],$mapIdToRecip))
        {
            $i_recip = $mapIdToRecip[$row['recipe_id']];
            $groups['groupId'] = $row['group_id'];
            $groups['groupName'] = $row['group_name'];
            $groups['authenticationLevel'] = $row['group_authentication_level'];
            $v = array_push($resArrVals[$i_recip]['groupsAuthenticationLevel'],$groups);
        }
        $i += 1;
    }
}


function delete_recipe($recip_id)
{
    // You have to be a valid user.
    $usr_info = get_current_user_info();
    if($usr_info == NULL) {
        error_log("Not a valid user");
        http_response_code(401);
        die();
    }
    // That has permissions.
    if(get_permission_per_user_recipe($usr_info->aud, $recip_id) <= 0) {
        error_log("No permission to delete.");
        http_response_code(403);
        die();
    }

    $db = connect();
    $sql = "DELETE FROM recipes WHERE recipe_id = :r_id ;";
    $stmt = $db->prepare($sql);
    $r_id = intval($recip_id);
    $stmt->bindValue(':r_id', $r_id, PDO::PARAM_INT);
    $ret = $stmt->execute();

    if(!$ret) {
        echo $stmt->errorInfo();
    }
}

function save_recipe($recipe)
{
    // Get who I am logged as and what recipe will be changed.
    $user = get_current_user_info();

    // Make sure the user 
    if ($user->authentication_level >= 1)
    {
        http_response_code(403);
        die();
    }

    //echo json_encode($recipe->name);
    
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

function edit_recipe($recipe)
{
    // Is the user logged in?
    $usr_info = get_current_user_info();
    if($usr_info == NULL) {
        http_response_code(401);
        die();
    }

    // Validate if the user can edit.

        // error_log("EDIT BEING ");
        // error_log( json_encode($usr_info));

        if($usr_info->aud != "root" && get_permission_per_user_recipe($usr_info->scope->id, $recipe->id) <= 0) {
            http_response_code(403);
            die();
        }

        // Do the update.
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


function fill_tags_for_recipes(&$mapIdToRecip, &$resArrVals) {
    $sql =<<<EOF
    SELECT src_recipe, src_tag, name, icon, color FROM recipe_tags, tags
        WHERE tags.tag_id == recipe_tags.src_tag
        ORDER BY recipe_tags.src_recipe ASC;
EOF;
    $db = connect();
    $ret = $db->query($sql);
    foreach($ret as $row) {
        if(array_key_exists($row['src_recipe'],$mapIdToRecip))
        {
            $tag_id = $row["src_tag"];
            $i_recip = $mapIdToRecip[$row['src_recipe']];
            $v = array_push($resArrVals[$i_recip]['tags'], $tag_id);
        }
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
    foreach($ret as $row) {
        if(array_key_exists($row['src_recipe'],$mapIdToRecip))
        {
            $ingred = array( "id" => $row["ingr_id"], "name" => $row["description"],
                "amount" => $row["quantity"], "unit" => $row["unit_name"]);
                   
            $i_recip = $mapIdToRecip[$row['src_recipe']];
            array_push($resArrVals[$i_recip]['ingredients'], $ingred);
        }
    }
}

function fill_steps_for_recipes(&$mapIdToRecip, &$resArrVals) {
    $sql =<<<EOF
    SELECT src_recipe, description FROM recipe_steps
        ORDER BY src_recipe ASC, step_id ASC;
EOF;
    $db = connect();
    $ret = $db->query($sql);
    foreach($ret as $row) {
        if(array_key_exists($row['src_recipe'],$mapIdToRecip))
        {
            $i_recip = $mapIdToRecip[$row['src_recipe']];
            $v = array_push($resArrVals[$i_recip]['preparation'],
                $row['description']);
        }
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
    foreach($ret as $row) {
        if(array_key_exists($row['src_recipe'],$mapIdToRecip))
        {
            $i_recip = $mapIdToRecip[$row['src_recipe']];
            $v = array_push($resArrVals[$i_recip]['photos'],
                $row['file_name']);
        }
    }
}

function list_tags() {
    $sql =<<<EOF
    SELECT tag_id, name, icon, color FROM tags;
EOF;

    $resArrVals = array();
    $db = connect();
    $ret = $db->query($sql);
    foreach($ret as $row) {
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
    foreach($ret as $row) {
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




?>