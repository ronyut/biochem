<?php

require("inc/config.php");
require("inc/functions.php");

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

    <title>ביוכימיה - שחזורים</title>

    <style>
    h1
    {
        font-size:32px;
        width: 100%;
    }
    
    h2 {
        font-size: 26px;
    }
    
    body{
        text-align:right;
        padding: 3%;
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
    
    .tags-search-wrapper {
        padding: 2rem 1rem;
        background-color: #e9ecef;
        border-radius: .3rem;
    }
    
    .tags-search {
        width: 80%;
        background: white;
        padding: 20px;
        border-radius: 10px;
    }
    
    .tag-link{
        margin-top:3px;
        margin-right: 3px;
    }
    
    .not-really {
        display:none;
    }
    
    .tags-inactive {
        background: black;
        padding: 20px;
        border-radius: 10px;
    }
    
    .article-container[show=false]{
        display:none;
    }
    
    .tag-active{
        border: 4px solid black;
    }
    
    #cnt_visible{
        font-weight: bold;
    }
    
    #cnt-visible-wrapper{
        margin-top: 20px;
    }

    </style>

</head>

<body>
    <div align="center" class="tags-search-wrapper">
    <div class="tags-search">
    <?php
        $tags = getAllTags();
        $i = 0;
        foreach($tags as $tag => $details) {
            $hidden = "";
            if ($i > 30) {
                $hidden = "hidden";
            }
            echo "<button dir='auto' type='button' class='btn tag-link $hidden'
                    style='background-color:".$details['color']."' tid='".$tag."'>".$details['name']."</button>";
            $i++;
        }
    ?>
    </div>
    <div id="cnt-visible-wrapper"><span id="cnt_visible"></span> תוצאות</div>
    </div>
    
    <?php
    
    $i = 1;
    $query0 = query("SELECT * FROM phrases WHERE isQuestion = 1");
    while($row0 = mysqli_fetch_array($query0)){
        if (!isset($_GET['all'])) {
            echo "<a href='item.php?id=".$row0['pID']."'>".$row0['phraseName']."</a><hr>";
        }
        else
        {
            $id = $row0['pID'];
            echo "<div class='article-container' pid='".$id."' show='true'>";
            showItem($id, $i, isset($_GET["editable"]));
            //$tags = getTagsByPid($id);
            //$tagsStr = implode(",", array_values($tags));
            ?>
                <div class="tags-container">
                    <input class="tags-input" pid="<?=$id?>" type="text" data-role="tagsinput">
                </div>
            </div>
            <?php
        }
        $i++;
    }
    ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-tagsinput.js"></script>
    <script src="js/typeahead.bundle.js"></script>
    <script src="js/jquery.md5.js"></script>
    <script src="js/main.js"></script>
    <?php if(isset($_GET["editable"])){ ?>
    <script src="js/editor.js"></script>
    <?php } ?>
    <?=getTagsDataForJS()?>
</body>
</html>