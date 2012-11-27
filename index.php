<!DOCTYPE html>
<html>
<head>
	<title>Welcome to CARenderia Atbp.</title>
	
	<link rel="stylesheet" type="text/css" href="css/layout.css"/>
	<link rel="stylesheet" type="text/css" href="css/slideshow.css"/>
	<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.21.custom.css"/>
	
	<script type="text/javascript" src="js/jquery-1.7.2.min.js" ></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js" ></script>
	<script type="text/javascript" src="js/jquery.validate.js" ></script>
	<script type="text/javascript" src="js/infowindow.js" ></script>
	<script type="text/javascript" src="js/incredisoft-services.js" ></script>
	
</head>
<body >
	<div id ="header_bk"> 
		<div id ="footer_bk"> 
			<div id="wrapper">
				<?php 
					include("template/header.php");
					$page = (isset($_GET['page']))?$_GET['page']:'home';
					include("template/$page.php");
					include("template/footer.php");
				?>
			</div>
		</div>
	</div>
</body>
</html>