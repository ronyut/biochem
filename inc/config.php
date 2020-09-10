<?php
    
    $dbname = "biochem";
    $hostMap = array(
        "rony1" => "3451754",
        "rony3" => "3451771",
        "rony6" => "3451776",
        "rony7" => "3456555",
        "rony9" => "3456571",
        "rony10" => "3456576"
    );
    
    /************************************************
		MySQL Connect
	************************************************/
	$local_names = array("localhost");

	if (in_array($_SERVER['SERVER_NAME'], $local_names) || preg_match('#192\.168\.2\.#', $_SERVER['SERVER_NAME'])) {
		    $db = 	mysqli_connect("localhost", "root", "") or die(mysqli_error($db));
            mysqli_select_db($db, $dbname) or die(mysqli_error($db));
            mysqli_query($db, "SET NAMES 'UTF8'");
		
		error_reporting(E_ALL ^ E_DEPRECATED);
	}
    elseif (preg_match('#tikun\.li#', $_SERVER['SERVER_NAME'])) {
              
		$db = 	mysqli_connect("localhost", "tikunli_rony", "o545535656") or die(mysqli_error($db));
				mysqli_select_db($db, "tikunli_biochem") or die(mysqli_error($db));
				mysqli_query($db, "SET NAMES 'UTF8'");

	}
	elseif (preg_match('#atwebpages\.com#', $_SERVER['SERVER_NAME'])) {
              
        $hostname_prefix = strtok($_SERVER['SERVER_NAME'], '.');           
        $db_uname = $hostMap[$hostname_prefix] . "_". $dbname;

		$db = 	mysqli_connect("fdb25.awardspace.net", $db_uname, "fBH*7JiQ3VmESCh", $db_uname, 3306) or die(mysqli_error($db));
				mysqli_query($db, "SET NAMES 'UTF8'");

	} else {
		die("No DB for this server");
	}

?>