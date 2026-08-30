<?php
session_start();

// Security Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Database Connection
$conn = new mysqli("localhost", "root", "", "nvsu_br_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// UPDATE LOGIC (With Image Upload Support)
if (isset($_POST['update'])) {
    $id    = (int)$_POST['id'];
    $name  = $_POST['item_name1'];
    $qty   = (int)$_POST['quantity'];
    $desc  = $_POST['description'];

    // Check if a new image was uploaded
    if (isset($_FILES['item_picture1']) && $_FILES['item_picture1']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['item_picture1']['tmp_name'];
        $fileName    = $_FILES['item_picture1']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = 'uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $newFileName = time() . '_' . md5(uniqid()) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $conn->prepare("UPDATE add_item SET item_name1 = ?, quantity = ?, description = ?, item_picture1 = ? WHERE id = ?");
                $stmt->bind_param("sissi", $name, $qty, $desc, $dest_path, $id);
            } else {
                echo "<script>alert('Error moving uploaded image.'); window.location='admin-ucao-2.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid file format! Allowed: JPG, PNG, GIF, WEBP.'); window.location='admin-ucao-2.php';</script>";
            exit;
        }
    } else {
        $stmt = $conn->prepare("UPDATE add_item SET item_name1 = ?, quantity = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sisi", $name, $qty, $desc, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Item Updated Successfully!'); window.location='admin-ucao-2.php';</script>";
        exit;
    }
}

// DELETE LOGIC
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM add_item WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Item Deleted!'); window.location='admin-ucao-2.php';</script>";
        exit;
    }
}

// FETCH ITEMS
$sql = "SELECT * FROM add_item ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRIMS-NVSU-ADMIN-UPDATE-ITEM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="admin-ucao-2.css">
    <style>
      /* Search Bar Styling */
.search-container {
    position: relative;
    max-width: 500px;
    margin: 0 auto 25px auto;
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
    font-family: inherit;
}

.search-container input:focus {
    border-color: #1B5E20;
    box-shadow: 0 0 6px rgba(27, 94, 32, 0.25);
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 14px;
}

.no-data {
    text-align: center;
    padding: 30px;
    color: #777;
    font-weight: 500;
}
    </style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
      <div class="brand">
        <img class="logo0" src="logo/nvsugif.gif" alt="NVSU Logo">
        <a href="#" class="logo">BRIMS-NVSU-ADMIN</a>
      </div>

      <button class="hamburger" id="hamburger-btn" aria-label="Toggle Navigation">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>

      <ul class="nav-menu" id="nav-menu">
        <li class="nav-item"><a href="admin-ucao-1.html" class="nav-link"><i class="fa-solid fa-plus"></i> Add Item</a></li>
        <li class="nav-item"><a href="admin-ucao-2.php" class="nav-link active-link"><i class="fa-solid fa-pen-to-square"></i> Update Item</a></li>
        <li class="nav-item"><a href="admin-ucao-3.html" class="nav-link"><i class="fa-solid fa-file-lines"></i> Requests & Status</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>
  </nav>
<main class="main-container">

    <section class="page-heading">
        <div class="heading-icon">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>
        <div>
            <h2>UPDATE ITEM</h2>
            <p>Update item to the inventory.</p>
        </div>
    </section>

    <div class="search-container">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" id="searchInput" placeholder="Search items by name or description..." onkeyup="filterItems()">
    </div>

    <div class="table-wrapper">
      <table class="responsive-table">
        <thead>
          <tr>
            <th>Picture</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Description</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="inventoryTableBody">
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()) { ?>
              <tr class="item-row">
                <td data-label="Picture">
                  <img class="item-img" src="<?php echo htmlspecialchars($row['item_picture1']); ?>" alt="Item Picture">
                </td>
                <td data-label="Item Name">
                  <span class="item-title-text"><?php echo htmlspecialchars($row['item_name1']); ?></span>
                </td>
                <td data-label="Quantity">
                  <span class="badge-qty"><?php echo htmlspecialchars($row['quantity']); ?></span>
                </td>
                <td data-label="Description">
                  <p class="item-desc-text"><?php echo htmlspecialchars($row['description']); ?></p>
                </td>
                <td data-label="Action" class="action-cell">
                  <button class="btn-edit" onclick="toggleEdit(<?php echo $row['id']; ?>)">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </button>
                  <button class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">
                    <i class="fa-solid fa-trash-can"></i> Delete
                  </button>
                </td>
              </tr>
              <tr id="edit-<?php echo $row['id']; ?>" class="edit-form-row">
                <td colspan="5">
                  <div class="edit-form-container">
                    <form method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                      
                      <div class="image-upload-wrapper">
                        <label>Photo Preview:</label>
                        <img id="preview-<?php echo $row['id']; ?>" src="<?php echo htmlspecialchars($row['item_picture1']); ?>" class="edit-img-preview" alt="Preview">
                        <label class="file-upload-btn">
                          <i class="fa-solid fa-camera"></i> Change Image
                          <input type="file" name="item_picture1" accept="image/*" onchange="previewImage(event, <?php echo $row['id']; ?>)">
                        </label>
                      </div>

                      <div class="form-inputs-group">
                        <div class="input-field">
                          <label>Item Name</label>
                          <input type="text" name="item_name1" value="<?php echo htmlspecialchars($row['item_name1']); ?>" required>
                        </div>
                        <div class="input-field">
                          <label>Quantity</label>
                          <input type="number" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" required>
                        </div>
                        <div class="input-field full-width">
                          <label>Description</label>
                          <textarea name="description" rows="2" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                        </div>
                        <div class="form-actions">
                          <button type="submit" name="update" class="btn-save">
                            <i class="fa-solid fa-floppy-disk"></i> Save Changes
                          </button>
                          <button type="button" class="btn-cancel" onclick="toggleEdit(<?php echo $row['id']; ?>)">
                            Cancel
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </td>
              </tr>
            <?php } ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="no-data">No items available.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      
      <p id="noResults" class="no-data" style="display: none;">No matching items found.</p>
    </div>
  </main>
<footer>
        <div class="footer-content">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Secure</span>•<span>Reliable</span>•<span>Efficient</span>
        </div>
        <p>&copy; 2026 BRIMS-NVSU</p>
</footer>
<script src="admin-ucao-2.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.getElementById('hamburger-btn');
    const navMenu = document.getElementById('nav-menu');

    if (hamburger && navMenu) {
      hamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
      });

      document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
          hamburger.classList.remove('active');
          navMenu.classList.remove('active');
        });
      });

      document.addEventListener('click', (e) => {
        if (!navMenu.contains(e.target) && !hamburger.contains(e.target)) {
          hamburger.classList.remove('active');
          navMenu.classList.remove('active');
        }
      });
    }
  });

  // Live Search Filter Function
function filterItems() {
  const input = document.getElementById('searchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#inventoryTableBody .item-row');
  let visibleCount = 0;

  rows.forEach(row => {
    const title = row.querySelector('.item-title-text').textContent.toLowerCase();
    const desc = row.querySelector('.item-desc-text').textContent.toLowerCase();
    
    // Find the next sibling edit-form-row corresponding to this row
    const editRow = row.nextElementSibling;

    if (title.includes(input) || desc.includes(input)) {
      row.style.display = "";
      visibleCount++;
    } else {
      row.style.display = "none";
      // Hide edit form if row gets hidden during search
      if (editRow && editRow.classList.contains('edit-form-row')) {
        editRow.style.display = "none";
      }
    }
  });

  const noResults = document.getElementById('noResults');
  if (rows.length > 0) {
    noResults.style.display = visibleCount === 0 ? "block" : "none";
  }
}
</script>
</body>
</html>