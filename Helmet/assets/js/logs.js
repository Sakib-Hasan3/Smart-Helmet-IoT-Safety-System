const logsTable = document.getElementById('logs-table');
const form = document.getElementById('filter-form');

function fetchLogs(params = {}) {
  const query = new URLSearchParams(params).toString();
  fetch(`../backend/logController.php?${query}`)
    .then(res => res.json())
    .then(data => {
      logsTable.innerHTML = '';
      if (data.length === 0) {
        logsTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No logs found</td></tr>';
        return;
      }

      data.forEach(log => {
        logsTable.innerHTML += `
          <tr>
            <td>${log.sensor_type}</td>
            <td>${log.value}</td>
            <td>${log.timestamp}</td>
          </tr>
        `;
      });
    });
}

// Initial load
fetchLogs();

// Filter on submit
form.addEventListener('submit', e => {
  e.preventDefault();
  fetchLogs({
    from: form.from.value,
    to: form.to.value,
    sensor: form.sensor.value
  });
});
