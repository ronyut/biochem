<?php

require("config.php");
require("functions.php");

$json = file_get_contents("makeJson/analyzed.json");
$json = json_decode($json);

$text = trimmer(escape($_POST["query"]));
$replace = [",", "-", "?", ".", "(", ")", "+", ":", "="];
$clean = str_replace($replace, " ", $text);
$clean = str_replace("'", "", $clean);
$clean = trimmer($clean);
$words = explode(" ", $clean);

$results = array();

foreach($json as $id => $values) {
    $score = 0;
    $log = "";
    $highlight = array();
    foreach($words as $word) {
        $word = mb_strtolower($word);
        
        foreach($values as $value) {
            if(!is_array($value)) {
                $orig = $value;
                $value = mb_strtolower($value);
                if ($value === $word) {
                    $score += 100;
                    $log .= $value.", ";
                    array_push($highlight, $orig);
                } else {
                    if(mb_strlen($word) >= 2 && mb_strlen($value) >= 2
                       && !is_numeric($word) && !ctype_alpha($word) && !is_numeric($value) && !ctype_alpha($value)) {
                        $sim = similar_text($value, $word, $perc);
                        if($perc > 85) {
                            $score += $perc;
                            $log .= $value.", ";
                            array_push($highlight, $value);
                        }
                    }
                }
            } else {
                foreach($value as $val){
                    $val = mb_strtolower($val);
                    if ($val === $word) {
                        $score += 100;
                        $log .= $val.", ";
                        $highlight = array_merge($highlight, $value);
                        break;
                    } else {
                        if(mb_strlen($word) >= 2 && mb_strlen($val) >= 2
                           && !is_numeric($word) && !ctype_alpha($word) && !is_numeric($val) && !ctype_alpha($val)) {
                            $sim = similar_text($val, $word, $perc);
                            if($perc > 85) {
                                $score += $perc;
                                $log .= $val.", ";
                                $highlight = array_merge($highlight, $value);
                        break;
                            }
                        }
                    }
                }
            }
        }
    }
    
    $highlight = array_unique($highlight);
    array_push($results, array("id" => (int) $id, "score" => $score, "highlight" => $highlight));
}

usort($results, "compareScore");

/*
$lower_limit = 1;
$upper_limit = 1000;
$results = array_filter(
    $results,
    function ($value) use ($lower_limit, $upper_limit) {
        return ($value >= $lower_limit && $value <= $upper_limit);
    }
);*/

$results = array_slice($results, 0, 5, true);

header('Content-Type: application/json');
echo json_encode($results, JSON_UNESCAPED_UNICODE);
?>