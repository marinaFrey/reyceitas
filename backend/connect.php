<?php
header("Access-Control-Allow-Origin: *");
class MyDB extends SQLite3 {
    function __construct() {
        $this->open('recipes.db');
        if(!$this) {
            echo $this->lastErrorMsg();
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
    CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY,
        username TEXT,
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
        
        FOREIGN KEY (owner) REFERENCES users(user_id)
    );


    CREATE TABLE IF NOT EXISTS recipe_ingredients (
        ingr_id INTEGER PRIMARY KEY,
        src_recipe INTEGER,
        quantity REAL NOT NULL,
        unit_name TEXT,
        description TEXT,
        
        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id)
    );


    CREATE TABLE IF NOT EXISTS recipe_steps (
        step_id INTEGER PRIMARY KEY,
        src_recipe INTEGER,
        step_order INTEGER NOT NULL,
        description TEXT,
        
        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id)
    );

    CREATE TABLE IF NOT EXISTS recipe_contributors (
        src_recipe INTEGER,
        contributor_id INTEGER NOT NULL,
        permission_level INTEGER NOT NULL,
        
        FOREIGN KEY (contributor_id) REFERENCES users(user_id),
        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id)
    );

    CREATE TABLE IF NOT EXISTS recipe_pictures (
        picture_id INTEGER PRIMARY KEY,
        src_recipe INTEGER,
        file_name TEXT NOT NULL,
        is_of_instructions INTEGER DEFAULT 0,

        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id)
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
        
        FOREIGN KEY (src_recipe) REFERENCES recipes(recipe_id),
        FOREIGN KEY (src_tag) REFERENCES tags(tag_id)
    );
EOF;
    $db = connect();
    $ret = $db->exec($sql);
    if(!$ret){
        echo $db->lastErrorMsg();
    } else {
        // echo "Table created successfully\n";
    }
}

function populate_with_dummy_info() {
    $sql =<<<EOF
    INSERT INTO users (user_id, username, full_name, password) VALUES (42, "testudo", "testy mactesterson", "123");
    INSERT INTO users (user_id, username, full_name, password) VALUES (1, "testinho", "testinho testado", "1234");
    INSERT INTO users (user_id, username, full_name, password) VALUES (2, "testículo", "testículo testação", "12345");

    INSERT INTO recipes
        (recipe_id, owner, name, difficulty, n_served, duration, description)
    VALUES
        (101, 42, "test recipe", 1, 2, '1:00H', 'test'),
        (102, 42, "test recipe2", 3, 4, '2:00H', 'teste2');
        

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
        echo $db->lastErrorMsg();
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
        echo $db->lastErrorMsg();
    } else {
        // echo "Records created successfully\n";
    }
}

//connect();
//create_tables();
//populate_with_dummy_info();
//populateTags();

?>
