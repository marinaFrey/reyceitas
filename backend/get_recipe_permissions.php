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
        echo json_encode($resArrVals);
    }
}
function get_public_recipes()
{
    $db = connect();
    $sql = "SELECT * FROM recipes";
    $sql.= " WHERE global_authentication_level != 0";
    $stmt= $db->prepare($sql);
    $ret = $stmt->execute();
    $ret = $stmt->fetchAll();

    $mapIdToData [] = array();
    $i = 0;
    foreach($ret as $row)
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
        $resArrVals[$i]['userId']= $row['owner'];
        $resArrVals[$i]['groupsAuthenticationLevel']= array();
        $resArrVals[$i]['globalAuthenticationLevel']=$row['global_authentication_level'];
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
function get_owned_recipes_per_user($user_id) 
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
if (isset($_GET['usr_id']) && isset($_GET['recp_id'])) {
    get_permission_per_user_recipe($_GET['recp_id'],$_GET['usr_id']);
}elseif (isset($_GET['username'])) {
    get_recipes_per_user($_GET['username']);
}elseif (isset($_GET['owned_recipes_per_user_id'])) {
    get_owned_recipes_per_user($_GET['owned_recipes_per_user_id']);
} elseif (isset($_GET['recipe_name'])) {
    get_group_per_recipes($_GET['recipe_name']);
} else {
    get_public_recipes();
    //list_all_recipe_permissions();
}
?>
