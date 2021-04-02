<?php
    
   /************************************************
		Global variables and constants
	************************************************/
    
    const FORWARD_HTTPS = false;
    const DB_NAME = "biochem";
    const PAGE_NOT_FOUND = "404.php";
    const COOKIE_HASH_NAME = "biochem_user_hash";
    const ACTION_TYPES = ["Add", "Edit", "Delete", "Hide", "Show"];
    const ENTITY_TYPES = ["Answer", "Tag", "Phrase", "User", "Question", "Comment"];
    $SERVER_NAME = $_SERVER['SERVER_NAME'];

    const HOST_MAP = array(
            "rony1" => "3451754",
            "rony3" => "3451771",
            "rony6" => "3451776",
            "rony7" => "3456555",
            "rony9" => "3456571",
            "rony10" => "3456576",
			"biochem" => "3792380" // r.onyu.t@gmail.com
        );
        
    /************************************************
		Redirect Http to Https
	************************************************/
    if (FORWARD_HTTPS && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") && $SERVER_NAME != "localhost") {
        $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $location);
        exit;
    }
    
    /************************************************
		MySQL Connect
	************************************************/
	$local_names = array("localhost");

	if (in_array($SERVER_NAME, $local_names) || preg_match('#192\.168\.2\.#', $SERVER_NAME)) {
		    $db = 	mysqli_connect("localhost", "root", "") or die(mysqli_error($db));
            mysqli_select_db($db, DB_NAME) or die(mysqli_error($db));
            
            error_reporting(E_ALL ^ E_DEPRECATED);
	}
    elseif (preg_match('#tikun\.li#', $SERVER_NAME)) {
              
		$db = 	mysqli_connect("localhost", "tikunli_rony", "o545535656") or die(mysqli_error($db));
				mysqli_select_db($db, "tikunli_biochem") or die(mysqli_error($db));
	}
	elseif (preg_match('#atwebpages\.com#', $SERVER_NAME)) {
              
        $hostname_prefix = strtok($SERVER_NAME, '.');           
        $db_uname = HOST_MAP[$hostname_prefix] . "_". DB_NAME;

		$db = 	mysqli_connect("fdb29.awardspace.net", $db_uname, "fBH*7JiQ3VmESCh", $db_uname, 3306) or die(mysqli_error($db));
	}
    elseif (preg_match('#000webhostapp\.com#', $SERVER_NAME)) {
              
        $db_uname = "id16487599_biochem";
        $db_name = "id16487599_biochem_000";

		$db = 	mysqli_connect("localhost", $db_uname, "fBH*7JiQ3VmESCh", $db_name, 3306) or die(mysqli_error($db));
	}
	elseif (preg_match('#42web\.io#', $SERVER_NAME)) {
              
        $db_uname = "epiz_28265250";
        $db_name = "epiz_28265250_biochem";

		$db = 	mysqli_connect("sql306.epizy.com", $db_uname, "ehofO9q0vB8", $db_name, 3306) or die(mysqli_error($db));
	}	else {
		die("No DB for this server");
	}
    
    mysqli_query($db, "SET NAMES 'UTF8'");

?>