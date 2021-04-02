<?php

require("inc/config.php");
require("inc/functions.php");

$pageTitle = "שינויים אחרונים | השחזורון";
$styleCSS = "addItem.css";
require("inc/header.php");

?>
    <div class="article-container">
        <h1 align="center">היסטוריית הפעילות באתר</h1>
        <hr>
        <div class="tab-pane" id="history-pane">
            <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">שאלה</th>
                    <th scope="col">תאריך</th>
                    <th scope="col">פעילות</th>
                    <th scope="col" style="width: 50%;">תוכן</th>
                </tr>
            </thead>
            <tbody id="history_tbody">
                <tr>
                    <td colspan="5">פול גז בניוטרל?</td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/jquery.md5.js"></script>
    <script src="js/main.js"></script>
    <script src="js/talkback.js"></script>
    <script>
        showHistory(true);
    </script>
</body>
</html>