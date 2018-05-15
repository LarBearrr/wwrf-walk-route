<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Walk Route Mapper</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

                        #map {
                height: 500px;
                width: 100%;
            }

            #listing {
                margin-top: 20px;
            }
            #description {
                font-family: Roboto;
                font-size: 15px;
                font-weight: 300;
            }

            #infowindow-content .title {
                font-weight: bold;
            }

            #infowindow-content {
                display: none;
            }

            #map #infowindow-content {
                display: inline;
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top scrolling-navbar">
            <div class="container">
                <a class="navbar-brand" href="#home"><i class="fa fa-map"></i> Walk Route Mapper</a>
 
                <form class="form-inline my-2 my-lg-0 ml-auto w-auto">
                <select name="mapchange" onchange="updateMap(this.options[this.selectedIndex].value)" class="form-control form-control-sm">
                        <option value="" disabled selected hidden>Choose type of business...</option>
                        <option value="restaurants">Restaurants</option>
                        <option value="warehouses">Warehouses</option>
                        <option value="temp services">Temp Services</option>
                        <option value="fast food">Fast Food</option>
                        <option value="construction">Construction</option>
                        <option value="manufacturing">Manufacturing</option>
                        <option value="aircraft">Aircraft</option>
                    </select>
                </form>

            </div>
        </nav>
        <div class="container mt-5 pt-5">
            <div class="row">
                <div class="col-4">
                        <!-- results listed here -->
                        <div id="listing">
                            <table id="resultsTable">
                            <tbody id="results"></tbody>
                            </table>
                            <!-- directions displayed here -->
                            <div id="map-directions"></div>
                        </div>
                        
                        <div id="info">
                            <table id="infoTable">
                                <tbody id="infoTableBody"></tbody>
                            </table>
                        </div>
                        <div id="map-directions"></div>
                        
                </div>
                <div class="col-8">
                    <!--  where the map will live -->
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </body>

    <script src="{{asset('js/app.js')}}" ></script>

    <script>
        // This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

var map;

var infowindow;

var wwrf = {
        lat: 37.682319,
        lng: -97.333311
    };

function initMap() {

    map = new google.maps.Map(document.getElementById('map'), {
        center: wwrf,
        zoom: 14,
        mapTypeControl: false,
        panControl: false,
        zoomControl: false,
        streetViewControl: false
    });

    var squareCoords = [
        {lat: 37.697465, lng: -97.341629},
        {lat: 37.697636, lng: -97.317306},
        {lat: 37.671759, lng: -97.317142},
        {lat: 37.673308, lng: -97.352833},
        {lat: 37.693239, lng: -97.352852}
    ];

    // Construct the walkroute polygon.
    var walkRoute = new google.maps.Polygon({
        paths: squareCoords,
        strokeColor: '#008000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#008000',
        fillOpacity: 0.1
    });
    
    walkRoute.setMap(map);

    infowindow = new google.maps.InfoWindow();
    /*var service = new google.maps.places.PlacesService(map);
    service.nearbySearch({
        location: wwrf,
        radius: 1600,
        type: ['establishment'],
        keyword: ['restaurant']
    }, callback);*/

}

var markers = [];

var MARKER_PATH = 'https://developers.google.com/maps/documentation/javascript/images/marker_green';

function callback(results, status) {
if (status === google.maps.places.PlacesServiceStatus.OK) {
    for (var i = 0; i < results.length; i++) {
        createMarker(results[i], i);
        addResult(results[i], i);
    }
    directionsDisplay.setMap(null); // clear direction from the map
    directionsDisplay.setPanel(null); // clear directionpanel from the map          
    //directionsDisplay = new google.maps.DirectionsRenderer(); // this is to render again, otherwise your route wont show for the second time searching
    //directionsDisplay.setMap(map); //this is to set up again
    map.setZoom(14);
}
}

var directionsDisplay;

function createMarker(place, i) {
    var placeLoc = place.geometry.location;
    
    var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (i % 26));
    var markerIcon = MARKER_PATH + markerLetter + '.png';
    
    var marker = new google.maps.Marker({
        map: map,
        position: place.geometry.location,
        icon: markerIcon,
        animation: google.maps.Animation.DROP
    });

    // Create a marker for each place.
    markers.push(marker);

    google.maps.event.addListener(marker, 'click', function() {
        
        var directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer;
        
        // Show info window on map
        infowindow.setContent('<strong>' + place.name + '</strong><br/>' + place.vicinity);
        infowindow.open(map, this);
        
        var service = new google.maps.DistanceMatrixService;
        
        service.getDistanceMatrix({
            origins: [{lat:37.681975, lng: -97.333269}],
            destinations: [place.vicinity],
            travelMode: 'WALKING',
            unitSystem: google.maps.UnitSystem.IMPERIAL,
            avoidHighways: false,
            avoidTolls: false
        }, function(response, status) {
            if (status !== 'OK') {
                alert('Error was: ' + status);
            } else {
                var originList = response.originAddresses;
                var destinationList = response.destinationAddresses;
                
                // Clear out the old markers.
                markers.forEach(function(marker) {
                    marker.setMap(null);
                });
                markers = [];
                
                clearResults();

                var showGeocodedAddressOnMap = function(asDestination) {
                    var icon = asDestination ? destinationIcon : originIcon;
                    return function(results, status) {
                        if (status === 'OK') {
                        map.fitBounds(bounds.extend(results[0].geometry.location));
                        markers.push(new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            icon: icon
                        }));
                        } else {
                        alert('Geocode was not successful due to: ' + status);
                        }
                    };
                };
    
                for (var i = 0; i < originList.length; i++) {
                    var results = response.rows[i].elements;
    
                    for (var j = 0; j < results.length; j++) {
                        
                        var distance = results[j].distance.value

                        directionsDisplay.setMap(map);
                        directionsDisplay.setPanel(document.getElementById('map-directions'));
    
                        directionsService.route({
                            origin: {lat:37.681975, lng: -97.333269},
                            destination: place.vicinity,
                            travelMode: 'WALKING',
                        }, function(response, status) {
                            if (status === 'OK') {
                            directionsDisplay.setDirections(response);
                            } else {
                            window.alert('Directions request failed due to ' + status);
                            }
                        });
                        
                        showPlace(place);
                    }
                }
            }
        });

    });
}

function calculateAndDisplayRoute(directionsService, directionsDisplay) {
    var start = wwrf;
    var end = place.vicinity;
    directionsService.route({
    origin: start,
    destination: end,
    travelMode: 'WALKING'
    }, function(response, status) {
    if (status === 'OK') {
        directionsDisplay.setDirections(response);
    } else {
        window.alert('Directions request failed due to ' + status);
    }
    });
}

function updateMap(selectControl)   {
    // Clear out the old markers.
    markers.forEach(function(marker) {
        marker.setMap(null);
    });
    markers = [];
    
    clearResults();

    var keyword = selectControl;
    var service = new google.maps.places.PlacesService(map);
    service.nearbySearch({
        location: wwrf,
        radius: 1600,
        type: ['establishment'],
        keyword: keyword
    }, callback);
}

function addResult(result, i) {
var results = document.getElementById('results');
var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (i % 26));
var markerIcon = MARKER_PATH + markerLetter + '.png';

var tr = document.createElement('tr');
tr.style.backgroundColor = (i % 2 === 0 ? '#F0F0F0' : '#FFFFFF');
tr.onclick = function() {
    google.maps.event.trigger(markers[i], 'click');
};

var iconTd = document.createElement('td');
var nameTd = document.createElement('td');
var icon = document.createElement('img');
icon.src = markerIcon;
icon.setAttribute('class', 'placeIcon');
icon.setAttribute('className', 'placeIcon');
var name = document.createTextNode(result.name);
iconTd.appendChild(icon);
nameTd.appendChild(name);
tr.appendChild(iconTd);
tr.appendChild(nameTd);
results.appendChild(tr);
}

function showPlace(place) {
    
    
    var results = document.getElementById('results');
    //var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (i % 26));
    //var markerIcon = MARKER_PATH + markerLetter + '.png';
    
    var tr = document.createElement('tr');
    //tr.style.backgroundColor = (i % 2 === 0 ? '#F0F0F0' : '#FFFFFF');
    //tr.onclick = function() {
    //google.maps.event.trigger(markers[i], 'click');
    //};
    
    //var iconTd = document.createElement('td');
    var nameTd = document.createElement('tr');
    var addressTd = document.createElement('tr');
    
    //var icon = document.createElement('img');
    //icon.src = markerIcon;
    //icon.setAttribute('class', 'placeIcon');
    //icon.setAttribute('className', 'placeIcon');
    var name = document.createTextNode(place.name);
    var address = document.createTextNode(place.vicinity);
    
    //iconTd.appendChild(icon);
    nameTd.appendChild(name);
    addressTd.appendChild(address);
    
    //tr.appendChild(iconTd);
    tr.appendChild(nameTd);
    tr.appendChild(addressTd);
    
    
    var placeInfo = new google.maps.places.PlacesService(map);
    placeInfo.getDetails({
        placeId: place.place_id,
    }, callbackInfo);
    
    function callbackInfo(place, status) {
    if (status == google.maps.places.PlacesServiceStatus.OK) {
        
        var phoneTr = document.createElement('tr');
        var phone = document.createTextNode(place.formatted_phone_number);
        
        phoneTr.appendChild(phone);
        tr.appendChild(phoneTr);
        
        
        var linkTr = document.createElement('tr');
        var linkElement = document.createElement('A');
        
        linkElement.href = place.website;
        linkElement.target = "_blank";
        linkElement.text = place.website;
        linkTr.appendChild(linkElement);
        
        tr.appendChild(linkTr);
        
        return tr;
    }
    }
    
    results.appendChild(tr);
}

function clearResults() {
var results = document.getElementById('results');
while (results.childNodes[0]) {
    results.removeChild(results.childNodes[0]);
}
}

function clearInfo() {
var info = document.getElementById('infoTableBody');
while (info.childNodes[0]) {
    info.removeChild(info.childNodes[0]);
}
}

function clearMarkers() {
    for (var i = 0; i < markersArray.length; i++ ) {
        markersArray[i].setMap(null);
    }
    markersArray.length = 0;
}
</script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDCEEQWmjat4dZoYXpY9zBg4w3zLD91Sfo&libraries=places&callback=initMap" async defer></script>
</html>
