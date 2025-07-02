// GLOBAL DATA
let showTokensModal;

window.csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let servers = [];
let collectors = [];
let current_collector = 0;
let lines = [];
let num_lines = 999;
let line_offset = 0;

// HELPER FUNCTIONS

function GETRequest(base_url, params, callback) {
    // use window.location.origin as the base for relative URLs
    let url = new URL(base_url, window.location.origin);
    params.csrf_token = window.csrf_token; // Include CSRF token in the request parameters
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json().then(json => {
                if (json.csrf_token) {
                    window.csrf_token = json.csrf_token; // Update global CSRF token if provided
                }
                return json;
            });
        })
        .then(data => callback(null, data))
        .catch(error => callback(error, null));
}


// API HANDLER FUNCTIONS

function fetchAllServers(callback = null) {
    GETRequest('/api/servers', {}, (error, data) => {
        if (error) {
            console.error('Error fetching servers:', error);
            return;
        }
        console.log('Servers:', data);
        servers = data.servers;
        if (callback) {
            callback(servers);
        }
    });
}

function fetchServerCollectors(serverId, callback = null) {
    GETRequest(`/api/collectors/fromserver`, {'server_id':serverId}, (error, data) => {
        if (error) {
            console.error(`Error fetching collectors for server ${serverId}:`, error);
            return;
        }
        console.log(`Collectors for server ${serverId}:`, data);
        collectors = data.collectors;
        callback(collectors);
    });
}

function fetchCollectorLog(collectorId, offset = 0, callback = null) {
    GETRequest(`/api/loglines/fromcollector`, {'collector_id': collectorId, 'offset': offset, 'limit': 100}, (error, data) => {
        if (error) {
            console.error(`Error fetching log for collector ${collectorId}:`, error);
            return;
        }
        console.log(`Log for collector ${collectorId}:`, data);
        lines = data.loglines;
        num_lines = data.total;
        if (callback) {
            callback(lines);
        }
    });
}

function fetchCollectorTokens(collectorId, callback = null) {
    GETRequest(`/api/tokens/fromcollector`, {'collector_id': collectorId}, (error, data) => {
        if (error) {
            console.error(`Error fetching tokens for collector ${collectorId}:`, error);
            return;
        }
        console.log(`Tokens for collector ${collectorId}:`, data);
        // Assuming data.tokens is an array of tokens
        if (callback) {
            callback(data.tokens);
        }
    });
}


// UI FUNCTIONS

function updateServerList() {
    var serverList = document.getElementById('server-list');
    serverList.innerHTML = ''; // Clear existing list
    servers.forEach(server => {
        var listItem = document.createElement('button');
        listItem.className = 'list-group-item list-group-item-action';
        listItem.innerHTML = server.name;
        listItem.onclick = function() {
            fetchServerCollectors(server.id, (collectors) => {
                updateCollectorList();
            });
        };
        serverList.appendChild(listItem);
    });
}

function updateCollectorList() {
    var collectorList = document.getElementById('collector-list');
    collectorList.innerHTML = ''; // Clear existing list
    collectors.forEach(collector => {
        var listItem = document.createElement('li');
        listItem.className = 'nav-item';
        listItem.innerHTML = '<a class="nav-link" href="#">' + collector.name + '</a>';
        listItem.onclick = function() {
            // Handle collector click, e.g., show details or perform an action
            console.log(`Collector clicked: ${collector.name}`);
            current_collector = collector.id; // Store the current collector ID
            fetchCollectorLog(current_collector, 0, (lines) => {
                updateLogLines();
            });
        };
        collectorList.appendChild(listItem);
    });
}

function updateLogLines() {
    var logList = document.getElementById('log-list'); //tbody
    logList.innerHTML = ''; // Clear existing log lines
    lines.forEach(line => {
        var row = document.createElement('tr');
        var linenumber = document.createElement('td');
        linenumber.textContent = num_lines - lines.indexOf(line) - line_offset; // Assuming num_lines is the total number of lines
        var cell = document.createElement('td');
        cell.textContent = line.content; // Assuming line is a string, adjust if it's an object
        row.appendChild(linenumber);
        row.appendChild(cell);
        logList.appendChild(row);
    });
}

function showTokens() {
    if (current_collector === 0) {
        alert('Please select a collector first.');
        return;
    }
    fetchCollectorTokens(current_collector, (tokens) => {
        var tokenlist_dom = document.getElementById('token-list');
        tokenlist_dom.innerHTML = ''; // Clear existing tokens
        tokens.forEach(token => {
            var listItem = document.createElement('li');
            listItem.className = 'list-group-item';
            listItem.textContent = token.content;
            tokenlist_dom.appendChild(listItem);
        });
        showTokensModal.show(); // Show the modal with tokens
    });
}



// RUNTIME
document.addEventListener('DOMContentLoaded', () => {
    fetchAllServers((servers) => {
        updateServerList();
    });
    showTokensModal = new bootstrap.Modal(document.getElementById('showTokensModal'));
});