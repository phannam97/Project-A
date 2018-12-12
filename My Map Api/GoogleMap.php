<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBRQI_U0TDwBKUXkVZn0f1YP61u1SQo7-Y&sensor=false&language=vi"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript" src="data.json"></script>
	<title>Tìm Địa Điểm Ăn Uống Xung Quanh</title>
</head>
<style type="text/css">
#google_map {
	width: 90%;
	height: 650px;
	margin-top:0px;
	margin-left:auto;
	margin-right:auto;
}
.marker-edit label{
	display:block;
	margin-bottom: 5px;
}
.marker-edit label span {
	width: 100px;
	float: left;
}
.marker-edit label input, .marker-edit label select{
	height: 24px;
}
.marker-edit label textarea{
	height: 60px;
}
.marker-edit label input,.marker-edit label textarea {
	width: 60%;
	margin:0px;
	padding-left: 5px;
	border: 1px solid ;
	border-radius: 3px;
}
h1.marker-heading{
	margin: 0px;
	padding: 0px;
	font: 18px Arial;
	border-bottom: 1px;
}
div.marker-info {
	max-width: 300px;
	margin-right: -20px;
	padding: 0px;
	margin: 10px 0px 10px 0;
}
div.marker-inner{
	padding: 5px;
}
button.save-marker, button.remove-marker{
	border: none;
	background: rgba(0, 0, 0, 0);
	padding: 0px;
	text-decoration: underline;
	margin-right: 10px;
	cursor: pointer;
}
.homeicon{
width: 10px;
height: 10px;
}

</style>
<script type="text/javascript">
	$(document).ready(function() {
		var geocoder = new google.maps.Geocoder();
		function geocodePosition(pos){
			geocoder.geocode({latLng:pos});
		}
		var map;
		var arrItems = []; 

		function load(){ 
			$.getJSON("./data.json", function (data) {			  
				$.each(data, function (index, value) {
					arrItems.push(value);       
				});
				for (var i = 0; i <arrItems.length; i++) {
					var pos=new google.maps.LatLng(arrItems[i].lat,arrItems[i].lng);
					create_marker(pos,arrItems[i].name,arrItems[i].address,true,true,true);
				}
			});
		}

		initialize();
		function initialize()
		{
			load();
			var MapOptions =
			{	
				zoom: 15,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			map = new google.maps.Map(document.getElementById("google_map"), MapOptions);
			google.maps.event.addListener(map, 'click', function(event) {
				//Edit form to be displayed with new marker
				var html = '<div class="marker-edit">'+
				'<form action="save.php" method="POST" name="SaveMarker" id="SaveMarker">'+
				'<label for="pName"><span>Place Name :</span><input type="text" name="pName" class="save_name" placeholder="Enter Title"/></label>'+
				'<label for="pAdd"><span>Address :</span><textarea name="pAdd"id="pAdd" class="save_address" placeholder="Enter Address"></textarea></label>'+
				'</form>'+
				'</div><button name="save-marker" class="save-marker">Add Marker</button>';
				//Drop a new Marker with our Edit Form
				create_marker(event.latLng, 'New Marker', html, true,true,true);
			});
			navigator.geolocation.getCurrentPosition(function(position) {

				var geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

				map.setCenter(geolocate);
				create_marker(geolocate,"My Location","",true,true);
			});

		}

		//############### Create Marker Function ##############
		function create_marker(MapPos, MapTitle, MapDesc,  InfoOpenDefault, Removable)
		{

			//Content structure of info Window for the Markers
			if(MapTitle=="My Location"){

			var contentString = $('<div  class="marker-info">'+
					'<div class="marker-inner"><span class="info-content">'+
					'<h1 class="marker-heading" align="center">'+MapTitle+'</h1>'+
					MapDesc+'<br/>'+
					'</span><button name="remove-marker" class="remove-marker" title="Remove Marker"><br/>Remove Marker</button>'+
					'</div></div>');
			
				//add home location
				var marker = new google.maps.Marker({
					position: MapPos,
					map: map,
					animation: google.maps.Animation.DROP,
					icon:'homeicon.png'
				});
			}else{
				//New Marker
				var marker = new google.maps.Marker({
					position: MapPos,
					map: map,
					animation: google.maps.Animation.DROP,
				});
				var contentString = $('<div  class="marker-info">'+
					'<div class="marker-inner"><span class="info-content">'+
					'<h1 class="marker-heading" align="center">'+MapTitle+'</h1>'+
					MapDesc+'<br/>'+
					'</span><button name="remove-marker" class="remove-marker" title="Remove Marker"><br/>Remove Marker</button>'+
					'</div></div>');
			}
			//Create an infoWindow
			var infowindow = new google.maps.InfoWindow();
			//set the content of infoWindow
			infowindow.setContent(contentString[0]);

			//Find remove button in infoWindow
			var removeBtn 	= contentString.find('button.remove-marker')[0];
			var saveBtn 	= contentString.find('button.save-marker')[0];

			//add click listner to remove marker button
			google.maps.event.addDomListener(removeBtn, "click", function(event) {				
				delete_marker(marker,MapTitle);
			});

			if(typeof saveBtn !== 'undefined'){

				//add click listner to save marker button
				google.maps.event.addDomListener(saveBtn,"click",function(event){
					//html to be replaced after success
					var marker_Replace = contentString.find('span.info-content');
					//name input field value
					var marker_Name = contentString.find('input.save_name')[0].value;
					//description input field value
					var marker_Address = contentString.find('textarea.save_address')[0].value;
					if(marker_Name == '' || marker_Address == ''){
						alert("Please enter Name and Address!");
					}else{
						//call save marker function
						save_marker(marker,marker_Name,marker_Address,marker_Replace);
					}
				});
			}
			//Click to get Addresss
			google.maps.event.addListener(marker, 'click', function() {
				geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						if (results[0]) {
							$('#pAdd').val(results[0].formatted_address);
						}
					}
				});
				//add click listner to save marker button
				infowindow.open(map,marker);
			});

		}
		//############### Delete Marker Function ##############
		function delete_marker(Marker,Name){
			//Remove saved marker from file json and map using jQuery Ajax
			var myData = {
				name : Name
			};
			$.ajax({
				type : "POST",
				url : "delete.php",
				data : myData,
				success:
				function(data){
					Marker.setMap(null);
					alert("Remove Successful");
				},
				error:function(xhr, ajaxOptions,thrownError){
					alert(thrownError);
				}
			});
		}
		//############### Save Marker Function ##############
		function save_marker(Marker,Name,Address,replace){
			//Save new marker using jQuery Ajax
			var coord=Marker.getPosition();
			var myData = {
				name : Name,
				address : Address,
				lat : coord.lat(),
				lng : coord.lng()
			};
			$.ajax({
				type : "POST",
				url : "save.php",
				data : myData,
				success:
				function(data){
					replace.html(data);//replace info window with new html
				},
				error:function(xhr, ajaxOptions,thrownError){
					alert(thrownError);
				}
			});
		}
	});

</script>
<body>
	<h1 class="heading" align="center">Lưu Địa Điểm Ăn Uống Vui Chơi Xung Quanh</h1>
	<div id="google_map"></div>

</body>
</html>
