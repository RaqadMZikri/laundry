<?php
    session_start();

    // Fungsi untuk menghubungkan ke database
    function connectToDatabase() {
        $conn = mysqli_connect('localhost', 'root', '', 'laundry');
        if (!$conn) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }
        return $conn;
    }
    function register($username, $email, $password) {
       $conn = ConnectToDatabase();
        // Melakukan sanitasi input
        $username = mysqli_real_escape_string($conn, $username);
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);
    
        // Memeriksa apakah username sudah digunakan sebelumnya
        $query = "SELECT * FROM akun WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Username sudah digunakan. Silakan gunakan username lain.')</script>";
            return;
        }
    
        // Menghash password sebelum menyimpan ke database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Query untuk menyimpan data pengguna baru ke database
        $query = "INSERT INTO akun (username,email, password) VALUES ('$username','$email', '$hashedPassword')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Registrasi berhasil.!') </script>";
        } else {
            echo "<script>alert('Registrasi gagal.!') </script>";
        }
    }
    
    // Form registrasi
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        register($username, $email, $password);
    }
    
?>
    <!DOCTYPE html>


    <html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <title>Register | Laundry</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <div class="center">
            <h1>Sign up</h1>
            <form method="post" action="">
                <div class="txt_field">
                    <input type="text" required name="username">
                    <span></span>
                    <label>Username</label>
                </div>
                <div class="txt_field">
                    <input type="email" required name="email">
                    <span></span>
                    <label>Email</label>
                </div>
                <div class="txt_field">
                    <input type="password" required name="password">
                    <span></span>
                    <label>Password</label>
                </div>
                <div class="pass">Lupa Password?</div>
                <input type="submit" value="Register" name="submit">
                <div class="signup_link">
                    Sudah mempunyai akun? <a href="index.php">Signin</a>
                </div>
            </form>
        </div>

    </body>

    </html>