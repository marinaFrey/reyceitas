<?php
// // header("Access-Control-Allow-Origin: *");
// // header("Access-Control-Allow-Headers: *");
// // header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// // header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
// header('Access-Control-Max-Age: 1000');
// header('Access-Control-Allow-Credentials: true');
// // header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
// header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, Authorization, X-Requested-With');



header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');






require 'login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // You have to be logged with a valid user to upload files.
    if(!is_logged_as_a_valid_user()) {
        error_log("NOT LOGGED, NO UPLOAD");
        http_response_code(403);
        die();
    }

    if (isset($_FILES['files'])) {
        $errors = [];
        $path = 'uploads/';
        $extensions = ['jpg', 'jpeg', 'png', 'gif'];

        $all_files = count($_FILES['files']['tmp_name']);

        for ($i = 0; $i < $all_files; $i++) {
            $file_name = basename($_FILES['files']['name'][$i]);
            $new_file_name = basename($_POST['filenames'][$i]);
            $file_tmp = ($_FILES['files']['tmp_name'][$i]);
            $file_type = $_FILES['files']['type'][$i];
            $file_size = $_FILES['files']['size'][$i];
            $file_ext = strtolower(end(explode('.', $_FILES['files']['name'][$i])));

            $file = $path . $new_file_name;

            if (!in_array($file_ext, $extensions)) {
                $errors[] = 'Extension not allowed: ' . $file_name . ' ' . $file_type;
            }

            if ($file_size > 2097152) {
                $errors[] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
            }

            if (empty($errors)) {
                move_uploaded_file($file_tmp, $file);
            }
        }

        if ($errors) print_r($errors);
    }
}
?>
