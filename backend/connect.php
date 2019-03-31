<?php
header("Access-Control-Allow-Origin: *");

class MyDB extends \PDO {
    function __construct() {
        parent::__construct("sqlite:" . "recipes.db");
        $this->exec('PRAGMA foreign_keys = ON;');
        if(!$this) {
            echo $this->errorInfo();
         } else {
             //echo "Opened database successfully\n";
             //Let's not drop recursive bombs here
         }
    }
}


function connect() {
    $db = new MyDB();
    return $db;
}


function create_tables() {
    $sql =<<<EOF
    CREATE TABLE IF NOT EXISTS groups(
        group_id INTEGER PRIMARY KEY,
        name TEXT UNIQUE
    );
    CREATE TABLE IF NOT EXISTS user_groups (
        user_group_id INTEGER PRIMARY KEY,
        user_id INTEGER NOT NULL,
        group_id INTEGER NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (group_id) REFERENCES groups(group_id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS recipe_permissions(
        recipe_permissions_id INTEGER PRIMARY KEY,
        recipe_id INTEGER NOT NULL,
        group_id INTEGER NOT NULL,
        authentication_level INTEGER,
        FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE,
        FOREIGN KEY (group_id) REFERENCES groups(group_id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY,
        username TEXT UNIQUE,
        password TEXT,
        email TEXT,
        full_name TEXT,
        authentication_level INTEGER
    );

    CREATE TABLE IF NOT EXISTS recipes (
        recipe_id INTEGER PRIMARY KEY,
        owner INTEGER NOT NULL,

        name TEXT NOT NULL,
        difficulty INTEGER DEFAULT 0,
        n_served INTEGER DEFAULT 0,
        duration TEXT,
        description TEXT,
        global_authentication_level INTEGER DEFAULT 1,
        
        FOREIGN KEY (owner) REFERENCES users(user_id)
    );


    CREATE TABLE IF NOT EXISTS recipe_ingredients (
        ingr_id INTEGER PRIMARY KEY,
        src_recipe INTEGER,
        quantity REAL NOT NULL,
        unit_name TEXT,
        description TEXT,
        
        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id) ON DELETE CASCADE
    );


    CREATE TABLE IF NOT EXISTS recipe_steps (
        step_id INTEGER PRIMARY KEY,
        src_recipe INTEGER,
        step_order INTEGER NOT NULL,
        description TEXT,
        
        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS recipe_contributors (
        src_recipe INTEGER,
        contributor_id INTEGER NOT NULL,
        permission_level INTEGER NOT NULL,
        
        FOREIGN KEY (contributor_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS recipe_pictures (
        picture_id INTEGER PRIMARY KEY,
        src_recipe INTEGER,
        file_name TEXT NOT NULL,
        is_of_instructions INTEGER DEFAULT 0,

        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS tags (
        tag_id INTEGER PRIMARY KEY,
        name TEXT,
        icon TEXT,
        color TEXT
    );
    
    CREATE TABLE IF NOT EXISTS recipe_tags (
        src_recipe INTEGER,
        src_tag INTEGER,
        
        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id) ON DELETE CASCADE,
        FOREIGN KEY (src_tag) REFERENCES tags(tag_id) ON DELETE CASCADE
    );

    PRAGMA foreign_keys = ON;

EOF;
    $db = connect();
    $ret = $db->exec($sql);
    if(!$ret){
        echo $db->errorInfo();
    } else {
        // echo "Table created successfully\n";
    }
}

function populate_with_dummy_info() {
    $sql =<<<EOF
    INSERT INTO users (username, full_name, password) VALUES ("testinho", "testinho testado", "1234");
    INSERT INTO users (username, full_name, password) VALUES ("testículo", "testículo testação", "12345");
    INSERT INTO users (username, full_name, password) VALUES ("testando", "testículo testação", "12345");


    INSERT INTO recipes
        (recipe_id, owner, name, difficulty, n_served, duration, description)
    VALUES
        (101, 2, "test recipe", 1, 2, '1:00H', 'test'),
        (102, 2, "test recipe2", 3, 4, '2:00H', 'teste2');
        

    INSERT INTO recipe_ingredients
        (src_recipe, quantity, unit_name, description)
    VALUES
        (101, 1, "cup", "rice"),
        (101, 2, "cup", "water"),
        (102, 1, "cup", "rice"),
        (102, 2, "cup", "water");;
        

    INSERT INTO recipe_steps
        (src_recipe, step_order, description)
    VALUES
        (101, 0, "put rice in bowl"),
        (101, 1, "put water in rice"),
        (101, 2, "cook for 20 minutes"),
        (102, 0, "put rice in bowl"),
        (102, 1, "put water in rice"),
        (102, 2, "cook for 20 minutes");;
        
    
    INSERT INTO recipe_tags VALUES (101, 42);
EOF;
    $db = connect();
    $ret = $db->exec($sql);
    if(!$ret) {
        echo $db->errorInfo();
    } else {
        // echo "Records created successfully\n";
    }
}

function populateTags()
{

  $sql =<<<EOF
    INSERT INTO tags 
    VALUES 
    (42, "Favoritos", "fa-star", "#dfc013"),
    (43, "Bebidas", "fa-coffee", "#915721"),
    (44, "Sobremesas", "fa-birthday-cake", "#dd73d8"),
    (45, "Vegetariano", "fa-feather-alt", "#72ce6f"),
    (46, "Refeições", "fa-utensils", "#777777"),
    (47, "Sopas", "fa-utensil-spoon", "#6fcebe"),
    (48, "Lanches", "fa-cookie-bite", "#926d4b"),
    (49, "Peixes", "fa-fish", "#6f98ce"),
    (50, "Aves", "fa-crow", "#ce926f"),
    (51, "Porco", "fa-piggy-bank", "#ce926f"),
    (52, "Carne vermelha", "fa-chess-knight", "#ce926f"),
    (53, "Saudável", "fa-apple-alt", "#9dce6f");

EOF;
    $db = connect();
    $ret = $db->exec($sql);
    if(!$ret) {
        echo $db->errorInfo();
    } else {
        // echo "Records created successfully\n";
    }
}
function populateUserGroups()
{

  $sql =<<<EOF
INSERT INTO groups (group_id, name) VALUES (1,"grupinho");
INSERT INTO groups (group_id, name) VALUES (2,"grupa");
INSERT INTO user_groups (user_id, group_id) VALUES (2, 2);
INSERT INTO user_groups (user_id, group_id) VALUES (1, 1);
INSERT INTO recipe_permissions (recipe_id, group_id, authentication_level) VALUES (101, 1, 1);
INSERT INTO recipe_permissions (recipe_id, group_id, authentication_level) VALUES (101, 2, 1);
INSERT INTO recipe_permissions (recipe_id, group_id, authentication_level) VALUES (102, 2, 1);
EOF;
    $db = connect();
    $ret = $db->exec($sql);
    if(!$ret) {
        echo $db->errorInfo();
    } else {
        // echo "Records created successfully\n";
    }
}

// connect();
// create_tables();
// populateTags();
// populate_with_dummy_info();
 //populateUserGroups();


?>
