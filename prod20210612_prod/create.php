<?php
// var_dump($_POST);
// exit();

    $name = $_POST["name"];
    $year = $_POST["year"];
    $email = $_POST["email"];
    $text = $_POST["text"];
    // $text2 = $_POST["text"];



    // $write_data = "{$name},{$year},{$email},{$text},{$text2}\n";
    $write_data = "{$name},{$year},{$email},{$text}\n";

    $file = fopen('./data/member.csv', 'a');

    flock($file, LOCK_EX);
    fwrite($file, $write_data);
    flock($file, LOCK_UN);
    fclose($file);

    header("Location:prod.php");
?>