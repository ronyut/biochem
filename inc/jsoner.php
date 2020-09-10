<?php

require("config.php");
require("functions.php");

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

$query = query("SELECT * FROM phrases");
while($row = mysqli_fetch_array($query)) {
    $text = trimmer($row["phraseName"]);
    $replace = [",", "-", "?", ".", "(", ")", "+"];
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
        $boom = explode(" ", $word);
        $lvl2 = array_merge($lvl2, $boom);
    }
    $lvl2 = array_unique($lvl2);
    
    var_dump($lvl2);
}

function getSynonyms($word, $array) {
    foreach ($array as $group) {
        foreach ($group as $val) {
            if ($val === $word) {
                return $group;
            } else {
                $sim = similar_text($val, $word, $perc);
                if($perc > 85) {
                    return $group;
                }
            }
        }
    }
    return null;
}

//header('Content-Type: application/json');
//echo json_encode($json);
?>