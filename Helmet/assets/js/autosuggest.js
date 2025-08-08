const input = document.getElementById('search-input');
const suggestionBox = document.getElementById('suggestions');

input.addEventListener('input', () => {
  const query = input.value.trim();

  if (query.length < 2) {
    suggestionBox.innerHTML = '';
    return;
  }

  fetch(`../backend/autocomplete.php?query=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      suggestionBox.innerHTML = '';
      data.forEach(item => {
        const li = document.createElement('li');
        li.className = 'list-group-item list-group-item-action';
        li.textContent = item;
        li.onclick = () => {
          input.value = item;
          suggestionBox.innerHTML = '';
          // Trigger filtering logs if needed
        };
        suggestionBox.appendChild(li);
      });
    });
});
