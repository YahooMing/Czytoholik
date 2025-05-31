<?php
$server = 'localhost';
$username = 'root';
$password = '';
$database = 'biblioteka';
$conn = new mysqli($server,$username,$password,$database);

if($conn->connect_errno){
    die("ERROR: nie udało sie połączyć z bazą danych");
}
?>