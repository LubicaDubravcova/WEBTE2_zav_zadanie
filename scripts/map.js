var map;
var geocoder;
var bounds;
var markerClusterer;

function controlButton(text, markers, left) {
	var button = document.createElement("div");
	button.innerHTML = text;
	$(button).css({
		"direction": "ltr",
		"display": "inline-block",
		"overflow": "hidden",
		"text-align": "center",
		"color": "rgb(86, 86, 86)",
		"font-family": "Roboto, Arial, sans-serif",
		"user-select": "none",
		"font-size": "11px",
		"background-color": "rgb(255, 255, 255)",
		"padding": "8px",
		"background-clip": "padding-box",
		"box-shadow": "rgba(0, 0, 0, 0.3) 0px 1px 4px -1px",
		"min-width": "28px"
	});
	if (left) {
		$(button).css({"border-bottom-left-radius": "2px","border-top-left-radius": "2px"});
		$(button).addClass("selected");
	} else 
		$(button).css({"border-bottom-right-radius": "2px","border-top-right-radius": "2px"});
	
	$(button).mouseenter(function() {
    	$(this).css("background-color", "rgb(235, 235, 235)").css("color", "rgb(0,0,0)");
	}).mouseleave(function() {
		if ($(this).hasClass("selected"))
			$(this).css("background-color", "rgb(255, 255, 255)").css("color", "rgb(0,0,0)");
     	else 
			$(this).css("background-color", "rgb(255, 255, 255)").css("color", "rgb(86,86,86)");
	});
	
	$(button).click(function(){
		if ($(this).hasClass("selected"))
			return;
		$(this).addClass("selected").siblings("div").removeClass("selected").trigger("mouseleave");
		markerClusterer.clearMarkers();
		markerClusterer.addMarkers(markers);
	});
	
	return button;
}

function initMap() {
	map = new google.maps.Map(document.getElementById("map"));
    geocoder = new google.maps.Geocoder();
	bounds = new google.maps.LatLngBounds();
	//locations.forEach(createMarker);
	var markers = locations.map(createMarker);
	var schoolMarkers = schools.map(createMarker);
	map.fitBounds(bounds);
	
	markerClusterer = new MarkerClusterer(map, schoolMarkers,{imagePath: 'images/m'});
	markerClusterer.clearMarkers();
	markerClusterer.addMarkers(markers);
	
  	var controlDiv = document.createElement("div");
	$(controlDiv).css("margin","10px").css("cursor","pointer");
	controlDiv.appendChild(controlButton("Domy",markers,true));
	controlDiv.appendChild(controlButton("Školy",schoolMarkers,false));

	controlDiv.index = 1;
	map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlDiv);
}

function createMarker(location) {
	location.lat = parseFloat(location.lat);
	location.lng = parseFloat(location.lng);
	bounds.extend(location);
	return new google.maps.Marker({
        title: location.name,
		map: map,
		position: location
	});
}

google.maps.event.addDomListener(window, "load", initMap);