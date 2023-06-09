<?php
    // Fungsi untuk menghubungkan ke database
function connectToDatabase() {
    $conn = mysqli_connect('localhost', 'root', '', 'laundry');
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
    return $conn;
}

?>