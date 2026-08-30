<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$host = 'localhost';
$db = 'nvsu_br_system';
$pass = '';
$username = 'root';

$conn = new mysqli("localhost", "root", "", "nvsu_br_system");

if(isset($_POST['submit'])){
    $id = trim($_POST['id']);
    $pass = trim($_POST['pass']);

    $sql = "SELECT * FROM borrower_log WHERE userID='$id' AND pass='$pass'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1){
        header("Location: index-borrower.html");
        exit();
    }
    else{
        echo "<script>alert('Wrong Office ID or Password!'); window.location.href='login2.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRIMS-NVSU-BORROWER-LOGIN</title>
    <link rel="stylesheet" href="login2.css">
</head>
<body onload="document.getElementById('fadeDiv').style.opacity='1'">

    <div class="circle c1"></div>
    <div class="circle c2"></div>
    <div class="circle c3"></div>

    <div id="fadeDiv" class="login-card">
        <div class="logo">
            <img src="logo/nvsugif.gif" alt="NVSU Logo">
            <h2>Borrowers Login</h2>
            <p>NVSU Borrowing System</p>
        </div>

        <form action="login2.php" method="post">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="id" name="id" placeholder="College ID" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="pass" name="pass" placeholder="Password" required>
            </div>

            <button id="submit" name="submit" type="submit">
                <i class="fa-solid fa-right-to-bracket"></i>
                Login
            </button>
        </form>

        <footer>
            &copy; 2026 Nueva Vizcaya State University
        </footer>

    </div>

    <script>
    app.use((req, res, next) => {
        res.set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        next();
    });

        window.addEventListener('pageshow', function (event) {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        window.location.reload();
    }
    });

    if (localStorage.getItem('token')) {
        window.location.replace('/dashboard');
    }

    </script>

</body>
</html>