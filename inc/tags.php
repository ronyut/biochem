<?php

require("config.php");
require("functions.php");

if(!isset($_GET['tag'])) {
    exit();
}

$query = trimmer(escape($_GET['tag']));

if(empty($query)) {
    die("Empty query");
}

$tags = getTagsByName($query);

header('Content-Type: application/json');
echo json_encode($tags);
?>