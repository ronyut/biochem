<?php

require("inc/config.php");
require("inc/functions.php");

$id = (int) $_GET['id'];
$questionName = getValueFromDB("SELECT * FROM phrases WHERE pID = $id AND isQuestion = 1", "phraseName");
if (!$questionName) {
    die("Question with this ID does not exist!");
}

$pageTitle = $questionName;
$styleCSS = "item.css";
require("inc/header.php");

?>
    <div class="article-container" pid="<?=$id?>">
    <?php
    showItem($id, null, $editable);
    $tags = getTagsByPid($id, false);
    //$tagsStr = implode(",", array_values($tags));
    ?>
    <small id="new-answer" class="for-editors form-text text-muted">
        <span>+</span>
    </small>
    <hr>
    <br />
    </div>
    <footer class="tags-container">
        <input class="tags-input" pid="<?=$id?>" type="text" data-role="tagsinput">
        <br>
        <div class="hidden">
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
        </div>
        <div id="tips" class="for-editors">
            <small class="form-text text-muted">
                <u>לחץ כדי לראות טיפים לעריכת שאלה</u>
                <ol>
                    <li>בתיבה שמעל השורה הזו תוכל להוסיף תגיות לשאלה שיעזרו לך למצוא אותה בקלות אחר כך</li>
                    <li>תוכל לסמן תשובה כנכונה/לא נכונה על ידי לחיצה על Ctrl וקליק שמאלי בעכבר</li>
                    <li>תוכל להוסיף הערה ע"י הוספת שני לוכסנים (//) בסוף הטקסט ולאחריהם להזין את ההערה. כדי לראות את ההערה יש לרפרש את הדף</li>
                    <li>כדי להפוך את השאלה למוסתרת, לחץ על העין שמשמאל לשאלה למעלה</li>
                    <li>כדי למחוק תשובה, פשוט מחק את התוכן שלה ורענן את הדף</li>
                    <li>כדי להוסיף תשובה חדשה, לחץ על סימן הפלוס שמתחת לתשובות</li>
                    <li>כדי לקשר לשאלה מסוימת בתוך הערה, יש להזין בהערה שטרודל ואז את המס' הסידורי של השאלה, למשל @123</li>
                </ol>
            </small>
        </div>
    </footer>
    <div class="iframe" style="height:300px; margin-bottom:30px;"></div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-tagsinput.js"></script>
    <script src="js/typeahead.bundle.js"></script>
    <script src="js/jquery.md5.js"></script>
    <script src="js/main.js"></script>
    <script>
    pageLoaded("<?=$isEditable?>" == "true");
    $(".iframe").html('<iframe src="comments.php?qid=' + <?=$id?> +'" width="90%" style="height:100%" border="0">');
    </script>
    <?php if($isEditable){ ?>
    <script src="js/editor.js"></script>
    <?php } ?>
</body>
</html>