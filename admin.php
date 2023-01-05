<?php
include('connection.php');
session_start();

if (isset($_SESSION["login"])) {
    header("Location:index.php");
    exit;
}

// Signup Handler Start
if(isset($_POST['signup'])) {
    // $token = "iniTokenBuatSignupAkunUas23#";
    $token = mysqli_fetch_assoc(mysqli_query($conn, "SELECT token FROM token"));
    $tokenInput = md5($_POST['token']);


    if ($tokenInput == $token['token']) {
        $uname = $_POST["username"];
        $password = $_POST["password"];
    
        $querysignup = "INSERT INTO `user` (`username`, `password`) VALUES ('$uname', md5('$password'))";
    
        // $signup = mysqli_num_rows(); 
    
        if(mysqli_query($conn, $querysignup)){
            session_start();
            $_SESSION['user'] = $uname;
            $_SESSION['alert'] = "Berhasil mendaftarkan akun! Silahkan Login dengan username dan password yang telah didaftarkan!";
            header('location:admin.php');
            exit;
        } else {
            $_SESSION['alert'] = "Error";
            exit;
        }
    } else {
        $_SESSION['alert'] = "Maaf anda tidak boleh mendaftar akun, karna token hanya dimiliki dosen penilai! dan beberapa orang";
    }

}
// Signup Handler End


// Login Handler Start
if(isset($_POST['login'])) {
    $uname = $_POST["username"];
    $password = $_POST["password"];

    $querylogin = "SELECT * FROM user WHERE username = '$uname' and password = md5('$password')" ;

    $login = mysqli_num_rows(mysqli_query($conn, $querylogin)); 

    if($login){
        session_start();
        $_SESSION['user'] = $uname;
        $_SESSION['login'] = true;
        header('location:index.php');
        exit;
    } else {
        $_SESSION['alert'] = "Username atau Password Salah";
        header('location:admin.php');
        exit;
    }
}
// Login Handler End
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website UTS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                screens:{
                'ip4': '420px',
                'sm': '640px',
                'md': '768px',
                'lg': '1024px',
                'xl': '1280px',
                '2xl': '1536px',
                },
                extend: {
                boxShadow: {
                    '3xl': '15px 15px 15px 10px rgba(0, 0, 0, 0.3)',
                },
                }
            }
        }
    </script>
    <style>
        #inputtext:focus + *{
            transform: translateY(-50%);
            color: #3b82f6;
        }
    </style>
</head>
<body>
    <section class="flex justify-center items-center w-full h-screen">
        <div class="mx-8 w-full max-w-[400px] h-[80%] shadow-lg border border-slate-200 text-sm md:text-base lg:text-lg rounded-xl">            

            <?php if(!isset($_GET['signup'])) : ?>
                <h1 class="text-center py-3 text-lg md:text-xl lg:text-2xl font-semibold tracking-widest italic pb-4">Login</h1>
                <form action="" method="POST">
                    <div class="w-[80%] mx-auto relative h-20">
                        <input name="username" placeholder=" " type="text" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" autofocus>
                        <label id="username" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Username</label>
                    </div>
                    <div class="w-[80%] mx-auto relative h-20">
                        <input name="password" placeholder=" " type="password" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer ">
                        <label id="password" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Password</label>
                    </div>
                    <button type="submit" name="login" class="w-[90%] bg-blue-500 py-2 mx-auto block text-white" 
                    onsubmit="
                        inputuname.value = ''
                        inputpass.value = ''"
                    >Sign-In!</button>
                    <div
                        class="flex px-4 text-slate-500 text-xs md:text-sm items-center my-4 before:flex-1 before:border-t before:border-gray-300 before:mt-0.5 after:flex-1 after:border-t after:border-gray-300 after:mt-0.5"
                    >
                        <p class="text-center font-semibold mx-4 mb-0">Belum mempunyai akun?</p>
                    </div>
                    <a href="admin.php?signup=yes" class="bg-green-600 text-white py-2 w-[90%] mx-auto block text-center">Daftar akun</a>
                </form>
            <?php else : ?>
                <h1 class="text-center py-3 text-lg md:text-xl lg:text-2xl font-semibold tracking-widest italic pb-4">Registrasi</h1>
                <form action="" method="POST">
                    <div class="w-[80%] mx-auto relative h-20">
                        <input name="username" required placeholder=" " type="text" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" autofocus>
                        <label id="username" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Username</label>
                    </div>
                    <div class="w-[80%] mx-auto relative h-20">
                        <input name="password" required placeholder=" " type="password" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer ">
                        <label id="password" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Password</label>
                    </div>
                    <div class="relative z-0 mb-6 w-[80%] mx-auto group">
                        <input required type="text" id="token" name="token" placeholder=" " class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        <label id="judullabel" for="token" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Token</label>
                    </div>
                    <button type="submit" name="signup" class="w-[90%] bg-blue-500 py-2 mx-auto block text-white" 
                    onsubmit="
                        inputuname.value = ''
                        inputpass.value = ''"
                    >Daftar!</button>
                    <div
                        class="flex px-4 text-slate-500 text-xs md:text-sm items-center my-4 before:flex-1 before:border-t before:border-gray-300 before:mt-0.5 after:flex-1 after:border-t after:border-gray-300 after:mt-0.5"
                    >
                        <p class="text-center font-semibold mx-4 mb-0">Sudah Mempunyai Akun? Coba</p>
                    </div>
                    <a href="admin.php" class="bg-green-600 text-white py-2 w-[90%] mx-auto block text-center">Login</a>
                </form>
            <?php endif ?>
        </div>
    </section>

    <script>
        <?php if(isset($_SESSION['alert'])) : ?>
            alert("<?=$_SESSION['alert']?>")
            <?php
            unset($_SESSION['alert'])
            ?>
        <?php endif ?>
    </script>
</body>
</html>