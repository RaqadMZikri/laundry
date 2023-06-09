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

// Fungsi untuk melakukan login
function login($username, $password) {
    $conn = connectToDatabase();

    // Melakukan sanitasi input
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Query untuk mendapatkan data pengguna berdasarkan username
    $query = "SELECT * FROM akun WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Memeriksa apakah username ditemukan
    if (mysqli_num_rows($result) > 0 ) {
        $row = mysqli_fetch_assoc($result);

        // Memverifikasi password
        if (password_verify($password, $row['password'])) {
            // Menghapus hash password dari memori
            unset($row['password']);

            // Login berhasil, simpan informasi pengguna dalam session
            $_SESSION['user'] = $row;

            $_SESSION["login"] = true;

            // Redirect ke halaman dashboard atau halaman lain yang diinginkan
            header("Location:admin/");
            exit();
        } else {
            // Password tidak valid
            echo "<script>alert(            
                        'Password yang Anda masukkan salah.'
                    )</script>";
        }
    } else {
        // Username tidak valid
        echo "<script>alert(            
            'Username tidak ditemukan..'
        )</script>";
    }
}

// Form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi form login
    if (empty($username) || empty($password)) {
        $errorMessage = "Mohon lengkapi semua field.";
    } else {
        $errorMessage = login($username, $password);
    }
}
?>


    <!DOCTYPE html>


    <html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <title>Login | Laundry</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <div class="center">
            <h1>Sign in</h1>
            <form method="post" action="">
                <div class="txt_field">
                    <input type="text" required name="username">
                    <span></span>
                    <label>Username</label>
                </div>
                <div class="txt_field">
                    <input type="password" required name="password">
                    <span></span>
                    <label>Password</label>
                </div>
                <div class="pass">Lupa Password?</div>
                <input type="submit" value="Login" name="submit">
                <div class="signup_link">
                    Belum mempunyai akun? <a href="register.php">Signup</a>
                </div>
            </form>
        </div>

    </body>

    </html>