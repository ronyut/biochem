<?php
	
    /**************************************************************
		isSecureConn
		check if https
	**************************************************************/
	function isSecureConn() {
      return
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443;
    }
    
    /**************************************************************
		getProtocol
		return http or htts
	**************************************************************/
    function getProtocol(){
        if(isSecureConn()) {
            return "https";
        }
        return "http";
    }
    
    
	/**************************************************************
		escape
		escape illegal chars
	**************************************************************/
	function escape($string) {
		global $db;
		return mysqli_real_escape_string($db, $string);
	}

	/**************************************************************
		query:
		Perform Mysqli query
	**************************************************************/
	function query($sql) {
		global $db;
		$query = mysqli_query($db, $sql) or die(mysqli_error($db));
		return $query;
	}
    
    /**************************************************************
		compare:
		Compare which number is bigger
	**************************************************************/
    function compare($a, $b) {
        return $a->percent < $b->percent;
    }
    
    /**************************************************************
		compareScore:
		Compare which number is bigger
	**************************************************************/
    function compareScore($a, $b) {
        return $a["score"] < $b["score"];
    }
    
    /**************************************************************
		intToBool:
		Int to bool
	**************************************************************/
    function intToBool($int) {
        if($int == 0) {
            return false;
        } else {
            return true;
        }
    }
    
    /**************************************************************
		textToBool:
		Text to bool
	**************************************************************/
    function textToBool($text) {
        return $text == "true";
    }
    
    /**************************************************************
		boolToInt:
		bool to int
	**************************************************************/
    function boolToInt($bool) {
        if($bool) {
            return 1;
        } else {
            return 0;
        }
    }
    
    /**************************************************************
		contains:
		Check if string has subtring
	**************************************************************/
    function contains($str, $needle) {
        return strpos($str, $needle) !== false;
    }
    
    /**************************************************************
		pop:
		Show JS alert and exit
	**************************************************************/
    function pop($msg){
        echo "<script>alert('$msg');</script>";
        exit();
    }
    
    /**************************************************************
		trimmer:
		Trim spaces and remove multipule spaces
	**************************************************************/
    function trimmer($input) {
        return trim(preg_replace("/\s+/u", " ", $input));
    }
    
    /**************************************************************
		showItem:
		Show a single item
	**************************************************************/
    function showItem($id, $index, $isEditable) {
        $editable = "";
        if($isEditable) {
            $editable = "contenteditable='true'";
        }
        
        $i = 1;
        $query = query("SELECT * FROM phrases WHERE pID = $id OR answerOf = $id ORDER BY pID ASC");
        while($row = mysqli_fetch_array($query)){
            // question
            if ($i == 1) {
                $name = $row["phraseName"];
                $nos = array("לא", "ללא", "אינו", "אינם", "אינה", "איננו", "איננה", "חוץ");
                foreach($nos as $no){
                    if (contains($row["phraseName"], $no." ")) {
                        $name = str_replace($no." ", "<span class='red'>$no</span> ", $row["phraseName"]);
                    }
                }
                
                echo "<hr><article><div class='row'><div class='col-md-11'><h1>";
                if ($index != null) {
                    echo $index.'. ';
                }
                echo "<span class='question phrase' $editable pid='".$row['pID']."'
                        hash='".md5($row["phraseName"])."' >".$name."</span></h1></div>
                            <div class='col-md-1'>
                            <a href='item.php?id=".$row['pID']."' target='_blank'>
                                <img src='img/link.png' border='0' width='20' height='20'></a>
                            </div>
                        </div>
                        <hr>
                        <ol>";
                
            } else {
                // answer
                $classAnswer = "incorrect";
                if ($row['isRight']) {
                    $classAnswer = "correct";
                }
                
                echo "<h2 class='$classAnswer'><li><span class='answer phrase' $editable pid='".$row['pID']."'
                      hash='".md5($row["phraseName"])."'>".$row['phraseName']."</span></li></h2>";
            }
            
            if ($row["comment"] != "") {
                echo "<div class='alert alert-warning comment' $editable hash='".md5($row["comment"])."'
                        pid='".$row['pID']."' role='alert'>".$row['comment']."</div>";
            }
                        
            $i++;
        }
        echo "</ol></article>";
    }
    
    /**************************************************************
		insertNewTag:
		Insert a new tag and return its new ID
        If alreadt exists, return its ID
	**************************************************************/
    function insertNewTag($tag) {
        global $db;
        
        $tagID = tagNameToID($tag);
        if($tagID != null) {
            return $tagID;
        }
        else {
            query("INSERT INTO tags (tagName) VALUES ('$tag')");
            return mysqli_insert_id($db);
        }
    }
    
    /**************************************************************
        removeTag:
        Remove tag from question
	**************************************************************/
    function removeTag($tagID, $pid, $text = "") {
        if($tagID != null) {
            $query = query("DELETE FROM tag2phrase WHERE tagID = $tagID AND pID = $pid");
            addHistory("Delete", "Tag", $pid, "Anonymous", $text, null);
            $query2 = query("SELECT * FROM tag2phrase WHERE tagID = $tagID");
            if(mysqli_num_rows($query2) == 0){
                $query = query("DELETE FROM tags WHERE tagID = $tagID");
            }
            
            return $query;
        } else {
            return false;
        }
    }
    
    /**************************************************************
        tagNameToID:
        Tag name to tag id
	**************************************************************/
    function tagNameToID($tag) {
        $query = query("SELECT * FROM tags WHERE tagName = '$tag'");
        if(mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_array($query)){
                return $row["tagID"];
            }                
        } else {
            return null;
        }
    }
    
    /**************************************************************
        getTagsByPid:
        Tag name to tag id
	**************************************************************/
    function getTagsByPid($pid = null, $raw = true) {
        $addition = "";
        if ($pid != null) {
            $addition = "WHERE pID = $pid";
        }
        
        $query = query("SELECT * FROM `tag2phrase` AS ref
                        JOIN tags ON ref.tagID = tags.tagID
                        $addition");
        if (!$raw) {
            $tags = array();
            while($row = mysqli_fetch_array($query)){
                $tags[$row["tagID"]] = $row["tagName"];
            }
        } else {
            $tags = $query;
        }
        
        return $tags;
    }
    
    /**************************************************************
        isCorrectAnswer:
        Get if answer is correct
	**************************************************************/
    function isCorrectAnswer($pid) {
        $query = query("SELECT * FROM phrases WHERE pID = ".$pid." AND isQuestion = 0");
        if(mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_array($query)){
                return $row["isRight"];
            }                
        } else {
            return null;
        }
    }
    
    /**************************************************************
        getValueFromDB:
        Get single value from db via sql query
	**************************************************************/
    function getValueFromDB($sql, $col) {
        $query = query($sql);
        if(mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_array($query)){
                return $row[$col];
            }                
        }
        return null;
    }
    
    /**************************************************************
        getAllTags:
        Get an array of all tags
	**************************************************************/
    function getAllTags($orderBy = "cnt DESC", $where = "") {
        $query = query("SELECT tags.tagID, tags.tagName, count(tag2phrase.tagID) AS cnt FROM `tags`
                        JOIN tag2phrase ON tags.tagID = tag2phrase.tagID
                        GROUP BY tag2phrase.tagID
                        ORDER BY $orderBy");
        
        $tags = array();
        while($row = mysqli_fetch_array($query)){
            $tags[$row["tagID"]] = array("name" => $row["tagName"], "count" => $row["cnt"], "color" => countToColor($row["cnt"]));
        }
        return $tags;
    }
    
    /**************************************************************
        getTagsByName:
        Get an array of all tags
	**************************************************************/
    function getTagsByName($tagName) {
        $query = query("SELECT * FROM tags
                        WHERE tagName LIKE '$tagName%' OR tagName LIKE '% $tagName%'");
        
        $tags = array();
        while($row = mysqli_fetch_array($query)){
            array_push($tags, array("tid" => $row["tagID"], "name" => $row["tagName"]));
        }
        return $tags;
    }
    
    /**************************************************************
        countToColor:
        Convert tag counter to color
	**************************************************************/
    function countToColor($count) {
        return getValueFromDB("SELECT * FROM heatmap WHERE max > $count ORDER BY max ASC", "color");
    }
    
    /**************************************************************
        getTagsDataForJS:
        getTagsDataForJS
	**************************************************************/
    function getTagsDataForJS($id = null) {
        $query = getTagsByPid($id); // get all tags
        
        $data = array();
        while($row = mysqli_fetch_array($query)){
            $pid = (int) $row["pID"];
            $tid = (int) $row["tagID"];
            $name = $row["tagName"];
            $approved = intToBool($row["approved"]);
            if(!array_key_exists($tid, $data)){
                $data[$tid] = array("name" => $name, "approved" => $approved, "pids" => array($pid));
            } else {
                array_push($data[$tid]["pids"], $pid);
            }
        }
        return $data;
    }
    
    /**************************************************************
        cleanWords:
        cleanWords
	**************************************************************/
    function cleanWords($words) {
        $newArr = array();
        foreach($words as $word){
            $clean = trimmer($word);
            array_push($newArr);
        }
        return $newArr;
    }
    
    function addHistory($actionType, $entityType, $qid = null, $user = "Anonymous", $content = "", $pid = null){      
        if(!in_array($actionType, ACTION_TYPES) || !in_array($entityType, ENTITY_TYPES)) {
            die("addHistory: Wrong action/entity type");
        }
        
        $actionType = strtoupper($actionType[0]);
        $entityType = strtoupper($entityType[0]);
        
        if($pid == null) {
            $pid = "NULL";
        }
        
        if($qid == null) {
            $qid = "NULL";
        }
        
        query("INSERT INTO history (actionType, entityType, user, content, pid, qid)
               VALUES ('$actionType', '$entityType', '$user', '$content', $pid, $qid)");
        
        return;
        if($entityType == "Phrase") {
            switch($actionType){
                case "Create":
                    query("INSERT INTO history (actionType, entityType, user, newContent, relevantID)
                           VALUES ()");
                    break;
                case "Modify":
                    break;
                case "Remove":
                    break;
                default:
                    die("Unknown action type");
                    break;
            }
        }
        
        
    }

?>