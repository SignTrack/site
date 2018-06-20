<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!move_uploaded_file($_FILES["file"]["tmp_name"], '../upload/'.$_POST["inventory_id"].'.csv')){
    echo -1;
}else{
    echo 1;
}
?>
