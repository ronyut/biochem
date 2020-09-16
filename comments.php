<?php

require("inc/config.php");
require("inc/functions.php");

if(!isset($_GET['qid'])) {
    die("No id");
}

$qid = (int) $_GET['qid'];

?>
<html lang="he">
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.png">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

    <title>תגובות</title>
    
    <style>
    @import url(http://fonts.googleapis.com/earlyaccess/opensanshebrew.css);

    *, :after, :before {
        direction: rtl;
    }
    
    body {
        font-family: 'Open Sans Hebrew', sans-serif;
    }
    
    
    .login, .logout { 
        position: absolute; 
        top: -3px;
        right: 0;
    }
    .page-header { position: relative; }
    .reviews {
        color: #555;    
        font-weight: bold;
        margin: 10px auto 20px;
        content: "";
    }
    
    .invisible {
        visibility: invisible;
    }
    
    .media .media-object {
        max-width: 120px;
        margin-top: 30px
    }
    .media-body { position: relative; }
    .media-date {
        display:inline;
        margin-right: 5px;
        color: grey;
    }
    .media-title { direction: ltr; }
    .media-count { font-size:26px; }
    .media-talkback { margin: 20px; }
    .media-replied { margin: 0 50px 20px 0px; }
    .media-replied .media-title { padding-left: 6px; }
    
    .btn-circle {
        font-weight: bold;
        font-size: 12px;
        padding: 6px 15px;
        border-radius: 20px;
    }
    .btn-circle span { padding-right: 6px; }
    .tab-content {
        padding: 50px 15px;
        border: 1px solid #ddd;
        border-top: 0;
        border-bottom-right-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    
    .nav-tabs>li{
        float: right;
    }
    
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
        width: 100%;
    }
    
    .rtl{
        direction: rtl;
        text-align: right;
    }
    
    .ltr{
        direction: ltr;
        text-align: left;
    }
    
    .talkback h4 {
         display:inline;
    }
    
    .talkback button {
        background-color: transparent;
        border: 0;
        font-size: 28px;
        color: #0B7EB5;
    }
    
    .talkback button span {
        font-size: 14px;
        color: #787878;
        font-weight:bold;
        vertical-align: super;
    }
    
    .arrow-replyTo {
        font-size: 16px;
        color: #ababab;
        display: inline;
    }
    
    .arrow-replyTo span{
        
    }
    
    button.vote:focus{
        outline: none;
    }
    
    button.vote:hover{
        color: #15a4e9;
    }
    
    .table{
        text-align:center;
        overflow-y: auto;
        max-height: 200px;
    }
    
    .table th {
        text-align:center;
    }
    
    .temp-textarea-reply{
        width: 500px;
    }
    
    .temp-textarea-reply[contenteditable="true"]{
        width: 500px;
    }
    
    .textarea[contenteditable="true"]{
        min-height: 100px;
        border: 2px solid black;
        border-radius: 5px;
        padding: 5px;
        background: white;
    }
    
    #talkbackForm .textarea{
        border: 1px solid #c7c7c7;
        text-align: right;
    }
    
    </style>
</head>
<body qid="<?=$qid?>" userID="<?=$USER["userID"]?>">
<div align="center" class="page-wrapper">
    <div class="talkback-tabs">
        <ul class="nav nav-tabs" role="tablist">
            <li class="active">
                <a href="#talkbacks" role="tab" data="talkbacks" data-toggle="tab">
                    <h4 class="reviews text-capitalize">כל התגובות</h4>
                </a>
            </li>
            <li>
                <a href="#add-talkback" role="tab" data-toggle="tab">
                    <h4 class="reviews text-capitalize">תגובה חדשה</h4>
                </a>
            </li>
            <li>
                <a href="#history-pane" role="tab" data="history" data-toggle="tab">
                    <h4 class="reviews text-capitalize">היסטוריה</h4>
                </a>
            </li>
        </ul>            
        <div class="tab-content">
            <div class="tab-pane active" id="talkbacks"> 
                <ul class="media-list" id="threads"></ul> 
            </div>
            <div class="tab-pane" id="add-talkback">
                <form id="talkbackForm">
                    <div class="row">
                        <div class="col-xs-12 pull-right">
                            <div class="textarea" contenteditable="true" dir="rtl" class="form-control"></div>
                            <br>
                            <button class="btn btn-primary btn-circle disabled" type="submit" id="submitTalkback">
                                <span class="glyphicon glyphicon-send"></span> פרסמ/י תגובה
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane" id="history-pane">
                <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">תאריך</th>
                        <th scope="col">פעילות</th>
                        <th scope="col">תוכן</th>
                    </tr>
                </thead>
                <tbody id="history_tbody">
                    <tr>
                        <td colspan="4">עם שאינו יודע את עברו, ההווה שלו דל ועתידו לוט בערפל</td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="templates" class="hidden">
        <!-- like button -->
        <svg id="likeButton" width="1em" height="1em" viewBox="0 0 256 256">
            <path fill="currentColor" d="M85 177H46V84h39v93zm116.2-92h-34.7c0-.1.1-.1.1-.2 7.9-15.2 9.7-22.7 9.4-35.3-.3-12.3-11.8-23-20.6-23.1-7.3 0-10.7 7.3-12.2 11.3-4.8 12.3-7.8 23.6-24.7 46.1-.5.7-1.3 1.1-2.1 1.1h-.1c-9.5 0-17.2 7.7-17.2 17.2v58.6c0 9.5 7.7 17.2 17.2 17.2h61c8.1 0 10.6-5.4 14.6-14.6 0 0 21.3-45.8 24.3-62.6 1.4-7.9-7-15.7-15-15.7z"></path>
        </svg>
        <!-- dislike button -->
        <svg id="dislikeButton" width="1em" height="1em" viewBox="0 0 256 256">
            <path fill="currentColor" d="M173 84h39v93h-39V84zM56.8 176h34.7c0 .1-.1.1-.1.2-7.9 15.2-9.7 22.7-9.4 35.3.3 12.3 11.8 23 20.6 23.1 7.3 0 10.7-7.3 12.2-11.3 4.8-12.3 7.8-23.6 24.7-46.1.5-.7 1.3-1.1 2.1-1.1h.1c9.5 0 17.2-7.7 17.2-17.2v-58.6c0-9.5-7.7-17.2-17.2-17.2h-61c-8.1 0-10.6 5.4-14.6 14.6 0 0-21.3 45.8-24.3 62.6-1.4 7.9 7 15.7 15 15.7z"></path>
        </svg>
        <!-- reply to button arrow -->
        <svg id="replyToArrow" width="1em" height="1em" viewBox="0 0 256 256">
            <path fill="currentColor" d="M213 141H70.4l50.1 50.9-16.5 16.8L25.6 129 104 49.3l16.5 16.8L70.4 117H213v24z"></path>
        </svg>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="js/talkback.js"></script>
</body>
</html>
