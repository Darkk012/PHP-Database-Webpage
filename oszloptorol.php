<?php
$table = $_POST["tabla"];
$oszlop= $_POST["oszlop"];
$conn = new mysqli("localhost", "root", "", "nagybeadando");
if ($conn->connect_error) {
   die("Nem sikerült az adatbázishoz csatlakozni!");
}

$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $table . "'";
$result = $conn->query($sql);
$sor = array();
while ($row = $result->fetch_assoc()) {
   array_push($sor, $row["COLUMN_NAME"]);
}
$sql="ALTER TABLE ".$table." DROP COLUMN ".$sor[$oszlop];
$result = $conn->query($sql);
$conn->close();