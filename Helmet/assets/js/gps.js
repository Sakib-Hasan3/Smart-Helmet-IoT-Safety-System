let map = L.map('map').setView([0, 0], 2); // Init

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

let marker = null;

function updateGPS() {
  fetch('../backend/gpsHandler.php')
    .then(res => res.json())
    .then(data => {
      if (!data.lat || !data.lng) return;

      const lat = parseFloat(data.lat);
      const lng = parseFloat(data.lng);

      document.getElementById("coords").innerText = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;

      if (marker) {
        marker.setLatLng([lat, lng]);
      } else {
        marker = L.marker([lat, lng]).addTo(map).bindPopup("Helmet Location").openPopup();
        map.setView([lat, lng], 15);
      }
    });
}

updateGPS();
setInterval(updateGPS, 5000); // Refresh every 5 seconds
