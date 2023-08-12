<!DOCTYPE html>
<!--Hey! Thanks for checking out my code :)
  Frederic G. 7/15/2023
-->

<html>
<head>
  <title>Toilets & Wifi</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <!-- L refers to the variable that represents the Leaflet library.-->
</head>
<body>
  <main>

    <header >
      <b>Disclamer</b>: Not intended for use. Proceed at own risk.
    </header>

    <!-- Div for buttons & admin pannel-->
    <div class="main-container">

      <button onclick="getLocation()">
        <span><b>Show<br>Map</b></span>
      </button>

      <button onclick="submitForm()" id="addLocationButton">
        <span>Add<br>Location</span>
      </button>

    <!-- Form for admin location submition-->
      <form id="locationForm">
        <!--latitude & longitude--> 
        <input type="hidden" name="latitude" id="latitudeCoordinate" readonly>
        <input type="hidden" name="longitude" id="longitudeCoordinate" readonly>

        <div>
          <input type="password" name="authority" id="authority" maxlength="10" size="12" placeholder="Admin password">
          <br>
          <label>
            <input type="radio" name="radioBtn" value="wifi" > Wifi
          </label>

          <label>
            <input type="radio" name="radioBtn" value="toilet"> Toilet
          </label>

          <br>
          <div id="wifiInput" style="display: none;">
            Wifi Name:
            <input type="text" name="wifiName" id="wifiName" maxlength="20" size="10">
            Wifi Password:
            <input type="text" name="wifiPassword" id="wifiPassword" maxlength="20" size="10">
          </div>
          
          <div id="toiletInput" style="display: none;">
            Women's Code:
            <input type="text" name="womenPassword" id="womenPassword" maxlength="10" size="10">
            Men's Code:
            <input type="text" name="menPassword" id="menPassword" maxlength="10" size="10">
          </div>
        </div>
      </form>
    </div>
  </main>  

  <script> 
    // JS script for buttons

    // Radio buttons and input field containers
    var radioButtons = document.getElementsByName("radioBtn");
    var wifiInput = document.getElementById("wifiInput");
    var toiletInput = document.getElementById("toiletInput");
  
    // Event listener to radio buttons
    radioButtons.forEach(function(radioButton) {
      radioButton.addEventListener("change", function() {
        
        // Hide all input field containers
        wifiInput.style.display = "none";
        toiletInput.style.display = "none";
  
        // Show input field container based IF radioButton is clicked
        if (radioButton.value === "wifi") {
          wifiInput.style.display = "block";
        } else if (radioButton.value === "toilet") {
          toiletInput.style.display = "block";
        }
      });
    });
  </script>
  
  <script>
    // JS script for form Submition to PHP - Works Fine
    function submitForm() {

      if (confirm("Do you want to add Co-Ordiante\n LAT: "+latitudeCoordinate.value+"\n LNG: "+longitudeCoordinate.value+"\nTo Databse?")){

        var form = document.getElementById("locationForm");
        var formData = new FormData(form);
        //console.log(formData.values);
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "add_location.php", true);

        xhr.onreadystatechange = function() {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status !== 200) {
              console.error("Error: " + xhr.status);
            }
          }
        };
  
        // console log to check on data being passed for debugging      
        //console.log("Data being posted:", formData)
        xhr.send(formData);
      }
    }
    </script>
    

  <!--MAP API-->
  <div id="map"></div> <!--Necessary for map-->
  <script>
    /* This JS script does the following: 
    1. getting User location  -- getLocation()
    2. setting icons markers styles
    3. Set-up the MAP to user location lat & long (from step 1)
    4. getting on-click location & updating the lat,long 
    5. show the User marker location on the map, and add a 150m circle.
    6. get the locations from get_locations.php as XMLHttpRequest
    7. Error codes (standard stuff)
    */

    // 1. getting User location  -- getLocation()
    // Default location coordinates
    const defaultLatitude = 35.17141148554964;
    const defaultLongitude = 129.11012210757997;
  
     function getLocation(defaultLatitude,defaultLongitude) {
      if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(showPosition, showError);
      } else {
          alert("Geolocation is not supported by this browser.");
          // Use default latitude and longitude since geolocation is not available
          showPosition({
                coords: {
                    latitude: defaultLatitude,
                    longitude: defaultLongitude
                }
            });
        }
    }

    // 2. setting icons markers styles
    const toiletIcon = L.icon({
      iconUrl: 'toilet.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
    });

    const wifiIcon = L.icon({
      iconUrl: 'wifi.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
    });

    const hereIcon = L.icon({
      iconUrl: 'here.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
    });

    function showPosition(position) {
      // 3. Set-up the MAP to user location lat & long (from step 1)
      var latitude = position.coords.latitude;
      var longitude = position.coords.longitude;
      
      var map = L.map('map', { maxZoom: 18 }).setView([latitude, longitude], 14);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 100
      }).addTo(map);

      
      // 4. Show the on click marker on the map.
      var userMarker;
      map.on('click', function (e) {
        if (userMarker) { // check if userMarker exists
          map.removeLayer(userMarker); // remove old userMarker
        }
        
        userMarker = new L.Marker([e.latlng.lat,e.latlng.lng]).addTo(map); // set New layer userMarker
      
        // UPDATE the locationCoordinates [hidden] text/input box
        latitudeCoordinate.value = e.latlng.lat;
        longitudeCoordinate.value = e.latlng.lng;
      });


      // 5. show the User marker location on the map, and add a 150m circle.
      const circle = L.circle([latitude,longitude],{
        radius:150,
        fillColor: 'green',
        fillOpacity: 0.1
      }).addTo(map)

      L.marker([latitude, longitude], { icon: hereIcon })
        .bindPopup("Your location")
        .addTo(map);
        console.log("Your current location" + "\nlatitude: " + latitude +"\nlongitude: "+longitude);

        L.geoJ

      // 6. get the locations from get_locations.php as XMLHttpRequest
      const xhr = new XMLHttpRequest();
      xhr.onload = function() {
        const data = JSON.parse(this.responseText);
        data.forEach(location => {
          if (location.type === 'toilet') {
            marker = L.marker([location.lat, location.lng], {icon: toiletIcon})
            .bindPopup(`<p><h3>${location.type}</h3>Women's: ${location.women}<br>Men's: ${location.men}</p>`);
          } else {
              marker = L.marker([location.lat, location.lng], {icon: wifiIcon})
              .bindPopup(`<p><h3>${location.type}</h3>Wifi Name: ${location.name}<br>Password: ${location.password}`);
            }
          marker.addTo(map);
        });
      };
      xhr.open('GET', 'get_locations.php');
      xhr.send();
    }

    // 7. Error codes (standard stuff)
    function showError(error) {

      showPosition({
          coords: {
              latitude: defaultLatitude,
              longitude: defaultLongitude
          }
      });

      switch (error.code) {
        case error.PERMISSION_DENIED:
          alert("To use this app\nPlease allow Geolocation\nor use a browser other than Safari\nthanks!");
          break;
        case error.POSITION_UNAVAILABLE:
          alert("Location information is unavailable.");
          break;
        case error.TIMEOUT:
          alert("The request to get user location timed out.");
          break;
        case error.UNKNOWN_ERROR:
          alert("An unknown error occurred\n ¯\\_(ツ)_/¯ ");
          break;
      }
    }
  </script>
</body>
</html>
