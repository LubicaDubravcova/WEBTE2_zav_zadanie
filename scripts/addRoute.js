// autocomplete directions podla: https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete-directions
// draggable directions podla: https://developers.google.com/maps/documentation/javascript/examples/directions-draggable

var mapDirectionsDisplay;

function initMap() {
	var map = new google.maps.Map(document.getElementById('map'), {
		mapTypeControl: false,
		zoom: 7,
		center: {lat: 48.6690, lng: 19.6990}
	});

	new AutocompleteDirectionsHandler(map);
}

/**
 * @constructor
 */
function AutocompleteDirectionsHandler(map) {
	this.map = map;
	this.originPlaceId = null;
	this.destinationPlaceId = null;
	this.travelMode = 'WALKING';
	var originInput = document.getElementById('origin-input');
	var destinationInput = document.getElementById('destination-input');
	this.directionsService = new google.maps.DirectionsService;
	this.directionsDisplay = new google.maps.DirectionsRenderer({
		draggable: true,
		map: map,
		//panel: document.getElementById('right-panel')
	});

	this.directionsDisplay.addListener('directions_changed', function() {
		fillFormData(mapDirectionsDisplay.getDirections());
	});

	mapDirectionsDisplay = this.directionsDisplay;


	var originAutocomplete = new google.maps.places.Autocomplete(
		originInput, {placeIdOnly: true});
	var destinationAutocomplete = new google.maps.places.Autocomplete(
		destinationInput, {placeIdOnly: true});

	this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
	this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');

	this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
	this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(destinationInput);
}

AutocompleteDirectionsHandler.prototype.setupPlaceChangedListener = function(autocomplete, mode) {
	var me = this;
	autocomplete.bindTo('bounds', this.map);
	autocomplete.addListener('place_changed', function() {
		var place = autocomplete.getPlace();
		if (!place.place_id) {
			window.alert("Prosím vyberte položku z rozbaľovacieho zoznamu");
			return;
		}
		if (mode === 'ORIG') {
			me.originPlaceId = place.place_id;
		} else {
			me.destinationPlaceId = place.place_id;
		}
		me.route();
	});

};

AutocompleteDirectionsHandler.prototype.route = function() {
	if (!this.originPlaceId || !this.destinationPlaceId) {
		return;
	}
	var me = this;

	this.directionsService.route({
		origin: {'placeId': this.originPlaceId},
		destination: {'placeId': this.destinationPlaceId},
		travelMode: this.travelMode
	}, function(response, status) {
		if (status === 'OK') {
			me.directionsDisplay.setDirections(response);
		} else {
			window.alert('Požiadavka zlyhala: ' + status);
		}
	});
};

function fillFormData(result) {
	var route = result.routes[0];
	var distance = 0.0;
	// nascitam celkovu vzdialenost usekov
	for (var i = 0; i < route.legs.length; i++) {
		distance += route.legs[i].distance.value;
	}

	// vlozim do formulara
	document.getElementById("length").value = distance;

	document.getElementById("length_display").value = distance/1000 + "km";

	// ziskam si kusy ciary reprezentujucej path a vlozim do formulara
	document.getElementById("path").value = route.overview_path;
}