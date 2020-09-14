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
        if(!isset($_POST["tag"]) || empty($_POST["tag"]) || !isset($_POST["approved"])) {
            $status["success"] = false;
            break;
        }
        $tag = trimmer(escape($_POST["tag"]));
        
        if (empty($tag)) {
            $status["success"] = false;
            break;
        }
        
        $tagID = insertNewTag($tag);
        $approved = boolToInt(textToBool($_POST["approved"]));
        
        if(isset($_POST["tid"]) && isset($_POST["pid"])) {
            $pid = (int) $_POST["pid"];
            query("INSERT INTO tag2phrase (tagID, pID, approved) VALUE ($tagID, $pid, $approved)");
            addHistory("Add", "Tag", $pid, "Anonymous", $tag, null);
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
        $status["success"] = removeTag($tid, $pid, $tagName);
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
        
        $column = "phraseName";
        if($isComment == "true") {
            $column = "comment";
        } else {
            if(contains($_POST["text"], $commentMarker)) {
                $phrase = explode($commentMarker, $_POST["text"]);
                $onlyPhrase = trimmer(escape($phrase[0]));
                $comment = trimmer(escape($phrase[1]));
                query("UPDATE phrases SET phraseName = '$onlyPhrase', comment = '$comment' WHERE pID=$pid");
                addHistory("Edit", "Phrase", $qid, "Anonymous", $onlyPhrase, $pid);
                break;
            }
        }
        
        $escaped = trimmer(escape($_POST["text"]));
        query("UPDATE phrases SET $column = '$escaped' WHERE pID=$pid");
        
        if ($column == "phraseName") {
            addHistory("Edit", "Phrase", $qid, "Anonymous", $escaped, $pid);
        }
        break;
    /*
        Toggle answer: correct/incorrect
    */
    case "toggleAnswer":
        $qid = (int) $_POST["qid"];
        $pid = (int) $_POST["pid"];
        $text = escape($_POST["text"]);
        
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
            addHistory($actionType, "Answer", $qid, "Anonymous", $text, $pid);
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
        require("config-google.php");
        session_destroy();
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
    default:
        break;
}

echo json_encode($status, JSON_UNESCAPED_UNICODE);
?>