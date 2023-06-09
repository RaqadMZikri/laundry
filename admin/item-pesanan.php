<?php
         require 'functions.php';
         session_start();
     
         if(!isset($_SESSION["login"])) {
             header("location:../index.php");
             exit();
         }
         $profil = $_SESSION['user'];

    // / Fungsi untuk mendapatkan data dari database
function getDataFromDatabase() {
    $conn = connectToDatabase();

    // Query untuk mendapatkan data dari tabel
    $query = "SELECT i.*, p.nama AS nama_pelanggan, l.nama_layanan
    FROM Item_Pesanan i
    JOIN Pesanan po ON i.id_pesanan = po.id_pesanan
    JOIN Pelanggan p ON po.id_pelanggan = p.id_pelanggan
    JOIN Layanan l ON i.id_layanan = l.id_layanan;
    ";
    $result = mysqli_query($conn, $query);

    // Mendapatkan hasil data dalam bentuk array
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    // Mengembalikan data
    return $data;
}

// Fungsi untuk menyimpan data ke database
function saveDataToDatabase($data) {
    $conn = connectToDatabase();

    // Melakukan sanitasi input
    $id_pesanan = mysqli_real_escape_string($conn, $data['id_pesanan']);
    $id_layanan = mysqli_real_escape_string($conn, $data['id_layanan']);
    $jumlah = mysqli_real_escape_string($conn, $data['jumlah']);

    // Menghitung total biaya
    $takeTotalBiaya = mysqli_query($conn, "SELECT harga FROM layanan l,item_pesanan i WHERE l.id_layanan = '$id_layanan'");
    $rowTB = mysqli_fetch_assoc($takeTotalBiaya);
   $total_biaya =  $jumlah * $rowTB['harga'];
 
    

    // Query untuk menyimpan data ke tabel
    $query = "INSERT INTO item_pesanan (id_item_pesanan, id_pesanan, id_layanan, jumlah,total_biaya) 
    VALUES ('','$id_pesanan', '$id_layanan', '$jumlah', '$total_biaya')";
    $result = mysqli_query($conn, $query);

    // Memeriksa apakah penyimpanan data berhasil
    if ($result) {
        return true;
    } else {
        return false;
    }
}

// Fungsi untuk mengupdate data di database
function updateDataInDatabase($data) {
    $conn = connectToDatabase();

    // Melakukan sanitasi input
    $id_item_pesanan = mysqli_real_escape_string($conn, $data['id_item_pesanan']);
    $id_pesanan = mysqli_real_escape_string($conn, $data['id_pesanan']);
    $id_layanan = mysqli_real_escape_string($conn, $data['id_layanan']);
    $jumlah = mysqli_real_escape_string($conn, $data['jumlah']);
    
    // Menghitung total biaya
    $takeTotalBiaya = mysqli_query($conn, "SELECT harga FROM layanan l,item_pesanan i WHERE l.id_layanan = '$id_layanan'");
    $rowTB = mysqli_fetch_assoc($takeTotalBiaya);
    $total_biaya =  $jumlah * $rowTB['harga'];

    // Query untuk mengupdate data di tabel
    $query = "UPDATE item_pesanan SET id_pesanan = '$id_pesanan', id_layanan = '$id_layanan', jumlah = '$jumlah' , total_biaya = '$total_biaya'WHERE id_item_pesanan = '$id_item_pesanan'";
    $result = mysqli_query($conn, $query);

    // Memeriksa apakah pembaruan data berhasil
    if ($result) {
        return true;
    } else {
        return false;
    }
}

// Fungsi untuk menghapus data dari database
function deleteDataFromDatabase($id_item_pesanan) {
    $conn = connectToDatabase();

    // Query untuk menghapus data dari tabel
    $query = "DELETE FROM item_pesanan WHERE id_item_pesanan = '$id_item_pesanan'";
    $result = mysqli_query($conn, $query);

    // Memeriksa apakah penghapusan data berhasil
    if ($result) {
        return true;
    } else {
        return false;
    }
}

// Proses submit form untuk create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_create'])) {
    $data = array(
        'id_pesanan' => $_POST['id_pesanan'],
        'id_layanan' => $_POST['id_layanan'],
        'jumlah' => $_POST['jumlah']
    );

    // Validasi data sebelum menyimpan
    $errorMessage = '';
    if (empty($data['id_pesanan'])) {
        $errorMessage .= 'tanggal pesanan harus diisi. ';
    }
    if (empty($data['id_layanan'])) {
        $errorMessage .= 'tanggal pengantaran harus diisi. ';
    }

    // Menyimpan data jika tidak ada kesalahan
    if (empty($errorMessage)) {
        $result = saveDataToDatabase($data);
        if ($result) {
            $successMessage = 'Data berhasil disimpan.';
        } else {
            $errorMessage = 'Terjadi kesalahan saat menyimpan data.';
        }
    }
}

// Proses submit form untuk edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit'])) {
    $data = array(
        'id_item_pesanan' => $_POST['id_item_pesanan'],
        'id_pesanan' => $_POST['id_pesanan'],
        'id_layanan' => $_POST['id_layanan'],
        'jumlah' => $_POST['jumlah'],
        'total_biaya' => $_POST['total_biaya']
    );

    // Validasi data sebelum mengupdate
    $errorMessage = '';
    if (empty($data['id_pesanan'])) {
        $errorMessage .= 'tanggal pesanan harus diisi. ';
    }
    if (empty($data['id_layanan'])) {
        $errorMessage .= 'tanggal pengantaran harus diisi. ';
    }

    // Mengupdate data jika tidak ada kesalahan
    if (empty($errorMessage)) {
        $result = updateDataInDatabase($data);
        if ($result) {
            $successMessage = 'Data berhasil diupdate.';
        } else {
            $errorMessage = 'Terjadi kesalahan saat mengupdate data.';
        }
    }
}

// Proses delete data
if (isset($_GET['delete'])) {
    $id_item_pesanan = $_GET['delete'];

    // Menghapus data jika id valid
    if (!empty($id_item_pesanan)) {
        $result = deleteDataFromDatabase($id_item_pesanan);
        if ($result) {
            $successMessage = 'Data berhasil dihapus.';
        } else {
            $errorMessage = 'Terjadi kesalahan saat menghapus data.';
        }
    }
}



// Mengambil data dari database
$data = getDataFromDatabase();

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title> Admin  - Item Pesanan</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
    <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-fw fa-tshirt"></i>
    </div>
    <div class="sidebar-brand-text mx-3">Laundry <sup></sup></div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item">
    <a class="nav-link" href="index.php">
        <i class="fas fa-fw fa-home"></i>
        <span>Dashboard</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Addons
</div>

<!-- Nav Item - Tables -->
<li class="nav-item active">
    <a class="nav-link active" href="item-pesanan.php">
        <i class="fas fa-fw fa-shopping-bag"></i>
        <span>ITEM PESANAN</span></a>
</li>
<li class="nav-item">
    <a class="nav-link" href="layanan.php">
        <i class="fas fa-fw fa-hand-holding"></i>
        <span>LAYANAN</span></a>
</li>
<li class="nav-item ">
    <a class="nav-link" href="pelanggan.php">
        <i class="fas fa-fw fa-users"></i>
        <span>PELANGGAN</span></a>
</li>
<li class="nav-item ">
    <a class="nav-link" href="pesanan.php">
        <i class="fas fa-fw fa-cart-plus"></i>
        <span>PESANAN</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>



</ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?=$profil['username'];?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">

                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800">ITEM PESANAN</h1>
            <p class="mb-4">Tabel ini digunakan untuk mencatat detail item layanan dalam setiap pesanan.</p>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">DATA ITEM PESANAN </h6>
                    <button class="btn btn-success" href="#" data-toggle="modal" data-target="#tambahModal">
                            Tambah Data
                        </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Pesanan</th>
                                    <th>Layanan</th>
                                    <th>Jumlah</th>
                                    <th>Total Biaya</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tfoot class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Pesanan</th>
                                    <th>Layanan</th>
                                    <th>Jumlah</th>
                                    <th>Total Biaya</th>
                                    <th>Option</th>
                                </tr>
                            </tfoot>
                            <?php 
                                $i = 1;
                            foreach($data as $item_pesanan) :?>
                        <tbody class="text-center">
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td><?php echo htmlspecialchars($item_pesanan['id_pesanan']);?></td>
                            <td><?php echo htmlspecialchars($item_pesanan['nama_layanan']);?></td>
                            <td><?php echo htmlspecialchars($item_pesanan['jumlah']);?></td>
                            <td><?php echo htmlspecialchars($item_pesanan['total_biaya']);?></td>
                            <td>
                                <div class="d-flex align-items-center justify-content-around">
                                <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $item_pesanan['id_item_pesanan'];?>" >
                                <i class="fas fa-fw fa-edit"></i>
                        </button>
                                <a class="btn btn-danger" onclick="return confirm('Apakah anda yakin menghapusnya?')" href="?delete=<?php echo $item_pesanan['id_item_pesanan']; ?>"> <i class="fas fa-fw fa-trash"></i></a>
                           
                                </div>    
                            </td>
                        </tr>
                                                  <!-- Edit Modal-->
        <div class="modal fade" id="editModal<?php echo $item_pesanan['id_item_pesanan'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data</h5>
                   
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
             
                <div class="modal-body">
                <form action="" method="POST">
                <input type="hidden" name="id_item_pesanan" value="<?=$item_pesanan['id_item_pesanan']?>">
                        <label for="id_pesanan"> Pesanan : </label>
                        <select name="id_pesanan" class="form-control mb-2">
        <option> PILIH</option>
            <?php
               $conn = connectToDatabase();
                $query = mysqli_query($conn, "SELECT * FROM pesanan p, pelanggan q WHERE p.id_pelanggan = q.id_pelanggan");
               foreach($query as $row) :?>
            <option value="<?=$row['id_pesanan']?>">
                <?=$row['nama'];?>
            </option>
          <?php
                endforeach ;
            ?>
        </select>
        <hr>
        <label for="id_layanan"> Layanan : </label>
                        <select name="id_layanan" class="form-control mb-2">
        <option> PILIH</option>
            <?php
               $conn = connectToDatabase();
                $query = mysqli_query($conn, "SELECT * FROM layanan ");
               foreach($query as $row) :?>
            <option value="<?=$row['id_layanan']?>">
                <?=$row['nama_layanan'];?>
            </option>
            
          <?php
                endforeach ;
            ?>
        </select>
        <hr>
            <label for="jumlah">Jumlah</label>
            <input type="number" id="jumlah" name="jumlah" class="form-control" value="<?=$item_pesanan['jumlah'];?>">
            <input type="hidden" name="total_biaya">
                       
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <input class="btn btn-warning" type="submit" name="submit_edit">
                    </form>
                </div>
            </div>
        </div>
    </div>
                        </tbody>
                        <?php endforeach;?>
                        </table>

    
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Raqad Muhammad Zikri 2023</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" jika kamu yakin ingin keluar</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>
  
        <!-- Tambah Modal-->
        <div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                   
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                <form action="" method="POST">
                <label for="id_pesanan"> Pesanan : </label>
                        <select name="id_pesanan" class="form-control mb-2">
        <option>PILIH</option>
            <?php
               $conn = connectToDatabase();
                $query = mysqli_query($conn, "SELECT * FROM pesanan p, pelanggan q WHERE p.id_pelanggan = q.id_pelanggan ");
               foreach($query as $row) :?>
            <option value="<?=$row['id_pesanan']?>">
                <?=$row['nama'];?>
            </option>
          <?php
                endforeach ;
            ?>
        </select>
        <hr>
        <label for="id_layanan"> Layanan : </label>
                        <select name="id_layanan" class="form-control mb-2">
        <option>PILIH</option>
            <?php
               $conn = connectToDatabase();
                $query = mysqli_query($conn, "SELECT * FROM layanan ");
               foreach($query as $row) :?>
            <option value="<?=$row['id_layanan']?>">
                <?=$row['nama_layanan'];?>
            </option>
          
          <?php
                endforeach ;
            ?>
        </select>
        <hr>
            <label for="jumlah">Jumlah</label>
            <input type="number" id="jumlah" name="jumlah" class="form-control">    
            <!-- <hr>
                        <label for="total_biaya">total Biaya : </label>
                        <input type="number" class="form-control" id="total_biaya" name="total_biaya" > -->
                    <input type="hidden" name="total_biaya">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <input class="btn btn-success" type="submit" name="submit_create">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

   
</body>

</html>