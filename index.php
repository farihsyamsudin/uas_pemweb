<?php
    session_start();

    include('connection.php');

    if(isset($_POST['logout'])) {
        $_SESSION = [];
        session_unset();
        session_destroy();
        header('location:index.php');
        exit;
    }

    // Function Upload Gambar Start
    function upload(){
        $namafile = $_FILES['gambar']['name'];
        $ukuranfile = $_FILES['gambar']['size'];
        $error = $_FILES['gambar']['error'];
        $tmp = $_FILES['gambar']['tmp_name'];

        if ($error === 4) {
            return false;
        }

        // Cek Format
        $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
        $ekstensiGambar = explode('.', $namafile);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
            return false;
        }
        // Cek Format END

        if ($ukuranfile > 10000000) {
            return false;
        }

        // BuatNamaFileBaru
        $namafilebaru = uniqid();
        $namafilebaru .= '.';
        $namafilebaru .= $ekstensiGambar;

        move_uploaded_file($tmp, 'img/cover/'. $namafilebaru);

        return $namafilebaru;
    }
    // Function Upload Gambar End

    // Function Upload Gambar Start
    function uploadIcon(){
        $namafile = $_FILES['icon']['name'];
        $ukuranfile = $_FILES['icon']['size'];
        $error = $_FILES['icon']['error'];
        $tmp = $_FILES['icon']['tmp_name'];

        if ($error === 4) {
            return false;
        }

        // Cek Format
        $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
        $ekstensiGambar = explode('.', $namafile);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
            return false;
        }
        // Cek Format END

        if ($ukuranfile > 10000000) {
            return false;
        }

        // BuatNamaFileBaru
        $namafilebaru = uniqid();
        $namafilebaru .= '.';
        $namafilebaru .= $ekstensiGambar;

        move_uploaded_file($tmp, 'img/icon/'. $namafilebaru);

        return $namafilebaru;
    }
    // Function Upload Gambar End

    // Get Kategori For Input
    $queryKategori = "SELECT * FROM kategori";
    $resKategori = mysqli_query($conn, $queryKategori);
    // Kategori For Input End

    // Read Data Handler Start
    $queryindex = "SELECT * FROM datalokasi JOIN kategori ON datalokasi.kategori_kategori = kategori.id_kategori";
    $resMap = mysqli_query($conn, $queryindex);
    $resData = mysqli_query($conn, $queryindex);
    while ($result = mysqli_fetch_array($resMap)) {
        $data[] = array(
            'judul' => $result['judul'],
            'alamat' => $result['alamat'],
            'lat' => $result['lat'],
            'lon' => $result['lon'],
            'gambar' => $result['gambar'],
            'detail' => $result['detail'],
            'icon' => $result['icon'],
            'kategori' => $result['nama_kategori'],
        ); 
    }
    $dataJson = json_encode($data);
    // Read Data Handler End
    

    // Insert Datalokasi Handler Start
    if (isset($_SESSION['login'])) {
        if (isset($_POST['createNew'])){
            $judul = htmlspecialchars($_POST['judul']);
            $alamat = htmlspecialchars($_POST['alamat']);
            $lat = htmlspecialchars($_POST['lat']);
            $lon = htmlspecialchars($_POST['lon']);
            $detail = htmlspecialchars($_POST['detail']);
            $kategori = htmlspecialchars($_POST['kategori']);

            $gambar = upload();

            if (!$gambar) {
                $_SESSION['alert'] = "Data Gagal Ditambahkan. Kesalahan saat mengupload Gambar. Gambar harus Diisi, Gambar harus memiliki format Jpg Jpeg Png, dan ukuran gambar max 10Mb";
                header('location:index.php');
                exit;
            }
        
            $queryInsert = "INSERT INTO `datalokasi` (`judul`, `alamat`, `lat`, `lon`, `gambar`, `detail`, `kategori_kategori`) 
            VALUES ('$judul', '$alamat', '$lat', '$lon', '$gambar', '$detail', '$kategori')";
            if(mysqli_query($conn, $queryInsert)){
                $_SESSION['alert'] = "Data Berhasil Ditambahkan";
                header('location:index.php');
                exit;
            } else {
                $error = mysqli_error($conn);
                $_SESSION['alert'] = "Data Gagal Ditambahkan '$error'";
                header('location:index.php');
                exit;
            };
        }
    }
    // Insert Datalokasi Handler END

    // Edit Data Handler Start
    if (isset($_GET['lhkjiuop'])) {
        $idEditValue = $_GET['lhkjiuop'];
        $queryEdit = "SELECT * FROM datalokasi JOIN kategori ON datalokasi.kategori_kategori = kategori.id_kategori WHERE id = '$idEditValue'";
        $resEdit = mysqli_query($conn, $queryEdit);

        if (isset($_POST['EditData'])) {
            $judul = htmlspecialchars($_POST['judul']);
            $alamat = htmlspecialchars($_POST['alamat']);
            $lat = htmlspecialchars($_POST['lat']);
            $lon = htmlspecialchars($_POST['lon']);
            $detail = htmlspecialchars($_POST['detail']);
            $kategori = htmlspecialchars($_POST['kategori']);
            $gambarlama = htmlspecialchars($_POST['gambarlama']);

            // Cek Apakah user update gambar
            if($_FILES['gambar']['error']===4){
                // Jika user tidak update gambar
                $gambar = $gambarlama;
            } else {
                // Jika user update gambar

                // Ngehapus Gambar Sebelumnya
                $filenametodelete = "img/cover/".$gambarlama;

                if(file_exists($filenametodelete)) {
                    unlink($filenametodelete);
                }
                // Ngehapus Gambar Sebelumnya

                $gambar = upload();
            }

            $queryUpdate = "UPDATE `datalokasi` 
            SET 
            judul = '$judul',
            alamat = '$alamat',
            lat = '$lat',
            lon = '$lon',
            detail = '$detail',
            gambar = '$gambar',
            kategori_kategori = '$kategori' WHERE id = $idEditValue";
        
            if(mysqli_query($conn, $queryUpdate)){
                $_SESSION['alert'] = "Data Berhasil Diubah";
                header('location:index.php');
                exit;
            } else {
                $_SESSION['alert'] = "Data Gagal Diubah";
                header('location:index.php');
                exit;
            };
        }
    }
    // Edit Data Handler End

    // Delete Data Handler Start
    if (isset($_SESSION['login'])) {
        if (isset($_GET['ohaidiha'])) {
            if(isset($_POST['DeleteData'])){
                $idDelete = $_GET['ohaidiha'];
                $queryBuatAmbilFileGambar = "SELECT * FROM datalokasi WHERE id = $idDelete";
                $queryDelete = "DELETE FROM datalokasi WHERE id = $idDelete";

                $resBuatHapusGambar = mysqli_query($conn, $queryBuatAmbilFileGambar);

                while ($row = mysqli_fetch_assoc($resBuatHapusGambar)) {
                    $filenametodelete = "img/cover/".$row['gambar'];
                    if(file_exists($filenametodelete)) {
                        unlink($filenametodelete);
                    }
                }                

                if(mysqli_query($conn, $queryDelete)){
                    $_SESSION['alert'] = "Data Berhasil Dihapus";
                    header('location:index.php');
                    exit;
                } else {
                    $_SESSION['alert'] = "Data Gagal Dihapus";
                    header('location:index.php');
                    exit;
                }
            }
        }        
    }
    // Delete Data Handler End

    // Kategori Data Handler Start
    $queryKategoriTable = "SELECT * FROM kategori";
    $resKategoriTable = mysqli_query($conn, $queryKategoriTable);
    // Kategori Data Handler End

    // Kategori Input Start
    if (isset($_SESSION['login'])) {
        if (isset($_POST['inputkategori'])) {
            $nama_kategori = $_POST['nama_kategori'];
            $deskripsi = $_POST['deskripsi'];

            $icon = uploadIcon();

            if (!$icon) {
                $_SESSION['alert'] = "Data Gagal Ditambahkan. Kesalahan saat mengupload Icon. Icon harus Diisi, Icon harus memiliki format Jpg Jpeg Png, dan ukuran Icon max 10Mb";
                header('location:index.php');
                exit;
            }

            $queryInsertKategori = "INSERT INTO `kategori` (`nama_kategori`, `icon`, `deskripsi`) 
            VALUES ('$nama_kategori', '$icon', '$deskripsi')";

            if(mysqli_query($conn, $queryInsertKategori)){
                $_SESSION['alert'] = "Data Berhasil Ditambahkan";
                header('location:index.php');
                exit;
            } else {
                $error = mysqli_error($conn);
                $_SESSION['alert'] = "Data Gagal Ditambahkan $error";
                header('location:index.php');
                exit;
            };   
        }
    }
    // Kategori Input End

    // Kategori Edit Handler Start
    if (isset($_SESSION['login'])) {
        if (isset($_GET['editkategoriez'])) {
            $idEditValueKategori = $_GET['editkategoriez']; 
            $queryEditKategori = "SELECT * FROM kategori WHERE id_kategori = '$idEditValueKategori'";
            $resEditKategori = mysqli_query($conn, $queryEditKategori);

            if (isset($_POST['submiteditkategori'])) {
                $nama_kategori = htmlspecialchars($_POST['nama_kategori']);
                $deskripsi = htmlspecialchars($_POST['deskripsi']);
                $iconlama = htmlspecialchars($_POST['iconlama']);


                $icon = htmlspecialchars($_POST['icon']);

                // Cek Apakah user update icon
                if($_FILES['icon']['error']===4){
                    // Jika user tidak update icon
                    $icon = $iconlama;
                } else {
                    // Jika user update icon

                    // Ngehapus icon Sebelumnya
                    $filenametodelete = "img/icon/".$iconlama;

                    if(file_exists($filenametodelete)) {
                        unlink($filenametodelete);
                    }
                    // Ngehapus icon Sebelumnya

                    $icon = uploadIcon();
                }


                $queryUpdateKategori = "UPDATE `kategori` 
                SET 
                nama_kategori = '$nama_kategori',
                icon = '$icon',
                deskripsi = '$deskripsi' WHERE id_kategori = $idEditValueKategori";

                if (mysqli_query($conn, $queryUpdateKategori)) {
                    $_SESSION['alert'] = "Data Berhasil Diubah";
                    header('location:index.php');
                    exit;
                } else {
                    $error = mysqli_error($conn);
                    $_SESSION['alert'] = "Gagal Mengubah Data $error";
                    header('location:index.php');
                    exit;
                }
            }
        }
    }
    // Kategori Edit Handler End

    // Hapus Kategori Start
    if (isset($_SESSION['login'])) {
        if (isset($_GET['hapuskategoriezzzs'])) {
            if(isset($_POST['DeleteDataKategori'])){
                $idDeleteKategori = $_GET['hapuskategoriezzzs'];
                $default = "-";

                // Menghapus Tabel datalokasi yang berkategori ini
                $queryDeleteTableKategoriNya = "DELETE FROM datalokasi WHERE kategori_kategori = $idDeleteKategori";
                // Menghapus Tabel datalokasi yang berkategori ini

                $queryAmbilNamaFileBuatDiDelete = "SELECT * FROM kategori WHERE id_kategori = $idDeleteKategori";

                $resBuatHapusGambar = mysqli_query($conn, $queryAmbilNamaFileBuatDiDelete);

                while ($row = mysqli_fetch_assoc($resBuatHapusGambar)) {
                    $filenametodelete = "img/icon/".$row['icon'];
                    if(file_exists($filenametodelete)) {
                        unlink($filenametodelete);
                    }
                }        

                mysqli_query($conn, $queryDeleteTableKategoriNya);

                $queryDelete = "DELETE FROM kategori WHERE id_kategori = $idDeleteKategori";

                if(mysqli_query($conn, $queryDelete)){
                    $_SESSION['alert'] = "Data Berhasil Dihapus";
                    header('location:index.php');
                    exit;
                } else {
                    $error = mysqli_error($conn);
                    $_SESSION['alert'] = "Data Gagal Dihapus $error";
                    header('location:index.php');
                    exit;
                }
            }
        }        
    }
    // Hapus Kategori END
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                }
            }
        }
    </script>
    <?php
        include('components/style.php')
    ?>
    <title>Webgis Kota Serang</title>
</head>
<body class="h-screen">
    <section>

        <div class="absolute z-[999] gap-10 py-2 px-4 items-center shadow-2xl bg-blue-200 rounded-b-xl w-full flex border justify-between">
            <h1 class="md:text-xl font-extrabold">WEBGIS KOTA SERANG</h1>
            <div>
            <?php if(!isset($_SESSION['login'])) : ?>
                <a href="admin.php" class=" inline-block py-2 px-4 bg-blue-800 text-white rounded-md">Login</a>
            <?php else :?>
                <form action="" method="POST">
                    <button type="submit" name="logout" class=" inline-block py-2 px-4 bg-red-800 text-white rounded-md">Logout</button>
                </form>
            <?php endif?>
            </div>
        </div>

        <div id="map" class="absolute w-full h-screen z-10"></div>

        <div id="barContainer" class="fixed z-20 bottom-0 w-full h-[70%] bg-blue-200 transition rounded-xl translate-y-full">
            <div id="barHandler" onclick="barHandlerClick()" class="-top-4 hover:cursor-pointer -translate-y-[20px] mx-auto right-0 z-[999] transition left-0 bg-blue-300 rounded-lg absolute w-[20%] flex justify-center">
                <div>
                    <span class="bg-black w-[30px] h-[2px] block my-2"></span>
                    <span class="bg-black w-[30px] h-[2px] block my-2"></span>
                    <span class="bg-black w-[30px] h-[2px] block my-2"></span>
                </div>
            </div>
            <div class="pb-10 w-full bg-blue-200 relative -z-10 py-10">
                <div id="close" onclick="barHandlerClick()" class="hover:cursor-pointer absolute right-5 top-5">
                    <img src="close.png" class="w-8" alt="close">
                </div>

                <div class="lg:flex w-full h-full">

                    <div class="
                    <?php if(isset($_SESSION['login'])) : ?>
                    lg:w-[60%] 
                    <?php else : ?>
                    w-full
                    <?php endif ?>
                    px-6">
                        <h1 class="text-center pb-4 text-xl md:text-2xl font-semibold">Data</h1>
                        <div class="overflow-x-auto w-full mb-6">

                            <!-- Table data lokasi Start -->
                            <table style="width: 1500px;" id="datalokasi" class="mx-auto my-4">
                                <thead>
                                    <tr class="[&>*]:px-4 [&>*]:py-4 bg-blue-900 text-white">
                                        <th>Judul</th>
                                        <th>Alamat</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Gambar</th>
                                        <th>Detail</th>
                                        <th>Kategori</th>
                                        <?php if(isset($_SESSION['login'])) : ?>
                                        <th>Aksi</th>
                                        <?php endif ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($rows = mysqli_fetch_assoc($resData)):?>
                                        <tr class="[&>*]:px-4 [&>*]:py-4 odd:bg-blue-50 even:bg-blue-100">
                                            <td><?=$rows['judul']?></td>
                                            <td><?=$rows['alamat']?></td>
                                            <td><?=$rows['lat']?></td>
                                            <td><?=$rows['lon']?></td>
                                            <td class="w-[250px]"><img src="img/cover/<?=$rows['gambar']?>" alt="<?=$rows['gambar']?>" class="w-full rounded-md brightness-[.70]"></td>
                                            <td><?=$rows['detail']?></td>
                                            <td class="text-center"><span class="font-bold"><?=$rows['nama_kategori']?></span> <br> <button onclick="kategoriDetailClick(`<?=$rows['nama_kategori']?>`, `<?=$rows['deskripsi']?>`)" class="inline-block mt-2 py-2 px-4 bg-green-600 text-white rounded-lg">Detail <?=$rows['nama_kategori']?></button></td>
                                            <?php if(isset($_SESSION['login'])) :?>
                                                <td>
                                                    <a href="index.php?lhkjiuop=<?=$rows['id']?>" class="inline-block my-1 py-2 px-4 bg-yellow-600 rounded-lg text-white">Edit</a>
                                                    <form action="index.php?ohaidiha=<?=$rows['id']?>" method="POST">
                                                        <button name="DeleteData" type="submit" onclick="return confirm(`Apakah anda yakin menghapus data <?=$rows['judul']?>?`)"  class="rounded-lg my-1 inline-block py-2 px-4 bg-red-600 text-white">Hapus</button>
                                                    </form>
                                                </td>
                                            <?php endif ?>
                                        </tr>
                                    <?php endwhile ?>
                                </tbody>
                            </table>
                            <!-- Table data lokasi end -->

                        </div>

                        <!-- Table kategori Start -->
                        <?php if(isset($_SESSION['login'])) : ?>
                            <h1 class="text-center pb-4 text-xl md:text-2xl font-semibold">Kategori</h1>
                            <div class="overflow-x-auto w-full">
                            <table style="width: 1500px;" id="kategoritable" class="mx-auto my-4">
                                <thead>
                                    <tr class="[&>*]:px-4 [&>*]:py-4 bg-blue-900 text-white">
                                        <th>Kategori</th>
                                        <th>Icon</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($rows = mysqli_fetch_assoc($resKategoriTable)):?>
                                        <tr class="[&>*]:px-4 [&>*]:py-4 odd:bg-blue-50 even:bg-blue-100">
                                            <td><?=$rows['nama_kategori']?></td>
                                            <td class="w-[75px]"><img src="img/icon/<?=$rows['icon']?>" alt="<?=$rows['icon']?>" class="w-full rounded-md brightness-[.70]"></td>
                                            <td><?=$rows['deskripsi']?></td>
                                                <td>
                                                    <a href="index.php?editkategoriez=<?=$rows['id_kategori']?>" class="inline-block my-1 py-2 px-4 bg-yellow-600 text-white rounded-lg">Edit</a>
                                                    <form action="index.php?hapuskategoriezzzs=<?=$rows['id_kategori']?>" method="POST">
                                                        <button name="DeleteDataKategori" type="submit" onclick="return confirm(`PERHATIAN!! Menghapus Data Kategori <?=$rows['nama_kategori']?> akan menghapus seluruh data lokasi yang memiliki kategori <?=$rows['nama_kategori']?>`)"  class=" my-1 inline-block py-2 px-4 bg-red-600 text-white rounded-lg">Hapus</button>
                                                    </form>
                                                </td>
                                        </tr>
                                    <?php endwhile ?>
                                </tbody>
                            </table>
                            </div>
                        <?php endif ?>
                        <!-- Table Kategori End -->

                    </div>

                    <!-- Input Edit Start -->
                    <?php if(isset($_SESSION['login'])) : ?>
                        <?php if(isset($_GET['lhkjiuop'])) : ?>
                            <div id="formInput" class="lg:w-[40%] lg:pt-0 pt-10 w-full lg:border-l-2 border-black px-6 ">
                                <h1 class="text-center pb-4 text-xl md:text-2xl font-semibold">Edit Data WEBGIS</h1>
                                <?php while($row=mysqli_fetch_assoc($resEdit)) : ?>
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="gambarlama" value="<?=$row['gambar']?>">
                                        <div class="relative z-0 mb-6 w-full group">
                                            <input value="<?=$row['judul']?>" required type="text" id="judul" name="judul" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                            <label id="judullabel" for="judul" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Judul</label>
                                        </div>
                                        <div class="relative z-0 mb-6 w-full group">
                                            <input value="<?=$row['alamat']?>" required type="text" id="alamat" name="alamat" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                            <label id="alamatlabel" for="alamat" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Alamat</label>
                                        </div>
                                        <div class="relative z-0 mb-6 w-full group">
                                            <input value="<?=$row['lat']?>" required type="text" id="lat" name="lat" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                            <label id="latlabel" for="lat" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Latitude</label>
                                        </div>
                                        <div class="relative z-0 mb-6 w-full group">
                                            <input value="<?=$row['lon']?>" required type="text" id="lon" name="lon" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                            <label id="lonlabel" for="lon" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Longitude</label>
                                        </div>
                                        <div class="mb-6">
                                            <h5 class="pb-2 ">Gambar:</h5>
                                            <h5 class="pb-2 text-slate-500">Gambar Sebelumnya:</h5>
                                            <img class="w-[80%] mx-auto rounded-md brightness-[.85] mb-2" src="img/cover/<?=$row['gambar']?>" alt="">
                                            <label class="block">
                                                <input name="gambar" type="file" class="block w-full text-sm text-slate-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-violet-50 file:text-violet-700
                                                hover:file:bg-violet-100
                                                "/>
                                            </label>
                                            <h6 class="text-xs text-slate-500">Abaikan jika tidak ingin mengubah gambar</h6>
                                        </div>
                                        <div class="mb-6">
                                            <label for="kategoriinput">Kategori :</label>
                                            <select class="py-2 px-4 block w-full text-white bg-green-600 rounded-md" name="kategori" id="kategoriinput">
                                                <?php while($rows = mysqli_fetch_assoc($resKategori)) : ?>
                                                <option class="" value="<?=$rows['id_kategori']?>" <?php if($row['id_kategori'] == $rows['id_kategori']){echo'selected';} ?> ><?=$rows['nama_kategori']?></option>
                                                <?php endwhile ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="detail">Detail :</label>
                                            <textarea id="textareaedit" required name="detail" id="detail" class="w-full bg-blue-300 rounded-md px-2 py-4" rows="10"></textarea>
                                            <script>
                                                document.getElementById('textareaedit').value='<?=$row['detail']?>'
                                            </script>
                                        </div>
                                        
                                        <button name="EditData" type="submit" class="block my-4 text-white bg-blue-800 py-2 px-4 text-sm rounded-md">Save!</button>
                                        <a href="index.php" class="inline-block my-4 text-white bg-red-800 py-2 px-4 text-sm rounded-md">Cancel Edit</a>
                                    </form>
                                <?php endwhile ?>
                            </div>
                        <?php else : ?>
                            <div id="formInput" class="lg:w-[40%] w-full lg:pt-0 pt-10 lg:border-l-2 border-black px-6 ">
                                <h1 class="text-center pb-4 text-xl md:text-2xl font-semibold">Input Data WEBGIS</h1>
                                <form method="POST" action="" enctype="multipart/form-data">
                                    
                                    <div class="relative z-0 mb-6 w-full group">
                                        <input required type="text" id="judul" name="judul" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                        <label for="judul" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Judul</label>
                                    </div>
                                    <div class="relative z-0 mb-6 w-full group">
                                        <input required type="text" id="alamat" name="alamat" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                        <label for="alamat" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Alamat</label>
                                    </div>
                                    <div class="relative z-0 mb-6 w-full group">
                                        <input required type="text" id="lat" name="lat" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                        <label for="lat" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Latitude</label>
                                    </div>
                                    <div class="relative z-0 mb-6 w-full group">
                                        <input required type="text" id="lon" name="lon" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                        <label for="lon" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Longitude</label>
                                    </div>
                                    <!-- <div class="relative z-0 mb-6 w-full group">
                                        <input required type="text" id="gambar" name="gambar" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                        <label for="gambar" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Gambar</label>
                                    </div> -->
                                    <div class="mb-6">
                                        <h5>Gambar:</h5>
                                        <label class="block">
                                            <input name="gambar" type="file" class="block w-full text-sm text-slate-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-violet-50 file:text-violet-700
                                            hover:file:bg-violet-100
                                            "/>
                                        </label>
                                    </div>
                                    <div class="mb-6">
                                        <label for="kategoriinput">Kategori :</label>
                                        <select class="py-2 px-4 block w-full text-white bg-green-600 rounded-md" name="kategori" id="kategoriinput">
                                            <?php while($row = mysqli_fetch_assoc($resKategori)) : ?>
                                            <option class="" value="<?=$row['id_kategori']?>"><?=$row['nama_kategori']?></option>
                                            <?php endwhile ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="detail">Detail :</label>
                                        <textarea required name="detail" id="detail" class="w-full bg-blue-300 rounded-md px-2 py-4" rows="10"></textarea>
                                    </div>
                                    <button name="createNew" type="submit" class="block my-4 text-white bg-blue-800 py-2 px-4 text-sm rounded-md">Save!</button>
                                </form>
                            </div>
                        <?php endif ?>
                    <?php endif?>
                    <!-- Input End -->

                </div>
                
                <?php if(isset($_SESSION['login'])) : ?>
                    <?php if(!isset($_GET['editkategoriez'])) : ?>
                        <!-- Form Input Kategori Start -->
                        <div class="pt-6 px-4 md:px-6">
                            <h1 class="text-center pb-4 text-xl md:text-2xl font-semibold">Input Data Kategori</h1>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="relative z-0 mb-6 w-full group">
                                    <input required type="text" id="nama_kategori" name="nama_kategori" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                    <label for="nama_kategori" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama Kategori</label>
                                </div>
                                <!-- <div class="relative z-0 mb-6 w-full group">
                                    <input required type="text" id="icon" name="icon" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                    <label for="icon" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Icon</label>
                                </div> -->
                                <div class="mb-6">
                                    <h5>Icon</h5>
                                    <label class="block">
                                        <input name="icon" type="file" class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-violet-50 file:text-violet-700
                                        hover:file:bg-violet-100
                                        "/>
                                    </label>
                                </div>
                                <div>
                                    <label for="deskripsi">Deskripsi: </label>
                                    <textarea required name="deskripsi" id="deskripsi" class="w-full bg-blue-300 rounded-md px-2 py-4" rows="10"></textarea>
                                </div>
                                <button type="submit" name="inputkategori" class="bg-blue-600 text-white block text-center px-4 py-2 w-full mt-4">Simpan!</button>
                            </form>
                        </div>
                        <!-- Form Input Kategori End -->
                        <?php else : ?>
                        <!-- Form Edit Kategori Start -->
                        <?php while($row = mysqli_fetch_assoc($resEditKategori)) : ?>
                            <div class="pt-6 px-4 md:px-6">
                                <h1 class="text-center pb-4 text-xl md:text-2xl font-semibold">Edit Data Kategori</h1>
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="iconlama" value="<?=$row['icon']?>">
                                    <div class="relative z-0 mb-6 w-full group">
                                        <input value="<?=$row['nama_kategori']?>" required type="text" id="nama_kategori" name="nama_kategori" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-500 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                        <label for="nama_kategori" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama Kategori</label>
                                    </div>
                                    <div class="mb-6">
                                    <h5 class="pb-2">Icon</h5>
                                    <h5 class="pb-2 text-slate-500">Icon Sebelumnya:</h5>
                                    <img class="w-[20%] mx-auto rounded-md brightness-[.85] mb-2" src="img/icon/<?=$row['icon']?>" alt="">
                                    <label class="block">
                                        <input name="icon" type="file" class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-violet-50 file:text-violet-700
                                        hover:file:bg-violet-100
                                        "/>
                                    </label>
                                    <h6 class="text-xs text-slate-500">Abaikan jika tidak ingin mengubah icon</h6>
                                </div>
                                    <div>
                                        <label for="deskripsi">Deskripsi: </label>
                                        <textarea required name="deskripsi" id="textareaeditdeskripsi" class="w-full bg-blue-300 rounded-md px-2 py-4" rows="10"></textarea>
                                        <script>
                                            document.getElementById('textareaeditdeskripsi').value='<?=$row['deskripsi']?>'
                                        </script>
                                    </div>
                                    <button type="submit" name="submiteditkategori" class="bg-blue-600 text-white block text-center px-4 py-2 w-full mt-4 hover:bg-blue-800">Simpan!</button>
                                    <a onclick="window.location.href = 'index.php'" class="bg-red-600 text-white block text-center px-4 py-2 w-full mt-4 hover:cursor-pointer hover:bg-red-800">Cancel Edit</a>                  
                                </form>
                            </div>
                        <?php endwhile ?>
                        <!-- Form Edit Kategori End -->
                    <?php endif ?>
                <?php endif ?>

            </div>
        </div>
    </section>
    

    
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#datalokasi').DataTable({
                lengthMenu: [
                [7, 14, 25, 35, -1],
                [7, 14, 25, 35, 'All'],
            ],
            });
        });

        $(document).ready(function () {
            $('#kategoritable').DataTable({
                lengthMenu: [
                [7, 14, 25, 35, -1],
                [7, 14, 25, 35, 'All'],
            ],
            });
        });
    </script>
    <script>
        <?php if(isset($_SESSION['alert'])) : ?>
            alert("<?=$_SESSION['alert']?>")

            setTimeout(() => {
            <?php
            unset($_SESSION['alert'])
            ?>  
            }, 1000);
        <?php endif ?>
    </script>
    <script type="text/javascript">const dataJson = <?= $dataJson ?>;</script>
    <script type="text/javascript" src="index.js"></script>
</body>
</html>