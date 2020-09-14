<?php
    
   /************************************************
		Global variables and constants
	************************************************/
    
    const DB_NAME = "biochem";
    const PAGE_NOT_FOUND = "404.php";
    const ACTION_TYPES = ["Add", "Edit", "Delete"];
    const ENTITY_TYPES = ["Answer", "Tag", "Phrase", "User"];
    $SERVER_NAME = $_SERVER['SERVER_NAME'];
    
    const HOST_MAP = array(
            "rony1" => "3451754",
            "rony3" => "3451771",
            "rony6" => "3451776",
            "rony7" => "3456555",
            "rony9" => "3456571",
            "rony10" => "3456576"
        );
        
    /************************************************
		Redirect Http to Https
	************************************************/
    if ((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") && $SERVER_NAME != "localhost") {
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

		$db = 	mysqli_connect("fdb25.awardspace.net", $db_uname, "fBH*7JiQ3VmESCh", $db_uname, 3306) or die(mysqli_error($db));
	} else {
		die("No DB for this server");
	}
    
    mysqli_query($db, "SET NAMES 'UTF8'");

?>