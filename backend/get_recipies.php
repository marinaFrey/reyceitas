<?php
header("Access-Control-Allow-Origin: *");
    class MyDB extends SQLite3 {
        function __construct() {
            $this->open('recipies.db');
            if(!$this) {
                echo $this->lastErrorMsg();
             } else {
                // echo "Opened database successfully\n";
                // $this->create_tables();
                // $this->populate_with_dummy_info();
                $this->list_all_recipies();
                // $this->list_tags();
             }
        }


        function fill_tags_for_recipies(&$mapIdToRecip, &$resArrVals) {
            $sql =<<<EOF
            SELECT id_recip, id_tag, name, icon, color FROM recipy_tags, tags
                WHERE tags.tag_id == recipy_tags.id_tag
                ORDER BY recipy_tags.id_recip ASC;
EOF;
            $ret = $this->query($sql);
            while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
                // $tag = array( "id" => $row["id_tag"], "name" => $row["name"],
                    // "icon" => $row["icon"],"color" => $row["color"]);
                $tag_id = $row["id_tag"];
                $i_recip = $mapIdToRecip[$row['id_recip']];
                $v = array_push($resArrVals[$i_recip]['tags'], $tag_id);
            }
        }

        function fill_ingredients_for_recipies(&$mapIdToRecip, &$resArrVals) {
            $sql =<<<EOF
            SELECT ingr_id, src_recip, quantity, unit_name, description
            FROM recipy_ingredients
                ORDER BY src_recip ASC, ingr_id ASC;
EOF;
            $ret = $this->query($sql);
            while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
                $ingred = array( "id" => $row["ingr_id"], "name" => $row["description"],
                    "amount" => $row["quantity"], "unit" => $row["unit_name"]);
                
                
                $i_recip = $mapIdToRecip[$row['src_recip']];
                $v = array_push($resArrVals[$i_recip]['ingredients'], $ingred);
            }
        }

        function fill_steps_for_recipies(&$mapIdToRecip, &$resArrVals) {
            $sql =<<<EOF
            SELECT src_recip, description FROM recipy_steps
                ORDER BY src_recip ASC, step_id ASC;
EOF;
            $ret = $this->query($sql);
            while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
                $i_recip = $mapIdToRecip[$row['src_recip']];
                $v = array_push($resArrVals[$i_recip]['preparation'],
                    $row['description']);
            }
        }
        
        function list_tags() {
            $sql =<<<EOF
            SELECT tag_id, name, icon, color FROM tags;
EOF;

            $resArrVals = array();
            $ret = $this->query($sql);
            while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
                $tag = array( "id" => $row["tag_id"], "name" => $row["name"],
                    "icon" => $row["icon"],"color" => $row["color"]);
                $v = array_push($resArrVals, $tag);
            }
            echo json_encode($resArrVals);
        }

        function list_all_recipies() {
            $sql =<<<EOF
            SELECT * FROM recipies;
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
            $ret = $this->query($sql);
            // Find recipies.
            $i_recp=0;
            while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
                $resArrVals[$i_recp]['id']=$row['recip_id'];
                $resArrVals[$i_recp]['name']=$row['name'];
                $resArrVals[$i_recp]['duration']=$row['duration'];
                $resArrVals[$i_recp]['difficulty']=$row['difficulty'];
                $resArrVals[$i_recp]['servings']=$row['n_served'];
                $resArrVals[$i_recp]['description']=$row['description'];
                
                // Keep this reference recipy id -> its place in the array.
                $mapIdToData[$resArrVals[$i_recp]['id']] = $i_recp;

                $i_recp++;
            }
            
            // Fill values.
            $this->fill_steps_for_recipies($mapIdToData, $resArrVals);
            $this->fill_ingredients_for_recipies($mapIdToData, $resArrVals);
            $this->fill_tags_for_recipies($mapIdToData, $resArrVals);

            echo json_encode($resArrVals);
        }
    }

   


   $db = new MyDB();
//    $db->close();

?>