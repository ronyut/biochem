<?php

require("inc/config.php");
require("inc/functions.php");

$id = (int) $_GET['id'];
$questionName = getValueFromDB("SELECT * FROM phrases WHERE pID = $id AND isQuestion = 1", "phraseName");
?>
<html lang="he" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-tagsinput.css">


    <title><?=$questionName?></title>

    <style>
    body{
        padding: 3%;
        text-align:right;
    }
    
    .correct
    {
        color: green;
    }
    
    .incorrect
    {
        color: grey;
    }
    
    .red
    {
        color: red;
        font-weight: bold;
    }
    
    footer {
        padding: 2rem 1rem;
        background-color: #e9ecef;
        border-radius: .3rem;
        width:90%;
        position: absolute;
    }
    footer h2 {
        display:inline;
    }
    
    footer span.bullet {
        font-size: 26px;
    }
    
    h1 {
        font-size:32px;
    }
    
    h2 {
        font-size:26px;
    }
    </style>

</head>

<body>
    <?php
    showItem($id, null, true);
    $tags = getTagsByPid($id, false);
    //$tagsStr = implode(",", array_values($tags));
    ?>
    <footer class="tags-container">
        <input class="tags-input" pid="<?=$id?>" type="text" data-role="tagsinput">
        <br>
        <?php
        $i = 1;
        foreach($tags as $tagID => $tagName) {
            echo '<h2>'.$tagName.'</h2>';
            if(sizeof($tags) != $i) {
                echo ' <span class="bullet">•</span> ';
            }
            $i++;
        }
        ?>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-tagsinput.js"></script>
    <script src="js/typeahead.bundle.js"></script>
    <script src="js/jquery.md5.js"></script>
    <script src="js/main.js"></script>
    <script src="js/editor.js"></script>
    <?=getTagsDataForJS($id)?>
</body>
</html>