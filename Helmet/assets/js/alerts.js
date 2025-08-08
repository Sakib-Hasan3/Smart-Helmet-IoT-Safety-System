function fetchAlerts() {
  fetch('../backend/alertsController.php')
    .then(res => res.json())
    .then(alerts => {
      const list = document.getElementById('alert-list');
      list.innerHTML = '';

      if (alerts.length === 0) {
        list.innerHTML = '<li class="list-group-item bg-secondary text-white">No alerts at the moment.</li>';
        return;
      }

      alerts.forEach(alert => {
        const li = document.createElement('li');
        li.className = 'list-group-item list-group-item-danger d-flex justify-content-between align-items-center';
        li.innerHTML = `<span>${alert.icon} ${alert.message}</span> <span class="badge bg-dark">${alert.time}</span>`;
        list.appendChild(li);
      });
    });
}

// Fetch alerts every 5 seconds
fetchAlerts();
setInterval(fetchAlerts, 5000);
