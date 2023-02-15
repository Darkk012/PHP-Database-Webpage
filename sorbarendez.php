<?php
$table=$_POST["tabla"];
$szam=$_POST["sor"];
$jog = $_POST["jog"];
$sorrend=$_POST["hanyszor"];
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
   
if($sorrend==0){
    $sql = "SELECT * FROM ".$table." ORDER BY ".$sor[$szam]." ASC";
}else{
    $sql = "SELECT * FROM ".$table." ORDER BY ".$sor[$szam]." DESC";
}
$result = $conn->query($sql);

if ($jog == 1) {
   $i = 0;
   echo "<tr>";
   while ($i < count($sor)) {
      echo '<th onclick="sorba(' . $i . ')">' . $sor[$i++] . '</th>';
   }
   echo "</tr>";

   while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      for ($i = 0; $i < count($sor); $i++) {
         echo '<td>' . $row[$sor[$i]] . '</td>';
      }
      echo '<td><button onclick="modosit(' . $row["id"] . ')">Módosít</button><button onclick="torol(' . $row["id"] . ')">Töröl</button></td>';
      echo "</tr>";
   }
   if ($table != "adatok") {
      echo "<tr>";
      for ($j = 0; $j < count($sor); $j++) {
         echo '<td><button onclick="otorol(' . $j . ')">Oszlop Törlés</button></td>';
      }
      echo "</tr>";
      echo '<tr><td><button onclick="hozzaad()">Hozzáadás</button><button onclick="ohozzaad()">Oszlop Hozzáadás</button></td></tr>';
   } else {
      echo '<tr><td><button onclick="hozzaad()">Hozzáadás</button> </td></tr>';
   }
} else if ($jog == 2) {
   $i = 0;
   echo "<tr>";
   while ($i < count($sor)) {
      echo '<th onclick="sorba(' . $i . ')">' . $sor[$i++] . '</th>';
   }
   echo "</tr>";
   while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      for ($i = 0; $i < count($sor); $i++) {
         echo '<td>' . $row[$sor[$i]] . '</td>';
      }
      echo '<td><button onclick="modosit(' . $row["id"] . ')">Módosít</button><button onclick="torol(' . $row["id"] . ')">Töröl</button></td>';
      echo "</tr>";
   }
   echo '<tr><td><button onclick="hozzaad()">Hozzáadás</button> </td></tr>';
} else if ($jog == 3) {
   $i = 0;
   echo "<tr>";
   while ($i < count($sor) - 1) {
      echo '<th onclick="sorba(' . $i . ')">' . $sor[$i++] . '</th>';
   }
   echo "</tr>";
   while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      for ($i = 0; $i < count($sor) - 1; $i++) {
         echo '<td>' . $row[$sor[$i]] . '</td>';
      }
      echo "</tr>";
   }
}

$conn->close();
