<?php
// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "nvsu_br_system";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch records
$sql = "SELECT Item_name1, Item_picture1, quantity, description FROM add_item";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRIMS-NVSU - View Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="items.css">
    <style>
        /* Search Bar Styling */
.search-container {
    position: relative;
    max-width: 500px;
    margin: 0 auto 20px auto;
}

.search-container input {
    width: 100%;
    padding: 12px 16px 12px 42px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 25px;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    background-color: #ffffff;
}

.search-container input:focus {
    border-color: #1b5e20;
    box-shadow: 0 0 6px rgba(27, 94, 32, 0.2);
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 14px;
}
    </style>
</head>
<body>

    <header class="brims-header">
        <div class="header-container">
            <div class="brand">
                <img src="logo/nvsugif.gif" alt="NVSU Logo" class="logo">
                <div class="brand-text">
                    <h1>BRIMS-NVSU</h1>
                    <p>Nueva Vizcaya State University</p>
                </div>
            </div>

            <a href="index.html" class="back-btn" title="Go Back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </header>

    <main class="main-container">
        
        <div class="search-container">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search items by name or description..." onkeyup="filterItems()">
        </div>

        <div class="product-grid" id="productGrid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="img-box">
                            <?php if (!empty($row['Item_picture1'])): ?>
                                <?php $imagePath = ltrim(str_replace('\\', '/', $row['Item_picture1']), '/'); ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['Item_name1']); ?>">
                            <?php else: ?>
                                <div class="no-img">No Image</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($row['Item_name1']); ?></h3>
                            <p class="product-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                            <div class="product-footer">
                                <span class="stock-badge">Qty: <?php echo htmlspecialchars($row['quantity']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No items available.</p>
            <?php endif; ?>
        </div>

        <p id="noResults" class="no-data" style="display: none;">No matching items found.</p>
    </main>

    <script>
        function filterItems() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const title = card.querySelector('.product-title').textContent.toLowerCase();
                const desc = card.querySelector('.product-desc').textContent.toLowerCase();

                if (title.includes(input) || desc.includes(input)) {
                    card.style.display = "";
                    visibleCount++;
                } else {
                    card.style.display = "none";
                }
            });

            const noResults = document.getElementById('noResults');
            if (cards.length > 0) {
                noResults.style.display = visibleCount === 0 ? "block" : "none";
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>