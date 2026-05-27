const API_URL = "https://en.wikipedia.org/w/api.php";

// Function to search Wikipedia using the API
async function searchWikipedia(searchTerm) {
  const params = new URLSearchParams({
    action: "query",
    list: "search",
    srsearch: searchTerm,
    format: "json",
    origin: "*",
  });

  const response = await fetch(`${API_URL}?${params}`);
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return response.json();
}

function displayResults(results) {
  const container = document.getElementById("results-container");
  container.innerHTML = "";

  if (results.length === 0) {
    container.innerHTML = '<p class="no-results">No results found.</p>';
    return;
  }

  const list = document.createElement("ul");
  list.className = "results-list";

  results.forEach((result) => {
    const item = document.createElement("li");
    item.className = "result-item";

    const title = document.createElement("a");
    title.href = `https://en.wikipedia.org/?curid=${result.pageid}`;
    title.target = "_blank";
    title.className = "result-title";
    title.textContent = result.title;

    const snippet = document.createElement("p");
    snippet.className = "result-snippet";
    snippet.innerHTML = result.snippet;

    item.appendChild(title);
    item.appendChild(snippet);
    list.appendChild(item);
  });

  container.appendChild(list);
}

async function saveSearchHistory(searchTerm) {
  try {
    await fetch("./backend/search-history/save_history.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ search_term: searchTerm }),
    });
  } catch (error) {
    console.error("Error saving search history:", error);
  }
}

  function debounce(callback, delay) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        callback(...args);
      }, delay);
    };
  }

async function loadSearchHistory() {
  try {
    const response = await fetch("./backend/search-history/get_history.php");
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    displaySearchHistory(data.history);
  } catch (error) {
    console.error("Error loading search history:", error);
  }
}

function displaySearchHistory(history) {
  const container = document.getElementById("history-container");
  container.innerHTML = "<h2>Search History</h2>";

  if (history.length === 0) {
    container.innerHTML += '<p class="no-history">No search history found.</p>';
    return;
  }

  const list = document.createElement("ul");
  list.className = "history-list";

  history.forEach((entry) => {
    const item = document.createElement("li");
    item.className = "history-item";
    item.textContent = `${entry.search_term} (searched on ${new Date(entry.searched_at).toLocaleString()})`;
    list.appendChild(item);
  });

  container.appendChild(list);
}

async function handleSearch() {
  const input = document.getElementById("search-input");
  const searchTerm = input.value.trim();

  if (!searchTerm || searchTerm.length === 0) {
    document.getElementById("results-container").innerHTML = "";
    return;
  }

  const container = document.getElementById("results-container");
  container.innerHTML = '<p class="loading">Searching...</p>';

  try {
    const data = await searchWikipedia(searchTerm);
    const results = data.query.search;
    displayResults(results);
    
    if (results.length > 0) {
      await saveSearchHistory(searchTerm);
    }
  } catch (error) {
    console.error("Error fetching data:", error);
    container.innerHTML =
      '<p class="error">Error fetching results. Please try again.</p>';
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");

  const optimizedSearch = debounce(() => {
    handleSearch();
  }, 200);

  searchInput.addEventListener("input", optimizedSearch);

  loadSearchHistory();
});
