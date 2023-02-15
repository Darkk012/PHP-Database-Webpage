<?php
$table=$_POST["tabla"];
$conn = new mysqli("localhost", "root", "", "nagybeadando");
if ($conn->connect_error) {
   die("Nem sikerült az adatbázishoz csatlakozni!");
}

$sql="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$table."'";
$result = $conn->query($sql);
$sor=array();
while ($row = $result->fetch_assoc()) {
   array_push($sor,$row["COLUMN_NAME"]);
}

echo '<input type="hidden" value="'.$table.'" name="tabla">';
if($table=="adatok"){
   $m=count($sor);
}else{
   $m=count($sor)-1;
}
for($i=1;$i<$m;$i++){
   echo $sor[$i];
   echo '<input type="text" name="'.$sor[$i].'">';
   echo "<br>";
}
   echo '<input type="submit" value="Küldés" name="kuldes">';

$conn->close();