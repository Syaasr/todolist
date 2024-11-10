<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "todo_db";
try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
}
catch(mysqli_sql_exception){
    echo"Tidak bisa tersambung <br>";
}
?>
