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