<?php
$table=$_POST["tabla"];
$conn = new mysqli("localhost", "root", "", "nagybeadando");
if ($conn->connect_error) {
   die("Nem sikerült az adatbázishoz csatlakozni!");
}

$sql="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$table."'";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
   echo '<option value="' . $row["COLUMN_NAME"] . '">' . $row["COLUMN_NAME"] . '</option>';
}