<?php

require("config.php");
require("functions.php");
header('Content-Type: application/json');

$status = array("success" => true);

if(!isset($_GET['action'])) {
    die("No action specified");
}

switch($_GET['action']){
    /*
        Add a tag
    */
    case "addTag":
        if(!isset($_POST["tag"]) || empty($_POST["tag"])) {
            $status["success"] = false;
            break;
        }
        $tag = trimmer(escape($_POST["tag"]));
        
        if (empty($tag)) {
            $status["success"] = false;
            break;
        }
        
        $tagID = insertNewTag($tag);
        $approved = $USER["isEditor"];
        
        if(isset($_POST["tid"]) && isset($_POST["pid"])) {
            $pid = (int) $_POST["pid"];
            query("INSERT INTO tag2phrase (tagID, pID, approved) VALUE ($tagID, $pid, $approved)");
            addHistory("Add", "Tag",$tag, array("qid" => $pid));
        }
        
        $status["tid"] = (int) $tagID;
        $status["approved"] = intToBool($approved);
        break;
    /*
        Remove a tag
    */
    case "removeTag":
        if(!isset($_POST["tid"]) || !isset($_POST["pid"]) || !isset($_POST["text"])) {
            $status["success"] = false;
            break;
        }
    
        $tid = (int) $_POST["tid"];
        $pid = (int) $_POST["pid"];
        $tagName = escape($_POST["text"]);
        
        if ($USER["isEditor"] == 1) {
            $status["success"] = removeTag($tid, $pid, $tagName);
        } else {
            $status["success"] = false;
        }
        break;
    /*
        Update a phrase
    */
    case "updatePhrase":
        $qid = (int) $_POST["qid"];
        $pid = (int) $_POST["pid"];
        $hash = $_POST["hash"];
        $isComment = $_POST["isComment"];
        $status["hash"] = md5($_POST["text"]);
        $commentMarker = "//";
        
        if (md5($_POST["text"]) == $hash) {
            break;
        }
        
        if ($USER["isEditor"] == 0) {
            $status["success"] = false;
            break;
        }
        
        // HTML purifier
        require_once 'html-purifier/HTMLPurifier.auto.php';
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $clean_html = $purifier->purify($_POST["text"]);
        $status["clean"] = $clean_html;
        
        $column = "phraseName";
        if($isComment == "true") {
            $column = "comment";
        } else {
            if(contains($clean_html, $commentMarker)) {
                $phrase = explode($commentMarker, $clean_html);
                $onlyPhrase = trimmer(escape($phrase[0]));
                $comment = trimmer(escape($phrase[1]));
                query("UPDATE phrases SET phraseName = '$onlyPhrase', comment = '$comment' WHERE pID=$pid");
                addHistory("Edit", "Phrase", $onlyPhrase, array("qid" => $qid, "pid" => $pid));
                break;
            }
        }
        
        $escaped = trimmer(escape($clean_html));
        
        query("UPDATE phrases SET $column = '$escaped' WHERE pID=$pid");
        
        if ($column == "phraseName") {
            addHistory("Edit", "Phrase", $escaped, array("qid" => $qid, "pid" => $pid));
        }
        break;
    /*
        Toggle answer: correct/incorrect
    */
    case "toggleAnswer":
        $qid = (int) $_POST["qid"];
        $pid = (int) $_POST["pid"];
        $text = escape($_POST["text"]);
        
         if ($USER["isEditor"] == 0) {
            $status["success"] = false;
            break;
        }
        
        $isCorrect = isCorrectAnswer($pid);
        if ($isCorrect == null) {
            $status["success"] = false;
        } else {
            $newVal = abs($isCorrect - 1);
            $status["isCorrect"] = intToBool($newVal);
            query("UPDATE phrases SET isRight = $newVal WHERE pID = $pid");
            
            $actionType = "Delete";
            if($status["isCorrect"]){
                $actionType = "Add";
            }
            addHistory($actionType, "Answer", $text, array("qid" => $qid, "pid" => $pid));
        }
        break;
    /*
        getTagsDataForJS
    */
    case "getTagsDataForJS":
        $qid = (int) $_GET["qid"];
        $status = getTagsDataForJS($qid);
        break;
    /*
        logout
    */
    case "logout":
        setcookie(COOKIE_HASH_NAME, "", time() - 3600 * 24, "/");
        break;
    /*
        getAllTagsForFilter
    */
    case "getAllTagsForFilter":
        $tags = getAllTags(isset($_GET["order"]) && $_GET["order"] == "heat");
        echo json_encode($tags, JSON_UNESCAPED_UNICODE);
        exit;
        break;
    /*
        getAllQuestions
    */
    case "getAllQuestions":
        $json = array();
        $i = 1;
        $query = query("SELECT * FROM phrases WHERE isQuestion = 1");
        while($row = mysqli_fetch_array($query)){
            $qid = $row['pID'];
            $isEditable = isset($_GET['editable']) && $_GET['editable'] == "true";
            $item = getOneItemJson($qid, $i, $isEditable);
            array_push($json, $item);
        }
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        exit;
        break;
    /*
        getHistory
    */    
    case "getHistory":
    $json = array();
        $qid = (int) $_GET["qid"];
        $query = query("SELECT * FROM history WHERE qid = $qid ORDER BY hID DESC");
        while($row = mysqli_fetch_array($query)){
            $user = getUserByID($row["userID"]);
            $fullName = $user["firstName"]." ".$user["lastName"];
            $item = array("action" => $row["actionType"], "entity" => $row["entityType"],
                          "userID" => (int) $row["userID"], "userFullName" => $fullName, "content" => $row["content"],
                          "pid" => (int) $row["pid"] , "time" => $row["time"]);
            array_push($json, $item);
        }
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        exit;
        break;
    /*
        Add a talkback
    */
    case "addTalkback":
        if(!isset($_POST["talkback"]) || !isset($_POST["qid"])) {
            $status["success"] = false;
            break;
        }
        $qid = (int) $_POST['qid'];
        $talkback = escape(htmlspecialchars($_POST["talkback"]));
        
        $replyToTalkbackID = "NUll";
        $replyToUserID = "NULL";
        if (isset($_POST['replyToTalkbackID']) && isset($_POST['replyToUserID'])) {
            $replyToTalkbackID = (int) $_POST['replyToTalkbackID'];
            $replyToUserID = (int) $_POST['replyToUserID'];
        }
        
        $sql = "INSERT INTO talkbacks (userID, msg, underTalkback, qID, replyToUser) 
                VALUES ('".$USER["userID"]."', '$talkback', $replyToTalkbackID, $qid, $replyToUserID)";
        query($sql); 
        break;
    /*
        getTalkbacks
    */    
    case "getTalkbacks":
    $json = array();
        $qid = (int) $_GET["qid"];
        $query = query("SELECT * FROM talkbacks WHERE qID = $qid ORDER BY talkbackID DESC");
        while($row = mysqli_fetch_array($query)){
            $user = getUserByID($row["userID"]);
            $replyToUser = getUserByID($row["replyToUser"]);
            $fullName = makeFullName($user);
            $replyToUserFullName = makeFullName($replyToUser);
            
            $item = array("userID" => (int) $row["userID"],
                          "id" => (int) $row["talkbackID"],
                          "userFullName" => $fullName,
                          "msg" => $row["msg"],
                          "underTalkback" => $row["underTalkback"], // don't add (int) so it cal be null
                          "time" => $row["time"],
                          "photo" => $user["photo"],
                          "replyToUserName" => $replyToUserFullName,
                          "replyToUserID" => $row["replyToUser"] // don't add (int) so it cal be null
                          );
            array_push($json, $item);
        }
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        exit;
        break;    
    default:
        break;
}

echo json_encode($status, JSON_UNESCAPED_UNICODE);
?>