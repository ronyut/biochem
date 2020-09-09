<?php

require("config.php");
require("functions.php");

$tags = getAllTags();
$ids = array_keys($tags);
$names = array_column($tags, 'name');
$tags = array_combine($ids, $names);

$output = array();
foreach($tags as $id => $name) {
   array_push($output, array("tid" => $id, "name" => $name));
}

header('Content-Type: application/json');
echo json_encode($output);
?>