<?php
require_once("database.php");

if ($_POST["submit"]) {

  $time = $_POST["text"];
  $name = $_POST["name"];
  $sector = $_POST["sector"];

  $target_dir = "uploads/";
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
  // Check if image file is a actual image or fake image
  if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
      echo "File is an image - " . $check["mime"] . ".";
      $uploadOk = 1;
    } else {
      echo "File is not an image.";
      $uploadOk = 0;
    }
  }
  if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
  }
  if ($uploadOk == 1) {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      echo "<br>File uploaded successfully.";
    } else {
      echo "<br>There was an error uploading your file.";
    }
  }
}

$fileName = $_FILES["fileToUpload"]["tmp_name"];
print_r($fileName); 

if(isset($_POST['submit'])){
    $sql="insert into current stock(time,name,sector) values('$time','$name','$sector')";
    $query=mysqli_query($dsn,$sql);
}
?>