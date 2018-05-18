// VYZADUJE COLOR PALETTE

// globalne premenne
var directionsDisplay;
var map;

var polylines = [];

// mozno nie uplne stastne, ak to budeme chciet zobrazovat do jednej mapy....
function initMap() {
	directionsDisplay = new google.maps.DirectionsRenderer();
	//stred Slovenska
	var mapOptions = {
		zoom: 7,
		center: {lat: 48.6690, lng: 19.6990}
	}

	map = new google.maps.Map(document.getElementById("map"), mapOptions);

	directionsDisplay.setMap(map);
}

// funkcia vykresli na mapu trasu a casti tejto trasy zadanych dlzok
// route - pole LatLng objektov urcujuce body trasy
// subdistances - pole vzdialenosti, ktore presli jednotlivi ludia urcujuce dlzky usekov trasy v "route" v METROCH
function displayRoute(route, subdistances = [], resetView) {
	// dokumentacia ku LatLng objektu: https://developers.google.com/maps/documentation/javascript/reference/3/?csw=1#LatLng

	// vytvorim cirau pre celu trasu a zobrazim ju
	renderPath(route, ROUTE_COLOR);

	var routeBoundaries = new google.maps.LatLngBounds(); // na vycentrovanie pohladu na trasu podla: https://stackoverflow.com/questions/3320925/google-maps-api-calculate-center-zoom-of-polyline/18352311
	routeBoundaries.extend(route[0]);
	var cummulativeDist = [0.0];
	// predpocitam si kumulativnu vzdialenost od zaciatku pre kazdy bod trasy
	for(i = 1; i < route.length; i++) {
		// pre i-tu polozku je kumulativna vzdialenost = i-1-tej polozky + vzdialenost medzi i-tym a i-1-tym bodom
		cummulativeDist.push(cummulativeDist[i-1]);

		// vyratam vzdialenost
		// podla: https://stackoverflow.com/questions/365826/calculate-distance-between-2-gps-coordinates
		cummulativeDist[i] += distanceInMBetweenEarthCoordinates(route[i-1].lat(), route[i-1].lng(), route[i].lat(), route[i].lng());

		// pridam suradnice do boundaries trasy
		routeBoundaries.extend(route[i]);
	}

	if(resetView) {
		// vycentrujem pohlad na trasu
		map.fitBounds(routeBoundaries);
	}

	if(subdistances) {

		// vytvorim ciary pre skratene useky
		for(i = 0; i < subdistances.length; i++) {
			// trasu nevykreslim ak ma 0 dlzku
			if(subdistances[i] <= 0) {
				break; // kedze su zoradene mozem skoncit predcasne
			}

			// najdem cast celkovej trate, ktora je uz predena
			var bound = upperBound(cummulativeDist, 0, cummulativeDist.length-1, subdistances[i]);

			if(bound == -1) {
				// prejdena trat je > ako cielova => vykreslim celu
				renderPath(route, SUBROUTE_COLORS[i%SUBROUTE_COLORS.length]);
			}
			else {
				// prejdena trat je kratsia => trafil som nahodou presne? (skoro urcite nie, kedze mame realne cisla....)
				if(subdistances[i] == cummulativeDist[bound]) {
					// presne => iba vykreslim
					renderPath(route.slice(0, bound+1), SUBROUTE_COLORS[i%SUBROUTE_COLORS.length]);
				}
				else {
					// musim dopocitat novy koncovy bod
					var distDif = cummulativeDist[bound] - subdistances[i]; // rozdiel kumulativnej vzdialenosti pri vacsom bode a aktualnej
					var pointDistDif = cummulativeDist[bound] - cummulativeDist[bound-1]; // rozdiel medzi poslednymi bodmi
					var percentage = distDif/pointDistDif; // podiel rozdielov

					var latDif = route[bound].lat() - route[bound-1].lat(); // rozdiel latitude
					var lngDif = route[bound].lng() - route[bound-1].lng(); // rozdiel longtitude

					var subPath = route.slice(0, bound);
					subPath.push(new google.maps.LatLng(route[bound-1].lat() + latDif*percentage, route[bound-1].lng() + lngDif*percentage));

					// vykreslim
					renderPath(subPath, SUBROUTE_COLORS[i%SUBROUTE_COLORS.length]);
				}
			}
		}
	}
}

// pomocna funkcia na vykreslenie ciary
function renderPath(path, color) {
	var newPath = new google.maps.Polyline({
		path: path,
		geodesic: true,
		strokeColor: color,
		strokeOpacity: 1.0,
		strokeWeight: 5
	});

	newPath.setMap(map);

	polylines.push(newPath);
}

// pomocna funkcia na vymazanie ciar z mapy
function removePolylines() {
	polylines.forEach(function (element) {
		element.setMap(null);
	})

	// vyprazdni zoznam
	polylines.length = 0;
}

// pomocne funkcie na vypocet vzdialenosti
// podla: https://stackoverflow.com/questions/365826/calculate-distance-between-2-gps-coordinates
// uprava na vzdialenost v metroch
function degreesToRadians(degrees) {
	return degrees * Math.PI / 180;
}

function distanceInMBetweenEarthCoordinates(lat1, lon1, lat2, lon2) {
	var earthRadiusM = 6371000;

	var dLat = degreesToRadians(lat2-lat1);
	var dLon = degreesToRadians(lon2-lon1);

	lat1 = degreesToRadians(lat1);
	lat2 = degreesToRadians(lat2);

	var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	return earthRadiusM * c;
}

// pomocne funkcie na najdenie horneho ohranicenia pre nejaky prvok v zotriedenom poly
// podla: https://www.geeksforgeeks.org/ceiling-in-a-sorted-array/
function upperBound(array, low, high, x) {
	// TL; DR binarne vyhladavanie
	// adaptacia C-ckovskeho kodu z uvedenej stranky do javascriptu
	var mid;

	if(x <= array[low]) {
		return low;
	}

	if(x > array[high]) {
		return -1;
	}

	mid = Math.floor((low+high)/2); // celociselne delenie, lebo indexy

	if(x == array[mid]) {
		return mid;
	}
	else if(x > array[mid]) {
		if(mid+1 <= high && x <= array[mid+1]) {
			return mid+1;
		}
		else return upperBound(array, mid+1, high, x);
	}
	else {
		if(mid - 1 >= low && x > array[mid-1]) {
			return mid;
		}
		else return upperBound(array, low, mid-1, x);
	}
}