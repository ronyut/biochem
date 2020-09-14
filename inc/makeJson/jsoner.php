<?php

require("../config.php");
require("../functions.php");

$json = file_get_contents("synonyms.json");
$json = json_decode($json);

$stopwords = file_get_contents("stopwords.json");
$stopwords = json_decode($stopwords);

$i = 0;
$allVars = array();
foreach($json as $group) {
    $variants = (array) $group->variants;
    array_push($variants, $group->term);
    $allVars[$i] = $variants;
    $i++;
}

$results = array();

$query = query("SELECT * FROM phrases");  // <<<---------------@@@@@@@@@@@
while($row = mysqli_fetch_array($query)) {
    $text = trimmer($row["phraseName"]);
    $replace = [",", "-", "?", ".", "(", ")", "+", ":", "=", "/", "\\"];
    $clean = str_replace($replace, " ", $text);
    $clean = trimmer($clean);
    $words = explode(" ", $clean);
    $words = array_unique($words);
        
    $lvl1 = array();
    foreach($words as $word) {
        // remove single hebrew letters
        if(mb_strlen($word) == 1 && !is_numeric($word) && !ctype_alpha($word)) {
            continue;
        }
        
        if (!in_array($word, $stopwords)) {
            array_push($lvl1, $word);
        }
    }
        
    $allWordsWithSyns = $lvl1;
    foreach($lvl1 as $word) {
        $syns = (array) getSynonyms($word, $allVars);
        
        // explode each word
        $lvl2 = array();
        foreach($syns as $syn) {
            $boom = explode(" ", $syn);
            $lvl2 = array_merge($lvl2, $boom);
        }
        
        // skip empty arrays
        if(sizeof($lvl2) > 0){
            array_push($allWordsWithSyns, (array) $syns);
        }
    }
        
    $id = $row["answerOf"];
    if($id == null) {
        $id = $row["pID"];
    }
    
    if(!array_key_exists($id, $results)){
        $results[$id] = array();
    }
    
    $results[$id] = array_merge($results[$id], $allWordsWithSyns);
}

/*
$final = array();
foreach($results as $id => $res) {
    $final[$id] = array_unique($res);
}*/

function getSynonyms($word, $array) {
    $max = 0;
    $i = 0;
    foreach ($array as $group) {
        foreach ($group as $val) {
            if ($val === $word) {
                return $group;
            } else {
                if(mb_strlen($word) >= 2 && mb_strlen($val) >= 2) {
                    $sim = similar_text($val, $word, $perc);
                    if($perc > 85) {
                        $max = $i;
                    }
                }
            }
        }
        $i += 1;
    }
    
    if ($max == 0) {
        return null;
    } else {
        return $array[$max];
    }
}

header('Content-Type: application/json');
echo $outputJson = json_encode($results, JSON_UNESCAPED_UNICODE);

file_put_contents("analyzed.json", $outputJson);
?>