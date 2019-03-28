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
        full_name TEXT
    );

    CREATE TABLE IF NOT EXISTS recipes (
        recip_id INTEGER PRIMARY KEY,
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
        src_recip INTEGER,
        quantity REAL NOT NULL,
        unit_name TEXT,
        description TEXT,
        
        FOREIGN KEY (src_recip) REFERENCES recipes(recip_id)
    );


    CREATE TABLE IF NOT EXISTS recipe_steps (
        step_id INTEGER PRIMARY KEY,
        src_recip INTEGER,
        step_order INTEGER NOT NULL,
        description TEXT,
        
        FOREIGN KEY (src_recip) REFERENCES recipes(recip_id)
    );

    CREATE TABLE IF NOT EXISTS recipe_contributiors (
        src_recip INTEGER,
        contributor_id INTEGER NOT NULL,
        permission_level INTEGER NOT NULL,
        
        FOREIGN KEY (contributor_id) REFERENCES users(user_id),
        FOREIGN KEY (src_recip) REFERENCES recipes(recip_id)
    );

    CREATE TABLE IF NOT EXISTS recipe_pictures (
        picture_id INTEGER PRIMARY KEY,
        file_name TEXT NOT NULL,
        img_data BLOB NOT NULL,
        is_of_instructions INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS tags (
        tag_id INTEGER PRIMARY KEY,
        name TEXT,
        icon TEXT,
        color TEXT
    );
    
    CREATE TABLE IF NOT EXISTS recipe_tags (
        id_recip INTEGER,
        id_tag INTEGER,
        
        FOREIGN KEY (id_recip) REFERENCES recipes(recip_id),
        FOREIGN KEY (id_tag) REFERENCES tags(tag_id)
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
        (recip_id, owner, name, difficulty, n_served, duration, description)
    VALUES
        (101, 42, "test recipe", 1, 2, '1:00H', 'test');
        

    INSERT INTO recipe_ingredients
        (src_recip, quantity, unit_name, description)
    VALUES
        (101, 1, "cup", "rice"),
        (101, 2, "cup", "water");
        

    INSERT INTO recipe_steps
        (src_recip, step_order, description)
    VALUES
        (101, 0, "put rice in bowl"),
        (101, 1, "put water in rice"),
        (101, 2, "cook for 20 minutes");
        
    
    INSERT INTO tags VALUES (42, "favoritos", "fa-star", "#dfc013");

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

create_tables();
//populate_with_dummy_info();
?>
