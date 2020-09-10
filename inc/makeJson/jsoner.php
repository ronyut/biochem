<?php

require("../config.php");
require("../functions.php");

$json = file_get_contents("synonyms.json");
$json = json_decode($json);

$i = 0;
$allVars = array();
foreach($json as $group) {
    $variants = (array) $group->variants;
    array_push($variants, $group->term);
    $allVars[$i] = $variants;
    $i++;
}

$results = array();

$query = query("SELECT * FROM phrases LIMIT 50");  // <<<---------------@@@@@@@@@@@
while($row = mysqli_fetch_array($query)) {
    $text = trimmer($row["phraseName"]);
    $replace = [",", "-", "?", ".", "(", ")", "+", ":", "="];
    $clean = str_replace($replace, " ", $text);
    $clean = trimmer($clean);
    $words = explode(" ", $clean);
    
    $allWordsWithSyns = $words;
    foreach($words as $word) {
        $syns = (array) getSynonyms($word, $allVars);
        $allWordsWithSyns = array_merge($allWordsWithSyns, $syns);
    }
    $allWordsWithSyns = array_unique($allWordsWithSyns);
    
    // explode each word
    $lvl2 = array();
    foreach($allWordsWithSyns as $word) {
        if(mb_strlen($word) == 1 && !is_numeric($word) && !ctype_alpha($word)) {
            continue;
        }
        $boom = explode(" ", $word);
        $lvl2 = array_merge($lvl2, $boom);
    }
    $lvl2 = array_unique($lvl2);
    
    $id = $row["answerOf"];
    if($id == null) {
        $id = $row["pID"];
    }
    
    if(!array_key_exists($id, $results)){
        $results[$id] = array();
    }
    
    $results[$id] = array_merge($results[$id], $lvl2);
}

$final = array();
foreach($results as $id => $res) {
    $final[$id] = array_unique($res);
}

function getSynonyms($word, $array) {
    foreach ($array as $group) {
        foreach ($group as $val) {
            if ($val === $word) {
                return $group;
            } else {
                if(mb_strlen($word) >= 2 && mb_strlen($val) >= 2) {
                    $sim = similar_text($val, $word, $perc);
                    if($perc > 85) {
                        return $group;
                    }
                }
            }
        }
    }
    return null;
}

header('Content-Type: application/json');
echo json_encode($final, JSON_UNESCAPED_UNICODE);
?>