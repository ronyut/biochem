<?php

require("inc/config-google.php");

$login_url = '';

//This $_GET["code"] variable value received after user has login into their Google Account redirct to PHP script then this variable value has been received
if(isset($_GET["code"]))
{
    //It will Attempt to exchange a code for a valid authentication token.
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

    //This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
    if(!isset($token['error']))
    {
        //Set the access token used for requests
        $google_client->setAccessToken($token['access_token']);

        //Store "access_token" value in $_SESSION variable for future use.
        //$_SESSION['access_token'] = $token['access_token'];

        //Create Object of Google Service OAuth 2 class
        $google_service = new Google_Service_Oauth2($google_client);

        //Get user profile data from google
        $data = $google_service->userinfo->get();

        $response = saveNewUser($data);
        if ($response != null) {
            header("Location: index.php");
            setcookie(COOKIE_HASH_NAME, $response, time() + 3600 * 24 * 365, "/");
        } else {
            header("Location: ".PAGE_NOT_FOUND);
        }
    }
}

if(isset($_GET["login"])) {
	$hash = $_GET["login"];
	setcookie(COOKIE_HASH_NAME, $hash, time() + 3600 * 24 * 365 * 10, "/");
	header("Location: index.php");
	exit();
}

// This is for check user has login into system by using Google account, if User not login into system then it will execute if block of code and make code for display Login link for Login using Google account.
$logged = true;
if(!isset($_COOKIE[COOKIE_HASH_NAME]))
{
 //Create a URL to obtain user authorization
 $login_url = $google_client->createAuthUrl();
 $user_image = "img/user.png";
 $logged = false;
} else {
    $user = getLoggedUser();
	if (!$user) {
		// remove cookie
		setcookie(COOKIE_HASH_NAME, "", time() - time(), "/");
		unset($user);
		header("Location: index.php");
	}
    $user_image = $user["photo"];
}

$editable = (isset($_GET["editable"]) && $_GET["editable"] == "true") || (isset($user) && $user["isEditor"]);

$isEditable = "false";
if($editable){
    $isEditable = "true";
}

if (!isset($pageTitle)) {
    $pageTitle = "השחזורון - ביוכימיה | בר אילן";
}

if(isset($styleCSS)) {
    $styleCSS = '<link rel="stylesheet" href="css/'.$styleCSS.'">';
} else {
    $styleCSS = "";
}

if(!isset($is_index)) {
    $is_index = "hidden";
}

if(!isset($showNavBar)) {
    $showNavBar = "";
}

?>
<html lang="he" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="description" content='שחזורים בקורס בביוכימיה (27202) של ד"ר יוליה פנסו מכל השנים של מדעי המוח, בר אילן'>
	<meta property="og:title" content="השחזורון בביוכימיה - מדעי המוח בר אילן" />
	<meta property="og:url" content="<?=$BASE_URL?>" />
	<meta property="og:description" content='שחזורים בקורס בביוכימיה (27202) של ד"ר יוליה פנסו מכל השנים של מדעי המוח, בר אילן'>
	<meta property="og:image" content="<?=$BASE_URL?>img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
    <?=$styleCSS?>

    <style>
    .dropdown-menu {
        text-align: right;
        margin: .125rem -80 0;
    }

    .dropdown-user {
        text-align: right;
    }

    .dropdown-toggle::after {
        display: block;
    }

    nav img {
        border: none !important;
        background: none !important;
    }

    .correct
    {
        color: green;
    }

    .incorrect
    {
        color: grey;
    }

    .red
    {
        color: red;
        font-weight: bold;
    }

    .hidden {
        display: none;
    }

    <?php if ($isEditable == "true") { ?>
    .for-editors {
        display: block;
    }
    <?php } else { ?>
    .for-editors {
        display: none;
    } 
    <?php } ?>
    </style>

    <title><?=$pageTitle?></title>

</head>

<body>
    <!-- navbar -->
    <nav class="<?=$showNavBar?> navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="<?=$BASE_URL?>">השחזורון <img src="img/favicon.png" border="0" width="40" height="40"></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="<?=$BASE_URL?>">שחזורים</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="changes.php">שינויים אחרונים <i class="fas fa-history"></i></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="addItem.php">הוספת שאלה <i class="fas fa-plus"></i></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="print.php">גרסת הדפסה <i class="fas fa-print"></i></a>
          </li>
        </ul>
        <div class="navbar-nav" style="margin-right: auto;">
              <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="<?=$user_image?>" width="40" height="40" class="img-responsive img-circle img-thumbnail">
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-user">
                    <?php if ($logged) { ?>
                    <a href="inc/makeJson/jsoner.php" target="_blank" class="dropdown-item"><i class="fas fa-sync"></i> רענון אינדקסים</a>
                    <?php } ?>
					<a href="index.php?heat" class="dropdown-item"><i class="fas fa-thermometer-half"></i> תצוגה חמה</a>
                    <a href="print.php" class="dropdown-item"><i class="fas fa-print"></i> גרסת הדפסה</a>
                    <!--<a href="#" class="dropdown-item"><i class="fas fa-grimace"></i> בחן את עצמך</a>-->
                    <a href="help.php" class="dropdown-item"><i class="fas fa-question-circle"></i> עזרה</a>
                    <!--<a href="#" class="dropdown-item"><i class="fas fa-signature"></i> אודות</a>-->
                    <div class="dropdown-divider"></div>
                    <?php if ($logged) { ?>
                    <a href="#"class="dropdown-item" role="logout"><i class="fas fa-sign-out-alt"></i> התנתק</a>
                    <?php } else { ?>
                    <a href="<?=$login_url?>" class="dropdown-item"><i class="fas fa-sign-in-alt"></i> התחבר</a>
                    <?php } ?>
                </div>
              </div>

            <?php
            if (!isset($showSearchForm)) {
                $showSearchForm = false;
            }

            if ($showSearchForm) { ?>
            <form id="myAlgoForm" class="form-inline mt-2 dropdown <?=$is_index?>">
                <input class="myAlgo form-control mr-sm-2" type="text" id="myAlgo" placeholder="חיפוש בזק" aria-label="Search" autocomplete="off">
                <button class="btn btn-outline-success my-2 my-sm-0" style="margin-right: 10px !important;" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right dropdown-results">
                    <div class="myAlgo-results"></div>
                    <div class="dropdown-divider"></div>
                    <button type="reset" onclick="clearMyAlgoResults()" class="btn btn-default">נקה חיפוש</button>
                </div>
            </form>
            <?php } ?>
        </div>
      </div>
    </nav>
    
    <!-- scroll to top -->
    <?php
    if (!isset($showBackToTopButton)) {
        $showBackToTopButton = false;
    }

    if ($showBackToTopButton) { ?>
    <a href="#" class="btn btn-light btn-lg back-to-top" role="button">
        <i class="fas fa-chevron-up"></i>
    </a>
    <?php } ?>