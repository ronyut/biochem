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

$links = array(
				array(
					  "title" => "שינויים אחרונים",
					  "link" => "changes.php",
					  "icon" => "history",
					  "is_nav" => true,
					 ),
				array(
					  "title" => "הוספת שאלה",
					  "link" => "addItem.php",
					  "icon" => "plus",
					  "is_nav" => true
					 ),
				array(
					  "title" => "גרסת הדפסה",
					  "link" => "print.php",
					  "icon" => "print",
					  "is_nav" => true
					 ),
				array(
					  "title" => "רענון אינדקסים",
					  "link" => "inc/makeJson/jsoner.php",
					  "icon" => "sync",
					  "is_logged" => true,
					  "target_blank" => true
					 ),
				array(
					  "title" => "תצוגה חמה",
					  "link" => "index.php?heat",
					  "icon" => "thermometer-half"
					 ),
				array(
					  "title" => "עזרה",
					  "link" => "help.php",
					  "icon" => "question-circle"
					 ),
				array(
					  "title" => "אודות",
					  "link" => "https://drive.google.com/file/d/19jdYj2MpBxosb8ygppVr1w4MyfTqA75N/view?usp=sharing",
					  "icon" => "signature",
					  "target_blank" => true
					 ),
				array(
					  "title" => "התנתק",
					  "link" => "#",
					  "icon" => "sign-out-alt",
					  "is_logged" => true,
					  "role" => "logout"
					 ),
				array(
					  "title" => "התחבר",
					  "link" => $login_url,
					  "icon" => "sign-in-alt",
					  "is_logged" => false
					 )
			  );

?>
<!DOCTYPE html>
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

	@media (min-width: 768px) {
		.dropdown-menu {
			text-align:right;
			margin-right: -120%;
		}
	}
	
	.dropdown-menu {
		text-align:right;
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
			<?php
			foreach($links as $link) {
				if (!isset($link["is_nav"])) { continue; }
				if (isset($link["is_logged"]) && $link["is_logged"] && !$logged) { continue; }
				if (isset($link["is_logged"]) && !$link["is_logged"] && $logged) { continue; }
				
			?>
			<li class="nav-item">
				<a class="nav-link" href="<?=$link["link"]?>"><?=$link["title"]?> <i class="fas fa-<?=$link["icon"]?>"></i></a>
			</li>
			<?php } ?>
        </ul>
        <div class="navbar-nav" style="margin-right: auto;">
              <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="<?=$user_image?>" width="40" height="40" class="img-responsive img-circle img-thumbnail">
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-user">
					<?php
					foreach($links as $link) {
						if (isset($link["is_nav"])) { continue; }
						if (isset($link["is_logged"]) && $link["is_logged"] && !$logged) { continue; }
						if (isset($link["is_logged"]) && !$link["is_logged"] && $logged) { continue; }
						
					?>
					<a href="<?=$link["link"]?>" 
							<?php echo isset($link["role"]) ? 'role="'.$link["role"].'"' : "" ?>
							<?php echo isset($link["target_blank"]) ? 'target="blank"' : "" ?>
							class="dropdown-item"
							>
							<i class="fas fa-<?=$link["icon"]?>"></i> <?=$link["title"]?></a>
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