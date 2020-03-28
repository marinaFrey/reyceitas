<?php
    // header("Access-Control-Allow-Origin: *");
    // header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");

    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    // header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    require 'recipes.php';

    if (isset($_GET['recipe']))
    {
        $recipe = json_decode($_GET['recipe']);
        save_recipe($recipe);
    }

?>
