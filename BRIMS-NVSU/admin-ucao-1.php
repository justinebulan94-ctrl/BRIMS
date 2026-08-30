<?php
    $host = 'localhost';
    $db = 'nvsu_br_system';
    $pass = '';
    $username = 'root';

     $conn = new mysqli($host, $username, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_POST['submit'])){
        $name = $_POST['itemName'];
        $quantity = (int)$_POST['quantity'];
        $description = $_POST['description'];
        $picture = '';

        if (isset($_FILES['itemPicture']) && $_FILES['itemPicture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['itemPicture']['tmp_name'];
            $fileName = $_FILES['itemPicture']['name'];
            
            $uploadFileDir = './uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $newFileName = time() . '_' . basename($fileName);
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $picture = $dest_path;
            }
        }

        $stmt = $conn->prepare("INSERT INTO add_item (item_name1, quantity, item_picture1, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $name, $quantity, $picture, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Data Inserted Successfully!'); window.location.href = 'admin-ucao-1.html';</script>";
        }
        else {
            echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
        }

        $stmt->close();
    }
?>