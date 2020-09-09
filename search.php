<?php

require("inc/config.php");
require("inc/functions.php");

$search = "";
if(isset($_POST["submit"])) {
    $search = trimmer(escape(htmlspecialchars($_POST["query"])));
}

?>

<style>
input[type="text"], input[type="submit"]
{
    font-size:40px;
    width: 100%;
}

label
{
    word-wrap:break-word;
}

[type="checkbox"]
{
    vertical-align:middle;
}
</style>
<div align="center">
<form action="" method="post" dir="rtl" autocomplete="off">
<input type="text" name="query" value="<?=$search?>">
<label for="searchAnswers">
    <input type="checkbox" name="searchAnswers" id="searchAnswers" checked>כולל תשובות
</label>
<input type="submit" value="חפש!" name="submit">
</div>

<?php

$threshold = 5;

if(isset($_POST["submit"])) {

    $addition = "";
    if(!isset($_POST["searchAnswers"])) {
        $addition = " WHERE isQuestion = 1";
    }
    
    $perc = 0;
    $results = array();
    
    $query = query("SELECT * FROM phrases".$addition);
    while($row = mysqli_fetch_array($query)){
        $sim = similar_text($search, $row["phraseName"], $perc);

        if ($perc > $threshold) {
            array_push($results, (object)["percent" => $perc, "phrase" => $row["phraseName"], "id" => $row["pID"]]);
        }
        //echo $perc."\n";
    }
    
    usort($results, "compare");
    var_dump($results);
}
?>