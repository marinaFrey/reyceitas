<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    require 'connect.php';

    if (isset($_GET['id']))
    {
        $db = connect();
        $sql = "DELETE FROM recipes WHERE recipe_id = :r_id ;";
        $stmt = $db->prepare($sql);
        $r_id = intval($_GET['id']);
        $stmt->bindValue(':r_id', $r_id, SQLITE3_INTEGER);
        $ret = $stmt->execute();

        if(!$ret) {
            echo $db->lastErrorMsg();
        }

    }

?>