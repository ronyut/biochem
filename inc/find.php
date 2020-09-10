<?php

require("config.php");
require("functions.php");

$json = file_get_contents("analyzed.json");
$json = json_decode($json);

$text = trimmer("ATP אוקסלו אצטט מעגל קרבס נחמד מאוד מאלאט");
$replace = [",", "-", "?", ".", "(", ")", "+", ":", "="];
$clean = str_replace($replace, " ", $text);
$clean = trimmer($clean);
$words = explode(" ", $clean);

$results = array();

foreach($json as $id => $values) {
    $count = 0;
    $log = "";
    foreach($words as $word) {
        foreach($values as $value) {
            if ($value === $word) {
                $count += 1;
                $log .= $word.", ";
            } else {
                if(mb_strlen($word) >= 2 && mb_strlen($value) >= 2
                   && !is_numeric($word) && !ctype_alpha($word) && !is_numeric($value) && !ctype_alpha($value)) {
                    $sim = similar_text($value, $word, $perc);
                    if($perc > 85) {
                        $count += 1;
                        $log .= $value.", ";
                    }
                }
            }
        }
    }
    
    $results[$id] = $count;
    
    if($count > 5) {
        echo $id. " -> ";
        echo $log."\n";
    }
}

var_dump($results);

//header('Content-Type: application/json');
//echo json_encode($final);
?>