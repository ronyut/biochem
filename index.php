<?php

$is_index = true;
$styleCSS = "index.css";
$showBackToTopButton = true;
$showSearchForm = true;

require("inc/config.php");
require("inc/functions.php");
require("inc/header.php");


$order = "abc";
if(isset($_GET['heat'])){
    $order = "heat";
}
  
?>
    
    <div class="page-wrapper">
        <div align="center" class="tags-search-wrapper">
            <div class="tags-search">
                <img src="img/flask.gif" width="150" height="150" border="0">
            </div>
            <div id="cnt-visible-wrapper"><span id="cnt_visible">טוען</span> תוצאות</div><br>
        </div>
        <div id="articles"></div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-tagsinput.js"></script>
    <script src="js/typeahead.bundle.js"></script>
    <script src="js/jquery.md5.js"></script>
    <script src="js/main.js"></script>
    <script>
    putTagsInFilterArea("<?=$order?>");
    loadAllQuestions("<?=$isEditable?>");
    </script>
    <?php if($editable){ ?>
    <script src="js/editor.js"></script>
    <?php } ?>
</body>
</html>