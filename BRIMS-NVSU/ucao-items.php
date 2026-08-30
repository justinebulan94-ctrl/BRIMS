<?php
session_start();

// Database Connection
$conn = new mysqli("localhost", "root", "", "nvsu_br_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// HANDLE BORROW REQUEST
if (isset($_POST['submit_borrow'])) {
    $item_id      = (int)$_POST['item_id'];
    $item_name    = $_POST['item_name'];
    $borrow_qty   = (int)$_POST['quantity'];
    $student_id   = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $contact_no   = $_POST['contact_no'];
    $email        = $_POST['email'];
    $borrow_date  = $_POST['borrow_date'];
    $return_date  = $_POST['return_date'];
    $purpose      = $_POST['purpose'];

    // 1. Check current available stock
    $check_stmt = $conn->prepare("SELECT quantity FROM add_item WHERE id = ?");
    $check_stmt->bind_param("i", $item_id);
    $check_stmt->execute();
    $res = $check_stmt->get_result()->fetch_assoc();

    if ($res && $res['quantity'] >= $borrow_qty) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // 2. Insert record into borrow_records
            $insert_stmt = $conn->prepare("INSERT INTO borrow_records (item_name, quantity, student_id, student_name, contact_no, email, borrow_date, return_date, purpose) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sisssssss", $item_name, $borrow_qty, $student_id, $student_name, $contact_no, $email, $borrow_date, $return_date, $purpose);
            $insert_stmt->execute();

            // 3. Deduct stock from add_item
            $update_stmt = $conn->prepare("UPDATE add_item SET quantity = quantity - ? WHERE id = ?");
            $update_stmt->bind_param("ii", $borrow_qty, $item_id);
            $update_stmt->execute();

            $conn->commit();
            echo "<script>alert('Borrow request submitted successfully!'); window.location='items.php';</script>";
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Failed to process request. Please try again.'); window.location='items.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Requested quantity exceeds available stock!'); window.location='items.php';</script>";
        exit;
    }
}

// FETCH ITEMS
$result = $conn->query("SELECT * FROM add_item WHERE quantity > 0 ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRIMS-NVSU - View Items</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- External Stylesheet -->
    <link rel="stylesheet" href="ucao-items.css">
</head>
<body>

    <header>
        <div class="brand">
            <img src="logo/nvsugif.gif" alt="Logo">
            <div>
                <h3 style="line-height:1;">BRIMS-NVSU</h3>
                <small style="opacity: 0.8;">Nueva Vizcaya State University</small>
            </div>
        </div>
        <a href="javascript:history.back()" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </header>

    <div class="container">
        <div class="grid">
            <?php while($item = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-img-container">
                        <img src="<?php echo htmlspecialchars($item['item_picture1']); ?>" class="card-img" alt="Item Image">
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="card-title"><?php echo htmlspecialchars($item['item_name1']); ?></div>
                            <div class="card-desc"><?php echo htmlspecialchars($item['description']); ?></div>
                        </div>
                        <div class="card-footer">
                            <span class="badge-qty">Qty: <?php echo $item['quantity']; ?></span>
                            <button class="btn-borrow" onclick='openBorrowModal(<?php echo json_encode($item); ?>)'>
                                Borrow
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- BORROW MODAL -->
    <div class="modal" id="borrowModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Borrow Request Form</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <!-- Item Information Preview -->
                    <div class="preview-box">
                        <img id="modalImg" src="" alt="Preview">
                        <div class="preview-info">
                            <h4 id="modalItemName"></h4>
                            <p>Available Stock: <strong id="modalStock"></strong></p>
                        </div>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="hidden" name="item_id" id="modalItemId">
                    <input type="hidden" name="item_name" id="modalItemNameInput">

                    <!-- Student Inputs -->
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Quantity to Borrow</label>
                            <input type="number" name="quantity" id="modalQty" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id" placeholder="e.g. 21-0001" required>
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="student_name" required>
                        </div>
                        <div class="form-group">
                            <label>Contact No.</label>
                            <input type="text" name="contact_no" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Borrow Date</label>
                            <input type="date" name="borrow_date" id="borrowDate" required>
                        </div>
                        <div class="form-group">
                            <label>Return Date</label>
                            <input type="date" name="return_date" required>
                        </div>
                        <div class="form-group full-width">
                            <label>Purpose</label>
                            <textarea name="purpose" rows="2" placeholder="State your purpose..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="submit_borrow" class="btn-submit">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('borrowModal');

        function openBorrowModal(item) {
            document.getElementById('modalItemId').value = item.id;
            document.getElementById('modalItemNameInput').value = item.item_name1;
            document.getElementById('modalItemName').innerText = item.item_name1;
            document.getElementById('modalStock').innerText = item.quantity;
            document.getElementById('modalImg').src = item.item_picture1;
            
            const qtyInput = document.getElementById('modalQty');
            qtyInput.max = item.quantity;
            qtyInput.value = 1;

            document.getElementById('borrowDate').value = new Date().toISOString().split('T')[0];

            modal.classList.add('open');
        }

        function closeModal() {
            modal.classList.remove('open');
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>