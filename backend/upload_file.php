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

    // You have to be logged with a validated user to upload.
    $auth = get_current_authentication_level();
    if($auth == NULL || $auth < 1) {
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
                http_response_code(415);
                die();
            }

            if ($file_size > 2097152) {
                $errors[] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
                http_response_code(413);
                die();
            }

            if (empty($errors)) {
                move_uploaded_file($file_tmp, $file);
            }
        }

        if ($errors) {
            print_r($errors);
        } else {
            print_r($file);
        }



    }
}
?>
