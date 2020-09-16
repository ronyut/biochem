<?php

require("config.php");
require("functions.php");
header ('Content-Type: image/png');

if(!isset($_GET['userID'])) {
    $id = 0; // anonymous
    $prefix = $BASE_PATH."/";
} else {
    $id = (int) $_GET['userID'];
    $prefix = "";
}

$user = getUserByID($id);
if($user == false) {
    die("no such user id");
}
$photo = $prefix.$user["photo"];

echo file_get_contents($photo);

?>