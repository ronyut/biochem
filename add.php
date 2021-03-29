<?php

require("inc/config.php");
require("inc/functions.php");

$full = "";
if(isset($_POST["submit"])) {
    $full = $_POST["full"];
}

?>
<!doctype html>

<html lang="he">
<head>
    <meta charset="utf-8">

    <title>Biochemistry</title>

    <style>
    textarea, input[type="submit"]
    {
        font-size:40px;
        width: 100%;
    }

    span {
        direction: rtl;
        float: right;
        font-size:40px;
    }

    </style>

</head>

<body>
    <div align="center">
        <form action="" method="post" dir="rtl" autocomplete="off">
            <textarea name="full" rows="5"><?=$full?></textarea>
            <input type="submit" value="פרק" name="submit">
        </form>
    </div>
</body>
</html>
<?php

if(isset($_POST["submit"])) {
    $arr = explode("\r\n", $full);
    $startChar = '\t';
    $i = 1;
    foreach($arr as $phrase) {
        if ($phrase == "") {
            continue;
        }
        $new_phrase = substr($phrase, strpos($phrase, $startChar) + strlen($startChar) + 1);
        $new_phrase = trimmer($new_phrase);
        
        if($i == 1) {
            echo '<form action="" method="post" dir="rtl" autocomplete="off">';
        }
        
        echo "<div class='inline'>";
        if ($i != 1) {
            echo "<span dir='rtl'>".($i-1).") </span>";
        }
        
        echo '<textarea dir="rtl" name="phrases[]">'.$new_phrase.'</textarea></div><br>';
        if($i == 1) {
            echo "<div align='center'><br>***************************<br></div>";
        }
        $i++;
    }
    echo '<input type="submit" value="הוסף" name="add"></form>';
    
    var_dump($arr);
    //echo $new_name;
}

if(isset($_POST["add"])) {
    $phrases = $_POST["phrases"];
    $ansMarker = "@";
    $commentMarker = "//";
    $foundAns = false;
    
    $i = 1;
    
    if (sizeof($phrases) <= 1) {
        pop("no question/answers!!");
    }
    
    foreach($phrases as $phrase) {
        if(contains($phrase, $ansMarker)) {
            $foundAns = true;
        }
    }
    
    if ($foundAns == false) {
        pop("no right answer");
    }
    
    $answerOf = "NULL";
    foreach($phrases as $phrase) {
        if ($phrase == "") {
            continue;
        }
        
        $isAns = 0;
        $isQuestion = 0;
        
        if ($i == 1) {
            $isQuestion = 1;
            $isAns = "NULL";
        }
        
        // is answer?
        if(contains($phrase, $ansMarker)) {
            $foundAns = true;
            $isAns = 1;
            $phrase = str_replace("@", "", $phrase);
            
            if ($i == 1) {
                die("Error: question cant be the answer!");
            }
        }
        
        // there's comment?
        if(contains($phrase, $commentMarker)) {
            $phrase = explode($commentMarker, $phrase);
            $onlyPhrase = $phrase[0];
            $comment = $phrase[1];
        } else {
            $onlyPhrase = $phrase;
            $comment = "";
        }

        $onlyPhrase = trimmer(escape($onlyPhrase));
        $comment = trimmer(escape($comment));
        
        if ($i == 1) {
            $addSql = "UPDATE phrases SET answerOf = pID WHERE answerOf IS NULL";
        }
        
        query("INSERT INTO phrases (phraseName, answerOf, isQuestion, isRight, comment)
               VALUES ('$onlyPhrase', $answerOf, $isQuestion, $isAns, '$comment');
               $addSql");
         
               
        $answerOf = mysqli_insert_id($db);
        $i++;
    }
}
?>