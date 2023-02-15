<?php
session_start();
$id=$_POST["id"];
$table=$_POST["tabla"];
$jog=$_POST["jog"];
$conn = new mysqli("localhost", "root", "", "nagybeadando");
if ($conn->connect_error) {
   die("Nem sikerült az adatbázishoz csatlakozni!");
}
$log=fopen('log.txt','a');
if($jog==1){
   $sql="DELETE FROM ".$table." WHERE id=".$id ;
   $conn->query($sql);
   $txt=$_SESSION["nev"]." A ".$table." táblában ".$id." idü értéket töröltt! \n";
}else if($jog==2){
   $sql="SELECT * FROM ".$table." WHERE id=".$id ;
   $result=$conn->query($sql);
   $row = $result->fetch_assoc();
   if($row["kie"]==$_SESSION["id"]){
      $sql="DELETE FROM ".$table." WHERE id=".$id ;
      $conn->query($sql);
      $txt=$_SESSION["nev"]." A ".$table." táblában ".$id." idü értéket töröltt! \n";
   }else{
      $txt=$_SESSION["nev"]." A ".$table." táblában ".$id." idü értéket nem tudja törölni! \n";
   }
}

fwrite($log, $txt);  
fclose($log);      
$conn->close();