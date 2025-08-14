<!-- Client List - Modern Card List, Enhanced Styles -->
<style>
    .client-card {
        position: relative;
        border: none;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(10,35,66,0.10);
        margin-bottom: 28px;
        background: #fff;
        overflow: hidden;
        min-height: 140px;
        display: flex;
        align-items: stretch;
        padding: 0;
        transition: box-shadow 0.18s, transform 0.13s, opacity 0.35s cubic-bezier(.4,0,.2,1);
        opacity: 0;
        transform: translateY(30px) scale(0.98);
    }
    .client-card:hover {
        box-shadow: 0 8px 32px rgba(10,35,66,0.13);
        transform: translateY(-2px) scale(1.01);
    }
    .client-card.card-animate-in {
        opacity: 1;
        transform: translateY(0) scale(1);
        animation: cardFadeIn 0.45s cubic-bezier(.4,0,.2,1);
    }
    @keyframes cardFadeIn {
        from { opacity: 0; transform: translateY(30px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .client-card .avatar-section {
        flex: 0 0 130px;
        background: linear-gradient(120deg, #eaf2f8 60%, #f8fafc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 0 0 0.5em;
    }
    .client-card .avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #eaf2f8;
        object-fit: cover;
        border: 2.5px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .client-card .details-section {
        flex: 1 1 auto;
        padding: 10px 10px;
        position: relative;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .client-card .client-name {
        font-size: 1.18em;
        font-weight: 700;
        color: #0A2342;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .client-card .client-name a {
        color: #0A2342;
        text-decoration: none;
        transition: color 0.15s;
    }
    .client-card .client-name a:hover {
        color: #2471A3;
        text-decoration: underline;
    }
    .client-card .client-meta {
        font-size: 0.98em;
        color: #555;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 5px;
    }

    .client-card .client-meta.small {
        font-size: 0.8em;
        color: #888;
    }

    .client-card .status-badge {
        font-size: 0.85em;
        padding: 0.3em 0.8em;
        border-radius: 12px;
        margin-left: 6px;
        vertical-align: middle;
        position: static;
        display: inline-block;
        font-weight: 600;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .client-card .category-label {
        font-size: 0.85em;
        background: #d4e6f1;
        color: #0A2342;
        border-radius: 8px;
        padding: 2px 10px;
        margin-left: 4px;
        font-weight: 600;
    }
    .client-card .action-btns {
        position: absolute;
        top: 10px;
        right: 14px;
        z-index: 2;
        display: flex;
        gap: 6px;
    }
    .client-card .action-btns .btn {
        padding: 4px 7px;
        font-size: 15px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #eaf2f8;
        color: #0A2342;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.07);
        transition: background 0.15s, color 0.15s;
    }
    .client-card .action-btns .btn:hover {
        background: #0A2342;
        color: #fff;
        border-color: #0A2342;
    }
    @media (max-width: 991px) {
        .client-card {
            min-height: 120px;
        }
        .client-card .avatar {
            width: 48px;
            height: 48px;
        }
    }
    @media (max-width: 767px) {
        .client-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .client-card .avatar-section {
            justify-content: flex-start;
            padding: 12px 0 0 12px;
        }
        .client-card .details-section {
            padding: 12px 12px 10px 12px;
        }
        .client-card .action-btns {
            top: 8px;
            right: 10px;
        }
    }
    .container-fluid {
        display: flex;
        flex-direction: column;
    }
    .client-grid-container {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    /* Grid layout for top-aligned cards */
    #clientGrid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        width: 100%;
        padding-bottom: 20px;
        min-height: 0;
    }
    @media (max-width: 1200px) {
        #clientGrid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 767px) {
        #clientGrid {
            grid-template-columns: 1fr;
        }
    }
    .client-card-col {
        width: 100%;
        margin-bottom: 0;
    }
    .pagination-footer {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0 0;
    }
    #paginationInfo {
        margin: 0;
        font-size: 13px;
        color: #555;
    }
    #paginationContainer {
        margin: 0;
    }
</style>
<div class="container-fluid">
    <div class="row search-bar" style="margin-bottom: 15px;">
        <div class="col-sm-6 col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, or address...">
        </div>
        <div class="col-sm-6 col-md-3">
            <select id="filterStatus" class="form-control">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
                <option value="archived">Archived</option>
            </select>
        </div>
        <div class="col-sm-6 col-md-3">
            <select id="filterCategory" class="form-control">
                <option value="">All Categories</option>
                <option value="residential">Residential</option>
                <option value="corporation">Corporation</option>
                <option value="government">Government</option>
            </select>
        </div>
        <div class="col-sm-6 col-md-2">
            <button class="btn btn-primary btn-block" onclick="refreshClientList()"><span
                    class="glyphicon glyphicon-search"></span> Search</button>
        </div>
    </div>
    <div class="client-grid-container">
        <div id="clientGrid">
            <!-- Client cards will be rendered here by JS -->
        </div>
        <div class="pagination-footer">
            <div id="paginationInfo"></div>
            <div id="paginationContainer"></div>
        </div>
        <div id="noResults" style="display:none;">
            <div class="alert alert-warning text-center">No clients found.</div>
        </div>
    </div>
</div>
<script>
    var clients = [];
    var currentPage = 1;
    var clientsPerPage = 12;
    var filteredClients = [];

    function getStatusLabel(status) {
        if ( status == 1 )
            return 'success';
        if ( !status ) return 'default';
    }
    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    function renderClients(list) {
        var grid = document.getElementById('clientGrid');
        grid.innerHTML = '';
        if (list.length === 0) {
            document.getElementById('noResults').style.display = '';
            document.getElementById('paginationContainer').innerHTML = '';
            return;
        } else {
            document.getElementById('noResults').style.display = 'none';
        }
        // Pagination logic
        var startIdx = (currentPage - 1) * clientsPerPage;
        var endIdx = startIdx + clientsPerPage;
        var pageClients = list.slice(startIdx, endIdx);
        pageClients.forEach(function (client, idx) {
            var cardCol = document.createElement('div');
            cardCol.className = 'client-card-col';
            var name = client.app_name || client.name || '';
            var id = client.app_id || client.id || '';
            var status = client.status_id || '';
            var address = client.address || '';
            var system = client.system || '';
            var category = client.category || (client.is_corporate === 'YES' ? 'corporation' : 'residential');
            cardCol.innerHTML =
                '<div class="client-card">' +
                '<div class="avatar-section" style="background-image: url(https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=0A2342&color=fff&size=128)">' +
                '</div>' +
                '<div class="details-section">' +
                '<div class="client-name"><a href="view/' + id + '">' + name + '</a>' +
                '<span class="label label-' + getStatusLabel(status) + ' status-badge">' + capitalize(status) + '</span>' +
                '</div>' +
                '<div class="client-meta"><strong>ID:</strong> PAE-CUST-' + (84000 + parseInt(id)) + '</div>' +
                (system ? '<div class="client-meta"><strong>System:</strong> ' + system + '</div>' : '') +
                '<div class="client-meta small"><span class="glyphicon glyphicon-map-marker text-muted"></span> ' + address + '</div>' +
                '<div class="client-meta"><span class="category-label">' + capitalize(category) + '</span></div>' +
                '</div>' +
                '</div>';
            // Add animation class with staggered delay
            setTimeout(function() {
                cardCol.firstChild.classList.add('card-animate-in');
            }, 60 * idx);
            grid.appendChild(cardCol);
        });
        renderPagination(list.length);
    }
    function renderPagination(totalClients) {
        var container = document.getElementById('paginationContainer');
        if (totalClients <= clientsPerPage) {
            container.innerHTML = '';
            return;
        }
        var totalPages = Math.ceil(totalClients / clientsPerPage);
        var html = '<ul class="pagination" style="margin:0;">';
        // << button
        html += '<li' + (currentPage === 1 ? ' class="disabled"' : '') + '><a href="#" onclick="gotoPage(1);return false;">&laquo;&laquo;</a></li>';
        // < button
        html += '<li' + (currentPage === 1 ? ' class="disabled"' : '') + '><a href="#" onclick="gotoPage(' + (currentPage - 1) + ');return false;">&lt;</a></li>';

        var maxPagesToShow = 5;
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            html += '<li class="disabled"><a href="#">...</a></li>';
        }

        for (var i = startPage; i <= endPage; i++) {
            html += '<li' + (i === currentPage ? ' class="active"' : '') + '><a href="#" onclick="gotoPage(' + i + ');return false;">' + i + '</a></li>';
        }

        if (endPage < totalPages) {
            html += '<li class="disabled"><a href="#">...</a></li>';
        }

        // > button
        html += '<li' + (currentPage === totalPages ? ' class="disabled"' : '') + '><a href="#" onclick="gotoPage(' + (currentPage + 1) + ');return false;">&gt;</a></li>';
        // >> button
        html += '<li' + (currentPage === totalPages ? ' class="disabled"' : '') + '><a href="#" onclick="gotoPage(' + totalPages + ');return false;">&raquo;&raquo;</a></li>';
        html += '</ul>';
        container.innerHTML = html;
    }
    window.gotoPage = function (page) {
        var totalPages = Math.ceil(filteredClients.length / clientsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderClients(filteredClients);
    };
    function filterClients() {
        var search = document.getElementById('searchInput').value.toLowerCase();
        var status = document.getElementById('filterStatus').value;
        var category = document.getElementById('filterCategory').value;
        filteredClients = clients.filter(function (client) {
            var name = client.app_name || client.name || '';
            var email = client.email || '';
            var address = client.address || '';
            var matchesSearch = name.toLowerCase().indexOf(search) !== -1 || email.toLowerCase().indexOf(search) !== -1 || address.toLowerCase().indexOf(search) !== -1;
            var matchesStatus = !status || (client.status && client.status.toLowerCase() === status);
            var cat = client.category || (client.is_corporate === 'YES' ? 'corporation' : 'residential');
            var matchesCategory = !category || cat === category;
            return matchesSearch && matchesStatus && matchesCategory;
        });
        currentPage = 1;
        renderClients(filteredClients);
    }
    // Remove keyup search, use Enter key only
    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            refreshClientList();
        }
    });
    document.getElementById('filterStatus').addEventListener('change', refreshClientList);
    document.getElementById('filterCategory').addEventListener('change', refreshClientList);
    function refreshClientList() {
        if (window.Pace) Pace.restart();
        fetch(baseUrl + '/clients/index')
            .then(function(response) { return response.json(); })
            .then(function(data) {
                clients = data;
                filteredClients = clients.slice();
                currentPage = 1;
                setTimeout(filterClients, 200); // Simulate async for Pace effect
            });
    }
    // Set your base URL here, or use a PHP variable
    var baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
    // Initial load
    refreshClientList();
</script>