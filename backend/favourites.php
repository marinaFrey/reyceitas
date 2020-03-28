<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");

    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    // header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require 'connect.php';

function list_all_user_favourites() 
{
    $db = connect();
    $sql = "SELECT * FROM user_favourites uf INNER JOIN users u ON uf.user_id = u.user_id INNER JOIN recipes r ON uf.recipe_id = r.recipe_id;";
    $ret = $db->query($sql);

    $i = 0;
    foreach($ret as $row)
    {
        $resArrVals[$i]['user_favourites_id']=$row['user_favourites_id'];
        $resArrVals[$i]['user_id']=$row['user_id'];
        $resArrVals[$i]['user_name']=$row['username'];
        $resArrVals[$i]['recipe_id']=$row['recipe_id'];
        $resArrVals[$i]['recipe_name']=$row['name'];

        $i += 1;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function get_favourites_per_user($username) 
{
    $db = connect();
    $sql = "SELECT * FROM user_favourites uf INNER JOIN users u ON uf.user_id = u.user_id INNER JOIN recipes r ON uf.recipe_id = r.recipe_id WHERE u.username = :username;";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    $i = 0;
    foreach($ret as $row)
    {
        //$resArrVals[$i]['user_favourites_id']=$row['user_favourites_id'];
        //$resArrVals[$i]['user_id']=$row['user_id'];
        //$resArrVals[$i]['user_name']=$row['username'];
        //$resArrVals[$i]['recipe_id']=$row['recipe_id'];
        //$resArrVals[$i]['recipe_name']=$row['name'];
        $resArrVals[$i]=$row['recipe_id'];

        $i += 1;
    }
    if($i>0)
    {
        echo json_encode($resArrVals);
    }
}
function save_user_favourites($user_id, $favourites)
{
    //favourites.php?user_id=1&favourites=[101]
    $db = connect();
    $sql = "DELETE FROM user_favourites WHERE user_id = :usr_id";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':usr_id', $user_id, PDO::PARAM_INT);
    $ret= $stmt->execute();
    $ret = $stmt->fetchAll();

    $sql = "INSERT INTO user_favourites (user_id, recipe_id) VALUES (:usr_id, :recp_id);";
    $stmt2= $db->prepare($sql);
    $stmt2->bindValue(':usr_id', $user_id, PDO::PARAM_INT);
    $favs = json_decode($favourites);
    foreach($favs as $fav)
    {
        $stmt2->bindValue(':recp_id', $fav, PDO::PARAM_INT);
        $ret2 = $stmt2->execute();
        if(!$ret2) 
        {
            echo json_encode($stmt2->errorInfo());
        }
    }

}
function rm_user_favourite($user_id, $favourite)
{
    //favourites.php?user_id=1&favourite=101
    $db = connect();

    $sql = "DELETE FROM user_favourites WHERE user_id = :usr_id AND recipe_id = :recp_id;";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':usr_id', $user_id, PDO::PARAM_INT);
    $fav = json_decode($favourite);
    $stmt->bindValue(':recp_id', $fav, PDO::PARAM_INT);
    $ret = $stmt->execute();
    if(!$ret) 
    {
        echo json_encode($stmt->errorInfo());
    }

}
function add_user_favourite($user_id, $favourite)
{
    //favourites.php?user_id=1&favourite=101
    $db = connect();

    $sql = "INSERT INTO user_favourites (user_id, recipe_id) VALUES (:usr_id, :recp_id);";
    $stmt= $db->prepare($sql);
    $stmt->bindValue(':usr_id', $user_id, PDO::PARAM_INT);
    $fav = json_decode($favourite);
    $stmt->bindValue(':recp_id', $fav, PDO::PARAM_INT);
    $ret = $stmt->execute();
    if(!$ret) 
    {
        echo json_encode($stmt->errorInfo());
    } else {
        $last_id = $db->lastInsertId();
        echo $last_id;
        return $last_id; 
    }

}


if (isset($_GET['user_id']) && isset($_GET['favourites'])) {
    save_user_favourites($_GET['user_id'],$_GET['favourites']);
} elseif (isset($_GET['user_id']) && isset($_GET['add_favourite'])) {
    add_user_favourite($_GET['user_id'],$_GET['add_favourite']);
} elseif (isset($_GET['user_id']) && isset($_GET['rm_favourite'])) {
    rm_user_favourite($_GET['user_id'],$_GET['rm_favourite']);
} elseif (isset($_GET['username'])) {
    get_favourites_per_user($_GET['username']);
} else {
    list_all_user_favourites();
}
?>
