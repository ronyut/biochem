<?php

require("config.php");
require("functions.php");

$status = array("success" => true);

switch($_GET['action']){
    case "addTag":
        $tag = trimmer(escape($_POST["tag"]));
        $pid = $_POST["pid"];
        $tagID = insertNewTag($tag);
        query("INSERT INTO tag2phrase (tagID, pID) VALUE ($tagID, $pid)");
        break;
    case "removeTag":
        $tag = trimmer(escape($_POST["tag"]));
        $pid = $_POST["pid"];
        $status["success"] = removeTag($tag, $pid);
        break;
    case "updatePhrase":
        $pid = $_POST["pid"];
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
                break;
            }
        }
        
        $escaped = trimmer(escape($_POST["text"]));
        query("UPDATE phrases SET $column = '$escaped' WHERE pID=$pid");
        break;
    case "toggleAnswer":
        $pid = $_POST["pid"];
        $isCorrect = isCorrectAnswer($pid);
        if ($isCorrect == null) {
            $status["success"] = false;
        } else {
            $newVal = abs($isCorrect - 1);
            $status["isCorrect"] = intToBool($newVal);
            query("UPDATE phrases SET isRight = $newVal WHERE pID = $pid");
        }
        break;
    default:
        break;
}

header('Content-Type: application/json');
echo json_encode($status);

?>