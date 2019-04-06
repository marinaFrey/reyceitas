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

function logDBErrors($db, $ret) {
    if($ret === false) {
        $err = $db->errorInfo();
        if($err[0] !== '00000') {
            echo "Query ran fine, but to no effect. \n";
        }else if($err[0] === '01000') {
            echo "Warning:\n";
        } else {
            echo "ERROR!\n";
        }
        print_r( $err );
    }
}


function createUserDBModel()
{

  $sql =<<<EOF
  PRAGMA foreign_keys = OFF;

  CREATE TABLE "users" (
      "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK ("id" >= 0),
      "email" VARCHAR(249) NOT NULL,
      "password" VARCHAR(255) NOT NULL,
      "username" VARCHAR(100) DEFAULT NULL,
      "status" INTEGER NOT NULL CHECK ("status" >= 0) DEFAULT "0",
      "verified" INTEGER NOT NULL CHECK ("verified" >= 0) DEFAULT "0",
      "resettable" INTEGER NOT NULL CHECK ("resettable" >= 0) DEFAULT "1",
      "roles_mask" INTEGER NOT NULL CHECK ("roles_mask" >= 0) DEFAULT "0",
      "registered" INTEGER NOT NULL CHECK ("registered" >= 0),
      "last_login" INTEGER CHECK ("last_login" >= 0) DEFAULT NULL,
      "force_logout" INTEGER NOT NULL CHECK ("force_logout" >= 0) DEFAULT "0",
      CONSTRAINT "email" UNIQUE ("email")
  );
  
  CREATE TABLE "users_confirmations" (
      "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK ("id" >= 0),
      "user_id" INTEGER NOT NULL CHECK ("user_id" >= 0),
      "email" VARCHAR(249) NOT NULL,
      "selector" VARCHAR(16) NOT NULL,
      "token" VARCHAR(255) NOT NULL,
      "expires" INTEGER NOT NULL CHECK ("expires" >= 0),
      CONSTRAINT "selector" UNIQUE ("selector")
  );
  CREATE INDEX "users_confirmations.email_expires" ON "users_confirmations" ("email", "expires");
  CREATE INDEX "users_confirmations.user_id" ON "users_confirmations" ("user_id");
  
  CREATE TABLE "users_remembered" (
      "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK ("id" >= 0),
      "user" INTEGER NOT NULL CHECK ("user" >= 0),
      "selector" VARCHAR(24) NOT NULL,
      "token" VARCHAR(255) NOT NULL,
      "expires" INTEGER NOT NULL CHECK ("expires" >= 0),
      CONSTRAINT "selector" UNIQUE ("selector")
  );
  CREATE INDEX "users_remembered.user" ON "users_remembered" ("user");
  
  CREATE TABLE "users_resets" (
      "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK ("id" >= 0),
      "user" INTEGER NOT NULL CHECK ("user" >= 0),
      "selector" VARCHAR(20) NOT NULL,
      "token" VARCHAR(255) NOT NULL,
      "expires" INTEGER NOT NULL CHECK ("expires" >= 0),
      CONSTRAINT "selector" UNIQUE ("selector")
  );
  CREATE INDEX "users_resets.user_expires" ON "users_resets" ("user", "expires");
  
  CREATE TABLE "users_throttling" (
      "bucket" VARCHAR(44) PRIMARY KEY NOT NULL,
      "tokens" REAL NOT NULL CHECK ("tokens" >= 0),
      "replenished_at" INTEGER NOT NULL CHECK ("replenished_at" >= 0),
      "expires_at" INTEGER NOT NULL CHECK ("expires_at" >= 0)
  );
  CREATE INDEX "users_throttling.expires_at" ON "users_throttling" ("expires_at");
EOF;
    $db = connect();
    $ret = $db->exec($sql);
    logDBErrors($db, $ret);
}


function create_tables() {

    createUserDBModel();

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

    CREATE TABLE IF NOT EXISTS recipes (
        recipe_id INTEGER PRIMARY KEY,
        owner INTEGER NOT NULL,

        name TEXT NOT NULL,
        difficulty INTEGER DEFAULT 0,
        n_served INTEGER DEFAULT 0,
        duration TEXT,
        description TEXT,
        global_authentication_level INTEGER DEFAULT 1,
        
        FOREIGN KEY (owner) REFERENCES users(id)
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
        
        FOREIGN KEY (contributor_id) REFERENCES users(id) ON DELETE CASCADE,
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
    logDBErrors($db, $ret);
}

require __DIR__ . '/vendor/autoload.php';

function register_user($auth, $mail, $pass, $usrname) 
{
    try {
        $userId = $auth->registerWithUniqueUsername($mail, $pass, $usrname);
        echo 'We have signed up a new user with the ID ' . $userId;
        return $userId;
    }
    catch (\Delight\Auth\InvalidEmailException $e) {
        die('Invalid email address');
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        die('Invalid password');
    }
    catch (\Delight\Auth\UserAlreadyExistsException $e) {
        die('User already exists');
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        die('Too many requests');
    }
    catch(\Delight\Auth\DuplicateUsernameException $e) {
        die('username already exists');
    }
}



function populate_with_dummy_info() {
    $db = connect();
    $auth = new \Delight\Auth\Auth($db);


    register_user($auth, "t@b1.com", "1234", "testinho");
    register_user($auth, "t@b2.com", "12345", "testículo");
    register_user($auth, "t@b3.com", "12345", "testando");

    $sql =<<<EOF
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
//  create_tables();
//  populateTags();
//  populate_with_dummy_info();
//  populateUserGroups();


?>
