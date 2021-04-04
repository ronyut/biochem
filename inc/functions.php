<?php

    /**************************************************************
		Global Vars
	**************************************************************/
	$USER = getLoggedUser();
    $ROOT_URL = getProtocol()."://$_SERVER[HTTP_HOST]"."/";
    $BASE_URL = $ROOT_URL;
    $ACTUAL_LINK = $ROOT_URL.$_SERVER["REQUEST_URI"];

    if ($SERVER_NAME == "localhost") {
        $BASE_URL .= "biochem/";
    }

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
        if(isSecureConn()){
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
        
        $i = 0;
        $query = query("SELECT * FROM phrases WHERE (answerOf = $id OR pID = $id) ORDER BY pID ASC");
        while($row = mysqli_fetch_array($query)){
            // question
            if ($i == 0) {
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

                $is_hidden = "";
                if($row["is_hidden"]) {
                    $is_hidden = "-slash";
                }

                echo "<span class='question phrase' $editable pid='".$row['pID']."'
                        hash='".md5($row["phraseName"])."' >".$name."</span></h1>
                        </div>
                        <div class='col-md-1' style='text-align: center;'>
                            <div class='for-editors'>
                                <i title='הסתר/חשוף שאלה' toggle='".$row['is_hidden']."' class='fas fa-eye".$is_hidden." toggle-question-visibility'></i>
                            </div>
                        </div>
                        </div>
                        <hr>
                        <ol>";
                
            } else {
                if ($row['is_hidden'] == 1 && empty($row['phraseName'])) {
                    continue;
                }

                // answer
                $classAnswer = "incorrect";
                if ($row['isRight'] == 1) {
                    $classAnswer = "correct";
                }
                
                echo "<h2 class='$classAnswer'><li><span class='answer phrase' $editable pid='".$row['pID']."'
                      hash='".md5($row["phraseName"])."'>".$row['phraseName']."</span></li></h2>";
            }
            
			$comment = $row["comment"];
			if (!$isEditable) {
				$comment = formatComment($row["comment"]);
			}
			
            if ($row["comment"] != "" && $row['is_hidden'] == 0) {
                echo "<div class='alert alert-warning comment' $editable hash='".md5($row["comment"])."'
                        pid='".$row['pID']."' role='alert'>".$comment."</div>";
            }
                        
            $i++;
        }
        echo "</ol></article>";
    }
    
    /**************************************************************
		getOneItemJson:
		Get json of a single item
	**************************************************************/
    function getOneItemJson($id, $index, $isEditable) {
        $i = 0;
        $json = array();
        $query = query("SELECT * FROM phrases WHERE (answerOf = $id OR pID = $id) AND is_hidden = 0 ORDER BY pID ASC");
        while($row = mysqli_fetch_array($query)){  
            $json[$i]["comment"] = formatComment($row["comment"]);
            $json[$i]["pid"] = (int) $row["pID"];
            
            // question
            if ($i == 0) {
                $name = $row["phraseName"];
                $nos = array("לא", "ללא", "אינו", "אינם", "אינה", "איננו", "איננה", "חוץ");
                foreach($nos as $no){
                    if (contains($row["phraseName"], $no." ")) {
                        $name = str_replace($no." ", "<span class='red'>$no</span> ", $name);
                    }
                }
                
                $json[$i]["type"] = "q";
                
            } else {
                $name = $row["phraseName"];
                // answer
                $json[$i]["type"] = "a";

                if ($row['isRight'] == 1) {
                    $json[$i]["correct"] = true;
                } else {
                    $json[$i]["correct"] = false;
                }
            }
            $json[$i]["name"] = $name;

            $i++;
        }
        return $json;
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
            addHistory("Delete", "Tag", $text, array("qid" => $pid));
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
        return false;
    }
    
    /**************************************************************
        getAllTags:
        Get an array of all tags
	**************************************************************/
    function getAllTags($isHeat = true) {
        if($isHeat) {
            $query = query("SELECT tags.tagID, tags.tagName, count(tag2phrase.tagID) AS cnt FROM `tags`
                            JOIN tag2phrase ON tags.tagID = tag2phrase.tagID
                            GROUP BY tag2phrase.tagID
                            ORDER BY cnt DESC");
        } else {
            // by abc
            $query = query("SELECT * FROM `tags`
                            ORDER BY tagName ASC");
        }
        
        $tags = array();
        while($row = mysqli_fetch_array($query)){
            $tagID = (int) $row["tagID"];
            $temp = array("tid" => $tagID, "name" => $row["tagName"]);
            if($isHeat) {
                $temp["count"] = (int) $row["cnt"];
                $temp["color"] = countToColor($row["cnt"]);
            }
            array_push($tags, $temp);
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
    
    /**************************************************************
        addHistory:
        addHistory
	**************************************************************/
    function addHistory($actionType, $entityType, $content = "", $ids = array()){      
        global $USER;
        
        if(!in_array($actionType, ACTION_TYPES) || !in_array($entityType, ENTITY_TYPES)) {
            die("addHistory: Wrong action/entity type: ". $actionType . " - ". $entityType);
        }
        
        $userID = 0;
        if($USER) {
            $userID = $USER["userID"];
        }
        
        $actionType = strtoupper($actionType[0]);
        $entityType = strtoupper($entityType[0]);
        
        $pid = "NULL";
        if(isset($ids["pid"])) {
            $pid = $ids["pid"];
        }
        
        $qid = "NULL";
        if(isset($ids["qid"])) {
            $qid = $ids["qid"];
            if ($qid == $pid && $entityType != "C") {
                $entityType = "Q";
            }
        }
        
        
        query("INSERT INTO history (actionType, entityType, userID, content, pid, qid)
               VALUES ('$actionType', '$entityType', '$userID', '$content', $pid, $qid)");
        
        return;
    }
    
    /**************************************************************
        saveNewUser:
        saveNewUser
	**************************************************************/
    function saveNewUser($data) {
        global $db;
        
        if(!empty($data['email']))
        {
            $email = escape($data['email']);
        } else {
            return null;
        }
        
        $firstName = escape($data['given_name']);
        $lastName = escape($data['family_name']);
        $photo = escape($data['picture']);
        $hash = getHashWithSalt($email);
        
        if ($userID = isUserExists($email)) {
            return getUserByID($userID)["hash"];
        }
        
        query("INSERT INTO users (firstName, lastName, email, photo, hash)
               VALUES ('$firstName', '$lastName', '$email', '$photo', '$hash')");
        
        savePhoto($photo, mysqli_insert_id($db));
        
        return $hash;
    }
    
    /**************************************************************
        isUserExists:
        isUserExists
	**************************************************************/
    function isUserExists($email) {
        return getValueFromDB("SELECT userID FROM users WHERE email = '$email'", "userID");
    }
   
    /**************************************************************
        getUserByHash:
        getUserByHash
	**************************************************************/
    function getUserByHash($hash) {
        $hash = html_entity_decode($hash);
        $output = array();
        $query = query("SELECT * FROM users WHERE hash = '$hash'");
        if(mysqli_num_rows($query) == 1) {
            while($row = mysqli_fetch_array($query)){
                $output["userID"] = (int) $row["userID"];
                $output["firstName"] = $row["firstName"];
                $output["lastName"] = $row["lastName"];
                $output["email"] = $row["email"];
                $output["photo"] = $row["photo"];
                $output["isEditor"] = (int) $row["isEditor"];
                $output["hash"] = $row["hash"];
                $output["banned"] = $row["banned"];
                return $output;
            }                
        }
        return null;
    }
    
    /**************************************************************
        getUserByID:
        getUserByID
	**************************************************************/
    function getUserByID($userID) {
        if ($userID == null) {
            return false;
        }
        
        $hash = getValueFromDB("SELECT hash FROM users WHERE userID = '$userID'", "hash");
        if($hash === false) {
            return false;
        }
        
        return getUserByHash($hash);
    }
    
    /**************************************************************
        getHash:
        getHash
	**************************************************************/
    function getHashWithSalt($email) {
        $SALT = "$#W87hGFXC)_O&^RTFLKMGHVFDX$%SE09i;lm,GHV45esL:09iHYUJVG".time();
        return password_hash($email.$salt, PASSWORD_ARGON2I);
    }
    
    /**************************************************************
        getLoggedUser:
        getLoggedUser
	**************************************************************/
    function getLoggedUser($die = false) {
        if (!isset($_COOKIE[COOKIE_HASH_NAME])) {
            $user = getUserByID(0); // anonymous
        } else {
            $user = getUserByHash($_COOKIE[COOKIE_HASH_NAME]);
        }
        
        if($user != null && $user["banned"] == 0) {
            return $user;
        } else {
            return false;
        }
    }
    
    function makeFullName($user) {
        $fullname = $user["firstName"];
        if (!empty($user["firstName"]) && !empty($user["lastName"])) {
            $fullname .= " ".$user["lastName"];
        } else if (empty($user["firstName"])) {
            $fullname = $user["email"];
        }
        return $fullname;
        
    }
    
    function purifyHtml($input) {
        require_once 'html-purifier/HTMLPurifier.auto.php';
        $config = HTMLPurifier_Config::createDefault();
        $config->set('AutoFormat.RemoveEmpty', true); // remove empty elements
        $config->set('AutoFormat.RemoveSpansWithoutAttributes', true); // remove empty spans
        //$config->set('CSS.Trusted', true); // trust user
        $config->set('CSS.Proprietary', true); // allow safe, proprietary CSS values.
        $config->set('CSS.AllowedProperties', array('color', 'direction', 'vertical-align', 'border', 'background',
                                                    'background-color', 'font-weight', 'text-decoration', 'font-size', 'background-image', 'width', 'height', 'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'padding', 'border-color', 'border-width', 'border-style', 'border-radius', 'font', 'font-family', 'font-style', 'text-align')); // remove all CSS
        $config->set('HTML.AllowedElements', array('p', 'div', 'a', 'br', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'ul',                                           'ol', 'li', 'b', 'i', 'span', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                                                   'hr', 'dl', 'dt', 'dd', 'del', 'video', 'audio'));
        //$config->set('HTML.AllowedAttributes', array('a.href', 'img.src')); // remove all attributes except a.href
        $purifier = new HTMLPurifier($config);
        $clean_html = $purifier->purify($input);
        $talkback = trimmer(escape($clean_html));
        return $talkback;
    }
    
    /*
        savePhoto:
        Save user google photo on server
    */
    function savePhoto($url, $userID) {
        $file = fopen('../img/users/profiles/'.$userID.'.jpg', 'w');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_FILE           => $file,
            CURLOPT_TIMEOUT        => 5,
            //CURLOPT_COOKIEJAR      => 'cookie.txt',
            CURLOPT_SSL_VERIFYPEER => 0
        ]);

        $response = curl_exec($curl);
        if ($response !== false) {
            //query("UPDATE users SET photo = '' WHERE userID = '$userID'");
        }
    }

	/*
		formatComment:
		replace @{qid} with a link to the question
	*/
	function formatComment($comment) {
		$comment = preg_replace("/@(\d+)/", "<a href='item.php?id=$1'>$1</a>", $comment);
        return $comment;
	}

?>