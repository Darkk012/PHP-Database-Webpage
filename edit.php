<?php
session_start();
$id=$_POST["id"];
$table=$_POST["tabla"];
$jog=$_POST["jog"];
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

$sql="SELECT * FROM ".$table." WHERE id=".$id;
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if($jog==1){
   echo '<input type="hidden" value="'.$table.'" name="tabla">';
   echo '<input type="hidden" value="'.$id.'" name="id">';
   for($i=1;$i<count($sor);$i++){
      echo $sor[$i];
      echo '<input type="text" value="'.$row[$sor[$i]].'" name="'.$sor[$i].'">';
   
      echo "<br>";
   }
      echo '<input type="submit" value="Küldés" name="send">';
}else if($jog==2){
   if($row["kie"]==$_SESSION["id"]){
      echo '<input type="hidden" value="'.$table.'" name="tabla">';
      echo '<input type="hidden" value="'.$id.'" name="id">';
      for($i=1;$i<count($sor)-1;$i++){
         echo $sor[$i];
         echo '<input type="text" value="'.$row[$sor[$i]].'" name="'.$sor[$i].'">';
      
         echo "<br>";
      }
         echo '<input type="submit" value="Küldés" name="send">';
   }else{
      echo "nem";
   }
}
$conn->close();