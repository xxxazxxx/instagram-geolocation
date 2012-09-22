<?php
$client_id = ""; # you instagram client ID
$distance = 500; // max: 5000 = 5km
$image_type = "standard_resolution"; # low_resolution (306px), thumbnail (150px), standard_resolution(612px)
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="jquery.inview.js"></script>
<script type="text/javascript">
var lat = 0;
var lng = 0;
var max_timestamp = <?php time(); ?>
$(document).ready(function(){
	var loading = "";
	
	function startLoading(){
		loading = setInterval(function(){$('#loading').fadeOut(500,function(){$(this).fadeIn(500);});}, 1000);
		$('#loading').bind('inview', function (event, visible) {
			if (visible == true) {
				$('#loading').unbind();
				startAPICall();
			}
		});
	}
	
	startLoading();
	$('#loading').unbind();
	startAPICall();
	
	function startAPICall(){
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(success, error);
		} else {
			$('#loading').empty().append('<b>Fehler:</b> Aktuelle Geolocation konnte nicht ermittelt werden :(');
		}
		
		function success(position) {
			
			lat = position.coords.latitude;
			lng = position.coords.longitude;
			
			var url = "https://api.instagram.com/v1/media/search?client_id=<?php echo $client_id; ?>&lat="+lat+"&lng="+lng+"&distance=<?php echo $distance; ?>&max_timestamp="+max_timestamp;
			
			$.ajax({
				type: "GET",
				dataType: "jsonp",
				cache: false,
				url: url,
				statusCode: {
					200: function(data){
						window.clearInterval(loading);
						$('#loading').empty().append('Weitere Bilder laden …');
						
						startLoading();
						
						for (var i = 0; i < 10; i++) {
							$("#pics").append("<a target='_blank' href='" + data.data[i].link + "'><img src='" + data.data[i].images.<?php echo $image_type; ?>.url + "'></img></a><br>");
							
							max_timestamp = data.data[i].created_time;
						}
					},
				    404: function() {
				    	window.clearInterval(loading);
				    	$('#loading').empty().append('<b>Fehler:</b> Serverfehler :(');
				    },
				    500: function() {
				    	window.clearInterval(loading);
				    	$('#loading').empty().append('<b>Fehler:</b> Serverfehler :(');
				    }
				}
		   });
		}
		
		function error(msg) {
			window.clearInterval(loading);
			$('#loading').empty().append('<b>Fehler:</b> Aktuelle Geolocation konnte nicht ermittelt werden :(');
			
		}
	}
	
});
</script>
<style>
body {
	text-align: center;
}
#loading {
	margin: 40px 0;
}
</style>
</head>
<body>
<h1>Instagr.am Fotos aus einem Umkreis von <?php echo $distance; ?>m</h1>
<noscript>(Aktiviere JavaScript!)</noscript>
<div id="pics"></div>
<div id="loading">Bitte warten … Lade …</div>
</body>
</html>