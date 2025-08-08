function fetchSensorData() {
  fetch('../backend/sensorData.php')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('sensor-data');
      container.innerHTML = ''; // Clear existing

      const icons = {
        gas: '🔥',
        rain: '🌧️',
        ir: '👀',
        vibration: '💥',
        fall: '🆘',
        eye: '😴',
        speed: '🏍️',
        gps: '📍'
      };

      for (let key in data) {
        let card = `
          <div class="col-md-4">
            <div class="card bg-secondary text-white shadow-sm">
              <div class="card-body">
                <h5 class="card-title">${icons[key] || ''} ${key.replace('_', ' ').toUpperCase()}</h5>
                <p class="card-text fs-5">${data[key]}</p>
              </div>
            </div>
          </div>
        `;
        container.innerHTML += card;
      }
    });
}

// Refresh data every 5 seconds
fetchSensorData();
setInterval(fetchSensorData, 5000);
