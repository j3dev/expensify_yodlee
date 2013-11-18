<?php $assets = "assets";  ?> 

<!DOCTYPE HTML>
<html>
<head>
	<title>Jake Gibson Expensify</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
	<link rel="stylesheet" type="text/css" href="<?php echo $assets ?>/style/ivory.css" media="all">
	<link rel="stylesheet" type="text/css" href="<?php echo $assets ?>/style/style.css" media="screen">
	<!-- For Date picker only --> 
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
	<script src="<?php echo $assets ?>/js/helpers.js"></script>
	<script src="<?php echo $assets ?>/js/router.js"></script>
	<script src="<?php echo $assets ?>/js/action.js"></script>
	<script src="<?php echo $assets ?>/js/client.js"></script>
	

<body onhashchange="router()">

	<div id="topSection">
		<div class="row space-bot">

			<div  class="c4 logo">
				<a href="expensify.j3.io"> <img src="<?php echo $assets ?>/img/logo.png" /> </a>
			</div>
			
			<div class="c4 menu right" />
				
				<ul id="topMenu"></ul>

			</div>

		</div>
	</div>


	<div id="midSection">

		<div class="g1024 space-bot">

		<!-- AJAX content loader section -->
			<div id="msgArea"></div>
			<div id="content"></div> 
			

		</div>
	</div>


	<div id="footerSection">
		<div class="g1024 space-bot">
			
			<div class="row space-bot">
				<div class="c6 first">
					<p>Developed by <a href="mailto:jake@j3.io">Jake Gibson</a> </p>
				</div>
				<div class="c6 last text-right">
					<p>Expensify TEST</p>
				</div>				
			</div>

		</div>
	</div>

	
</body>
</html>

