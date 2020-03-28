<?php
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require 'recipes.php';


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
