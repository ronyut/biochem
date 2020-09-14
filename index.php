<?php

require("inc/config.php");
require("inc/functions.php");
require("inc/config-google.php");

$login_button = '';

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
  $_SESSION['access_token'] = $token['access_token'];

  //Create Object of Google Service OAuth 2 class
  $google_service = new Google_Service_Oauth2($google_client);

  //Get user profile data from google
  $data = $google_service->userinfo->get();

  //Below you can find Get profile data and store into $_SESSION variable
  if(!empty($data['given_name']))
  {
   $_SESSION['user_first_name'] = $data['given_name'];
  }

  if(!empty($data['family_name']))
  {
   $_SESSION['user_last_name'] = $data['family_name'];
  }

  if(!empty($data['email']))
  {
   $_SESSION['user_email_address'] = $data['email'];
  }

  if(!empty($data['gender']))
  {
   $_SESSION['user_gender'] = $data['gender'];
  }

  if(!empty($data['picture']))
  {
   $_SESSION['user_image'] = $data['picture'];
  } else {
      $_SESSION['user_image'] = "img/user.png";
  }
  header("Location: index.php");
 }
}

// This is for check user has login into system by using Google account, if User not login into system then it will execute if block of code and make code for display Login link for Login using Google account.
$logged = true;
if(!isset($_SESSION['access_token']))
{
 //Create a URL to obtain user authorization
 $login_button = $google_client->createAuthUrl();
 $_SESSION['user_image'] = "img/user.png";
 $logged = false;
}

$user_image = $_SESSION['user_image'];

?>
<html lang="he">
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">

    <title>ביוכימיה - שחזורים</title>

</head>

<body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="#">ביוכימיה <img src="img/favicon.png" border="0" width="40" height="40"></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="#">שחזורים</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">שינויים אחרונים <i class="fas fa-history"></i></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">הוספת שאלה +</a>
          </li>
        </ul>
        <div class="navbar-nav" style="margin-right: auto;">
              <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="<?=$user_image?>" width="40" height="40" class="img-responsive img-circle img-thumbnail">
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-user">
                    <?php if ($logged) { ?>
                    <a href="#" class="dropdown-item"><i class="fas fa-user-cog"></i> הגדרות</a>
                    <?php } ?>
                    <a href="#" class="dropdown-item"><i class="fas fa-print"></i> גרסת הדפסה</a>
                    <a href="#" class="dropdown-item"><i class="fas fa-grimace"></i> בחן את עצמך</a>
                    <a href="#" class="dropdown-item"><i class="fas fa-question-circle"></i> עזרה</a>
                    <a href="#" class="dropdown-item"><i class="fas fa-signature"></i> אודות</a>
                    <div class="dropdown-divider"></div>
                    <?php if ($logged) { ?>
                    <a href="#"class="dropdown-item" role="logout"><i class="fas fa-sign-out-alt"></i> התנתק</a>
                    <?php } else { ?>
                    <a href="<?=$login_button?>" class="dropdown-item"><i class="fas fa-sign-in-alt"></i> התחבר</a>
                    <?php } ?>
                </div>
              </div>
              
            <form id="myAlgoForm" class="form-inline mt-2 dropdown">
                <input class="myAlgo form-control mr-sm-2" type="text" id="myAlgo" placeholder="שאל אותי שאלה" aria-label="Search" autocomplete="off">
                <button class="btn btn-outline-success my-2 my-sm-0" style="margin-right: 10px !important;" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right dropdown-results">
                    <div class="myAlgo-results"></div>
                    <div class="dropdown-divider"></div>
                    <button type="reset" onclick="clearMyAlgoResults()" class="btn btn-default">נקה חיפוש</button>
                </div>
            </form>
        </div>
      </div>
    </nav>
    
    <!-- scroll to top -->
    <a href="#" class="btn btn-light btn-lg back-to-top" role="button">
        <i class="fas fa-chevron-up"></i>
    </a>
    
    <div class="page-wrapper">
    <?php if (!isset($_GET['titles'])) { ?>
    <div align="center" class="tags-search-wrapper">
    <div class="tags-search">
    <?php
        if(!isset($_GET['heat'])) {
            $tags = getAllTags("tags.tagName ASC");
        } else {
            $tags = getAllTags();
        }
        $i = 0;
        foreach($tags as $tag => $details) {
            if(isset($_GET['heat'])){
                echo "<button dir='auto' type='button' class='btn tag-link'
                        style='background-color:".$details['color']."' tid='".$tag."'>".$details['name']."</button>";
            } else {
                echo "<button dir='auto' type='button' class='btn tag-link'
                    style='background-color:rgb(0,255,".(235 - $i*2).")' tid='".$tag."'>".$details['name']."</button>";
            }
            $i++;
        }
    ?>
    </div>
    <div id="cnt-visible-wrapper"><span id="cnt_visible"></span> תוצאות</div>
    <br>
    </div>
    <?php
    }
    
    $i = 1;
    $query0 = query("SELECT * FROM phrases WHERE isQuestion = 1");
    while($row0 = mysqli_fetch_array($query0)){
        if (isset($_GET['titles'])) {
            echo "<a href='item.php?id=".$row0['pID']."'>".$row0['phraseName']."</a><hr>";
        }
        else
        {
            $id = $row0['pID'];
            echo "<div class='article-container' pid='".$id."' show='true'>";
            showItem($id, $i, isset($_GET["editable"]));
            //$tags = getTagsByPid($id);
            //$tagsStr = implode(",", array_values($tags));
            ?>
                <div class="tags-container">
                    <input class="tags-input" pid="<?=$id?>" type="text" data-role="tagsinput">
                </div>
            </div>
            <?php
        }
        $i++;
    }
    ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-tagsinput.js"></script>
    <script src="js/typeahead.bundle.js"></script>
    <script src="js/jquery.md5.js"></script>
    <script src="js/main.js"></script>
    <?php if(isset($_GET["editable"])){ ?>
    <script src="js/editor.js"></script>
    <?php } ?>
    <script>
    // Enable dragging of search window
    $(".draggable").draggable();
    </script>
</body>
</html>