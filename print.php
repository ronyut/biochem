<?php

$styleCSS = "index.css";
$showNavBar = "hidden";

require("inc/config.php");
require("inc/functions.php");
require("inc/header.php");

$order = "titles";
if(isset($_GET["order"]) && $_GET['order'] == "time") {
	$order = "time";
}
  
?>
	<style>
	#articles{
		-moz-column-count: 3;
		-moz-column-gap: 0px;
		-webkit-column-count: 3;
		-webkit-column-gap: 0px;
		column-count: 3;
		column-gap: 0px;
	}
	
	.article-container {
		border: 1px solid black;
		padding: 10px;
		break-inside: avoid;
	}
	
	article h1 {
		font-size: 22px;
	}
	
	article h2 {
		font-size: 16px;
	}
	
	body {
		padding: 50px 50px;
	}
	
	.alert {
		padding: .25rem 0.25rem;
		margin-bottom: 0.3rem;
		margin-top: -5px;
	}
	
	.alert-warning {
		font-size: 12px;
	}
	
	@media print {
		.no-print {
			display: none;
		}
	}
	
	#settings {
		display: none;
	}
	
	.tags-search-wrapper {
		margin-bottom: 40px;
	}
	
	.red.bw, .correct.bw {
		color: black !important;
		text-decoration: underline;
	}
	
	.tag-title h1 {
		text-align:center;
	}
	
	.mr-30{
		margin-right: 30px;
		display: inline;
	}
	</style>
	
    <div class="page-wrapper">
        <div align="center" class="tags-search-wrapper no-print">
            <div id="loader" class="tags-search">
                <img src="img/flask.gif" width="150" height="150" border="0">
				<div id="cnt-visible-wrapper">טוען שאלות</div><br>
            </div>
        
			<div id="settings">
				<button class="btn btn-primary" onclick="printPage('bw');">גרסה למדפסת שחור-לבן</button>
				<button class="btn btn-primary" onclick="printPage('rgb');">גרסה למדפסת צבעונית</button>
				<button class="btn btn-success" onclick="window.print();">הדפס</button>
				<br><br>
				<select onchange="window.location='print.php?order='+this.value" style="width:250px" class="form-control" aria-label="סדר שאלות לפי">
					<option value="titles" <?php echo $order == "titles" ? "selected" : "" ?>>סדר לפי כותרות</option>
					<option value="time" <?php echo $order == "time" ? "selected" : "" ?>>סדר הוספה (כמו בדף הראשי)</option>
				</select>
				<br>
				<input class="form-check-input" type="checkbox" value="1" id="hideComments" onchange="toggleComments(this)">
				<label class="form-check-label" for="hideComments">    הסתר הערות</label>
				<div class="mr-30">
					<input class="form-check-input" type="checkbox" value="1" id="hideNumbering" onchange="toggleNumbering(this)">
					<label class="form-check-label" for="hideNumbering">    הסתר מספור</label>
				</div>
			</div>
		</div>
        <div id="articles"></div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-tagsinput.js"></script>
    <script src="js/typeahead.bundle.js"></script>
	<script src="js/jquery.md5.js"></script>
	<script src="js/main.js"></script>
	<script>
	let order = "<?=$order?>";
    if (order == "time") {
		getPrintableQuestions(order);
	} else {
		getTitles();
	}
    </script>
</body>
</html>