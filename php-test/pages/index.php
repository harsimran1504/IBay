<?php

// TEST LOGIN INFO, test@test.com, Test123.

session_start(); // Start the session
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}
$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles2.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <?php if (isset($_SESSION['logout_msg'])) :?>
        <div class="alert alert-success alert-dismissible fade show p-3 rounded-3 shadow-sm text-center fw-bold" role="alert" id="successAlert">
            Sign out successful! You have been logged out.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['logout_msg']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['item_added'])) :?>
        <div class="alert alert-success alert-dismissible fade show p-3 rounded-3 shadow-sm text-center fw-bold" role="alert" id="successAlert">
            Item added successfully to Sellers List! You can now view it on Homepage
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['item_added']); ?>
    <?php endif; ?>

    <div class="Title-SearchBar">
        <header> 
            <h1>Ibay</h1>
        </header>
        <form id="searchForm" class="d-flex">
            <input type="text" name="query" id="searchInput" class="form-control me-2" placeholder="Search for any item">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="advanced_search.php" class="text-decoration-none ms-2 advanced-link" data-bs-toggle="modal" data-bs-target="#advancedSearchModal">Advanced</a>
        </form>

        <div class="profile-dropdown">
            <button class="profile-dropdown-btn">
                <?php echo isset($_SESSION['name']) ? 'Welcome, ' . htmlspecialchars($_SESSION['name']) : 'Account'; ?>
            </button>
            <div class="profile-dropdown-content">
                <?php if (isset($_SESSION['name'])): ?>
                    <a href="profile.php">My Profile</a>
                    <a href="basket.php">View Basket</a>
                    <a href="sell.php">Sell Item</a>
                    <a href="../includes/logout.php">Sign Out</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                    <a href="sell.php">Sell Item</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="Divider"> 
        <hr class="solid">
    </div>

    <div class="NavButtons">
        <nav>
            <ul>
                <li><a href="#" data-category="">Home</a></li>
                <li><a href="#" data-category="Electronics">Electronics</a></li>
                <li><a href="#" data-category="clothing">Fashion</a></li>
                <li><a href="#" data-category="Book">Books</a></li>
                <li><a href="#" data-category="Furniture">Furniture</a></li>
                <li><a href="#" data-category="Toy">Toys</a></li>
                <li><a href="#" data-category="Miscellaneous">Miscellaneous</a></li>
            </ul>
        </nav>
    </div>

    <div class="MainContent">
        <div class="sort-bar mb-3">
        <label for="sortSelect" class="form-label">Sort by:</label>
        <select id="sortSelect" class="form-select" style="width:auto; display:inline-block;">
            <option value="">Default (Newest)</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
        </select>
        </div>

        <div class="Products" id = "products-container">
            <?php
            $sql = "SELECT * FROM iBayItems LEFT JOIN iBayImages2 ON iBayItems.itemId = iBayImages2.itemId";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                $displayed = [];
                while($row = mysqli_fetch_assoc($result)) {
                    if ($row['finish'] == '0000-00-00 00:00:00' && !in_array($row['itemId'], $displayed)) {
                        $displayed[] = $row['itemId'];
                        echo "<a href='product_details.php?id=" . $row['itemId'] . "' class='product-link'>";
                        echo "<div class='product-item'>";
                        $imageData = base64_encode($row['image']);
                        $imageMime = $row['mimeType'];
                        echo "<img src='data:$imageMime;base64,$imageData' alt='Product Image' class='product-image'>";
                        echo "<div class='product-info'>";
                        echo "<h2>" . htmlspecialchars($row["title"]) . "</h2>";
                        echo "<p>£" . number_format($row["price"], 2) . " + " . htmlspecialchars($row["postage"]) . "</p>";
                        echo "</div></div>";
                        echo "</a>";
                    } 
                }
            } else {
                echo "<p>No items found.</p>";
            }
            mysqli_close($conn);
            ?>
        </div>
    </div>

    <div class="Divider"> 
        <hr class="solid">
    </div>

    <footer>
        <div class="footer">
            <p>© 2025 Ibay. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.addEventListener('DOMContentLoaded', (event) => {
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
                setTimeout(() => {
                    successAlert.remove();
                }, 500);
            }, 5000);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
    const productsContainer = document.querySelector('.Products');
    const searchForm = document.getElementById('advancedSearchForm');
    const applyBtn = document.getElementById('applyFilters');

    applyBtn.addEventListener('click', function() {
        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);
        
        loadAdvancedResults(params);
        $('#advancedSearchModal').modal('hide');
    });

        async function loadAdvancedResults(params) {
            try {
                productsContainer.innerHTML = `
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary"></div>
                        <p>Loading results...</p>
                    </div>
                `;

                const response = await fetch(`advanced_search.php?${params}`);
                const html = await response.text();

                productsContainer.innerHTML = html;
                history.pushState({}, '', `?${params}`);
                
            } catch (error) {
                productsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        Error loading results. Please try again.
                    </div>
                `;
            }
        }
    });

    
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', ()=>{
    const productsEl = document.getElementById('products-container');
    const links = document.querySelectorAll('nav ul li a[data-category]');

    async function loadCategory(cat) {
        // update URL
        const url = cat ? `?category=${encodeURIComponent(cat)}` : window.location.pathname;
        history.pushState({category:cat}, '', url);

        // fetch HTML fragment
        productsEl.innerHTML = '<div class="spinner-border"></div>';
        try {
        const res = await fetch(`category1.php?category=${encodeURIComponent(cat)}`);
        if (!res.ok) throw new Error(res.statusText);
        const html = await res.text();
        productsEl.innerHTML = html;
        } catch(err) {
        productsEl.innerHTML = `<div class="alert alert-danger">Error loading category.</div>`;
        console.error(err);
        }
    }

    // Link click handlers
    links.forEach(a=>{
        a.addEventListener('click', e=>{
        e.preventDefault();
        const cat = a.getAttribute('data-category');
        loadCategory(cat);
        });
    });

    // Handle back/forward buttons
    window.addEventListener('popstate', e=>{
        const cat = e.state ? e.state.category : '';
        loadCategory(cat);
    });
    });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', ()=>{
        const form = document.getElementById('searchForm');
        const input = document.getElementById('searchInput');
        const container = document.getElementById('products-container');

        async function doSearch(query) {
            // update URL
            const params = new URLSearchParams({query});
            history.pushState({query}, '', '?' + params.toString());

            // loading indicator
            container.innerHTML = `
            <div class="loading-spinner text-center my-5">
                <div class="spinner-border"></div>
                <p>Searching...</p>
            </div>
            `;

            try {
            const res = await fetch(`search1.php?query=${encodeURIComponent(query)}`);
            if (!res.ok) throw new Error(res.statusText);
            const html = await res.text();
            container.innerHTML = html;
            } catch (err) {
            console.error(err);
            container.innerHTML = `<div class="alert alert-danger">Error during search.</div>`;
            }
        }

        // Intercept form submit
        form.addEventListener('submit', e => {
            e.preventDefault();
            const q = input.value.trim();
            doSearch(q);
        });

        // Handle back/forward
        window.addEventListener('popstate', e => {
            const q = e.state?.query || '';
            input.value = q;
            doSearch(q);
        });

        // If ?query= present on initial load
        const initQ = new URLSearchParams(window.location.search).get('query');
        if (initQ) {
            input.value = initQ;
            doSearch(initQ);
        }
        });
    </script>


    <!-- Advanced Search Modal -->
    <div class="modal fade" id="advancedSearchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Advanced Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="advancedSearchForm">
                        <div class="mb-3">
                            <label class="form-label">Search Query</label>
                            <input type="text" name="query" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Clothing">Fashion</option>
                                <option value="Book">Books</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Toy">Toys</option>
                                <option value="Miscellaneous">Miscellaneous</option>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col">
                                <label class="form-label">Min Price (£)</label>
                                <input type="number" name="min_price" class="form-control" step="0.01">
                            </div>
                            <div class="col">
                                <label class="form-label">Max Price (£)</label>
                                <input type="number" name="max_price" class="form-control" step="0.01">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="applyFilters">Search</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>