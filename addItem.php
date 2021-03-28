<?php

require("inc/config.php");
require("inc/functions.php");

$pageTitle = "הוספת שאלה חדשה";
$styleCSS = "addItem.css";
require("inc/header.php");

?>
    <div class="article-container">
        <h1 align="center">הוספת שאלה חדשה</h1>
        <hr>
        <form>
            <div class="form-group">
                <label for="question">השאלה</label>
                <textarea class="form-control" id="question" aria-describedby="questionHelp" placeholder="הזן כאן את השאלה"></textarea>
                <small id="questionHelp" class="form-text text-muted">טיפ: ניתן להוסיף הערה ע"י הוספת שני לוכסנים (//) בסוף הטקסט ולאחריהם להזין את ההערה</small>
            </div>
            <div id="answers_div" class="form-group">
                <label>תשובות</label>
                <div class="answer-wrapper">
                    <textarea class="form-control answer" placeholder="הקלד תשובה אפשרית"></textarea>
                    <input class="form-check-input checkbox-correct" type="checkbox">
                    <label class="form-check-label label-correct">תשובה נכונה</label>
                    <label class="remove red">מחק</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">סיימתי</button>
        </form>
    </div>
    <div class="iframe"></div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/typeahead.bundle.js"></script>
    <script src="js/jquery.md5.js"></script>
    <script src="js/main.js"></script>
    <script>
    // auto add new answer
    $(document).on("keyup", ".answer", function(){
        let ans_wrp = $(".answer-wrapper");
        let my_parent = $(this).parent();
        if (ans_wrp.index(my_parent) + 1 == ans_wrp.length && $(this).val() != "") {
            $("#answers_div").append($(".answer-wrapper:first").clone());
            $(".answer-wrapper:last .answer").val("");
        }
    });

    // toggle correct answer
    $(document).on("click", ".label-correct", function(){
        let checkbox = $(this).parent().find(".checkbox-correct")
        checkbox.attr("checked", !checkbox.attr("checked"));
    });
    
    //remove answer
    $(document).on("click", ".remove", function(){
        if ($(".answer-wrapper").length > 1) {
            $(this).closest(".answer-wrapper").remove();
        }
    });
    </script>
    <script src="js/editor.js"></script>
</body>
</html>