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
        if(!isset($_POST["tag"]) || empty($_POST["tag"]) || !$USER || $USER["isEditor"] == 0) {
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
            addHistory("Add", "Tag", $tag, array("qid" => $pid));
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
        
        if (!$USER || $USER["isEditor"] == 0) {
            $status["success"] = false;
            break;
        }
        
        // HTML purifier
        $clean_html = purifyHtml($_POST["text"]);
        $status["clean"] = $clean_html;
        
        $entity = "Phrase";
        if ($qid == $pid) {
            $entity = "Question";
        }

        $column = "phraseName";
        if($isComment == "true") {
            $column = "comment";
            $entity = "Comment";
        } else {
            // add comment
            if(contains($clean_html, $commentMarker)) {
                $phrase = explode($commentMarker, $clean_html);
                $onlyPhrase = trimmer($phrase[0]);
                $comment = trimmer($phrase[1]);
                query("UPDATE phrases SET phraseName = '$onlyPhrase', comment = '$comment' WHERE pID=$pid");
                addHistory("Edit", $entity, $onlyPhrase, array("qid" => $qid, "pid" => $pid));
                break;
            }
        }
        
        $escaped = trimmer($clean_html);
        
        query("UPDATE phrases SET $column = '$escaped' WHERE pID=$pid");
        
        addHistory("Edit", $entity, $escaped, array("qid" => $qid, "pid" => $pid));
        
        if ($column == "phraseName") {
            if (empty($escaped)) {
                query("UPDATE phrases SET is_hidden = 1 WHERE pID=$pid");
            } else {
                query("UPDATE phrases SET is_hidden = 0 WHERE pID=$pid");
            }
        }

        break;
    /*
        Toggle answer: correct/incorrect
    */
    case "toggleAnswer":
        $qid = (int) $_POST["qid"];
        $pid = (int) $_POST["pid"];
        $text = escape($_POST["text"]);
        
		if (!$USER || $USER["isEditor"] == 0) {
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
        $query = query("SELECT * FROM phrases WHERE isQuestion = 1 AND is_hidden = 0");
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
        get all History
    */    
    case "getAllHistory":
        $json = array();
        $query = query("SELECT * FROM history ORDER BY hID DESC");
        while($row = mysqli_fetch_array($query)){
            $user = getUserByID($row["userID"]);
            $fullName = $user["firstName"]." ".$user["lastName"];
            $item = array("action" => $row["actionType"], "entity" => $row["entityType"],
                          "userID" => (int) $row["userID"], "userFullName" => $fullName, "content" => $row["content"],
                          "pid" => (int) $row["pid"], "qid" => (int) $row["qid"], "time" => $row["time"]);
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
        
        // HTML purifier
        $clean_html = purifyHtml($_POST["talkback"]);
        $talkback = trimmer(escape($clean_html));
        
        $replyToTalkbackID = "NULL";
        $replyToUserID = "NULL";
        if (isset($_POST['replyToTalkbackID']) && isset($_POST['replyToUserID'])) {
            $replyToTalkbackID = (int) $_POST['replyToTalkbackID'];
            $replyToUserID = (int) $_POST['replyToUserID'];
        }
        
        $sql = "INSERT INTO talkbacks (userID, msg, underTalkback, qID, replyToUser) 
                VALUES ('".$USER["userID"]."', '$talkback', $replyToTalkbackID, $qid, $replyToUserID)";
        query($sql); 
        $status["id"] = mysqli_insert_id($db);
        break;
    
    /*
        updateTalkback
    */ 
    case "updateTalkback":
        if(!isset($_POST["talkback"]) || !isset($_POST["tbid"])) {
            $status["success"] = false;
            break;
        }
        $tbid = (int) $_POST['tbid'];
        $talkback = purifyHtml($_POST["talkback"]);
        
        $sql = "UPDATE talkbacks SET msg = '$talkback' WHERE userID = ".$USER["userID"]." AND talkbackID = $tbid";
        query($sql); 
        
        $status["clean"] = $talkback;
        
        break;
    
    /*
        getTalkbacks
    */    
    case "getTalkbacks":
        $json = array();
        $qid = (int) $_GET["qid"];
        $query = query("SELECT * FROM talkbacks WHERE qID = $qid ORDER BY underTalkback, talkbackID DESC");
        while($row = mysqli_fetch_array($query)){
            $user = getUserByID($row["userID"]);
            $replyToUser = getUserByID($row["replyToUser"]);
            $fullName = makeFullName($user);
            $replyToUserFullName = makeFullName($replyToUser);
            
            $item = array("userID" => (int) $row["userID"],
                          "id" => (int) $row["talkbackID"],
                          "userFullName" => $fullName,
                          "msg" => nl2br($row["msg"]),
                          "underTalkback" => $row["underTalkback"], // don't add (int) so it cal be null
                          "time" => date("d/m/Y H:i", strtotime($row["time"])),
                          "photo" => $user["photo"],
                          "replyToUserName" => $replyToUserFullName,
                          "replyToUserID" => $row["replyToUser"] // don't add (int) so it cal be null
                          );
            array_push($json, $item);
        }
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        exit;
        break;
    case "savePhoto":
        savePhoto($USER["photo"], $USER["userID"]);
        $status["photo"] = $USER["photo"];
        break;
    /*
    Add new question
    */
    case "addItem":
        $json = file_get_contents('php://input');
        $json = json_decode($json, TRUE); //convert JSON into array
        $question =  trimmer(escape(purifyHtml($json["question"])));
        $answers = $json["answers"]["text"];
        $checkboxes = $json["answers"]["checkbox"];
        $commentMarker = "//";

         // there's comment?
         if(contains($question, $commentMarker)) {
            $phrase = explode($commentMarker, $question);
            $question = $phrase[0];
            $comment = $phrase[1];
        } else {
            $comment = "";
        }

        $addSql = "UPDATE phrases SET answerOf = pID WHERE answerOf IS NULL";
        query("INSERT INTO phrases (phraseName, answerOf, isQuestion, isRight, comment)
                   VALUES ('$question', NULL, 1, NULL, '$comment')");
        $qid = mysqli_insert_id($db);
        query($addSql);

        // add question to  history
        addHistory("Add", "Question", $question, array("qid" => $qid));

        $i = 0;
        foreach($answers as $answer) {
            if ($answer == "") {
                $i++;
                continue;
            }
            
            // is answer?
            $isCorrect = $checkboxes[$i] == "1" ? 1 : 0;
            
            // there's comment?
            if(contains($answer, $commentMarker)) {
                $phrase = explode($commentMarker, $answer);
                $onlyPhrase = $phrase[0];
                $comment = $phrase[1];
            } else {
                $onlyPhrase = $answer;
                $comment = "";
            }
    
            $onlyPhrase = trimmer(escape($onlyPhrase));
            $comment = trimmer(escape($comment));
            
            $sql = "INSERT INTO phrases (phraseName, answerOf, isQuestion, isRight, comment)
                    VALUES ('$onlyPhrase', $qid, 0, $isCorrect, '$comment')";
            query($sql);
			
			$pid = mysqli_insert_id($db);
			
            // add answer to history
            addHistory("Add", "Phrase", $onlyPhrase, array("qid" => $qid, "pid" => $pid));

			if ($isCorrect) {
				// add correct answer to history
				addHistory("Add", "Answer", $onlyPhrase, array("qid" => $qid, "pid" => $pid));
			}
			
            $i++;
        }

        $status["qid"] = $qid;

        break;

    /*
    Hide question ("remove" it)
    */
    case "toggleQuestionVisibility":
		if (!$USER || $USER["isEditor"] == 0) {
            $status["success"] = false;
            break;
        }
		
        $qid = (int) $_POST["qid"];
        $toggle = (int) $_POST["toggle"];
        query("UPDATE phrases SET is_hidden = $toggle WHERE answerOf = $qid");
        $status["is_hidden"] = $toggle;
        
        $op = "Show";
        if ($toggle == 1) {
            $op = "Hide";
        }
        addHistory($op, "Question", "", array("qid" => $qid));

        break;
    /*
    Add new answer
    */
    case "addNewAnswer":
		if (!$USER || $USER["isEditor"] == 0) {
            $status["success"] = false;
            break;
        }
		
        $qid = (int) $_POST["qid"];
        query("INSERT INTO phrases (phraseName, answerOf, comment) VALUES ('', $qid, '')");
        $pid = mysqli_insert_id($db);
        
        addHistory("Add", "Phrase", "", array("qid" => $qid, "pid" => $pid));

        $status["pid"] = $pid;
        break;
    default:
        $status["success"] = false;
        break;
}

echo json_encode($status, JSON_UNESCAPED_UNICODE);
?>