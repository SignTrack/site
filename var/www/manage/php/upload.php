<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");

move_uploaded_file($_FILES["file"]["tmp_name"], '/var/www/manage/images/upload/'. basename($_FILES["file"]["name"]));
?>
