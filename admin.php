<?php
session_start();
?>
<html>

<head>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
        }
    </style>

</head>

<body onload="tablafeltolt()">
    <div id="listaz">
        <select id="select" onchange="tablafeltolt()">
            <?php
            $conn = new mysqli("localhost", "root", "", "nagybeadando");
            if ($conn->connect_error) {
                die("Nem sikerült az adatbázishoz csatlakozni!");
            }

            $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='nagybeadando'";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                if ($row["TABLE_NAME"] == "adatok") {
                    echo '<option value="' . $row["TABLE_NAME"] . '" selected>' . $row["TABLE_NAME"] . '</option>';
                } else {
                    echo '<option value="' . $row["TABLE_NAME"] . '">' . $row["TABLE_NAME"] . '</option>';
                }
            }
            $conn->close();
            ?>
        </select>
        <input type="text" id="kereso" onchange="szures()">
        <select id="oszlop" onchange="szures()"></select>
        <button onclick="uj()">Új Táblázat</button>
        <button onclick="ki()">Kijelentkezés</button>
        <table id="tabla" hidden></table>
    </div>

    <div id="modositas" hidden>
        <form id="modosit" action="admin.php" method="POST"></form>
        <?php
        if (isset($_POST['send'])) {
            $conn = new mysqli("localhost", "root", "", "nagybeadando");
            if ($conn->connect_error) {
                die("Nem sikerült az adatbázishoz csatlakozni!");
            }
            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $_POST["tabla"] . "'";
            $result = $conn->query($sql);
            $sor = array();
            while ($row = $result->fetch_assoc()) {
                array_push($sor, $row["COLUMN_NAME"]);
            }

            for ($i = 1; $i < count($sor); $i++) {
                $sql = "UPDATE " . $_POST["tabla"] . " SET ";
                $sql .= $sor[$i];
                $sql .= "='";
                $sql .= $_POST[$sor[$i]] . "' ";
                $sql .= "WHERE id=" . $_POST["id"];
                $conn->query($sql);
            }
            $conn->close();
            header("Location: " . $_SERVER["PHP_SELF"]);
        }
        ?>
    </div>

    <div id="hozzaadas" hidden>
        <form id="hozzaad" action="admin.php" method="POST"></form>
        <?php
        if (isset($_POST["kuldes"])) {
            $conn = new mysqli("localhost", "root", "", "nagybeadando");
            if ($conn->connect_error) {
                die("Nem sikerült az adatbázishoz csatlakozni!");
            }           
            if ($_POST["tabla"] == "adatok") {
                $nev = $_POST["felhasznalo"];
                $sql = "SELECT felhasznalo FROM adatok";
                $result = $conn->query($sql);
                $bentvane = 0;
                while ($row = $result->fetch_assoc()) {
                    if ($nev == $row["felhasznalo"]) $bentvane = 1;
                }
                if ($bentvane == 0) {
                    $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $_POST["tabla"] . "'";
                    $result = $conn->query($sql);
                    $sor = array();
                    while ($row = $result->fetch_assoc()) {
                        array_push($sor, $row["COLUMN_NAME"]);
                    }
                    $sql = "INSERT INTO " . $_POST["tabla"] . "( " . $sor[1];
                    for ($i = 2; $i < count($sor); $i++) {
                        $sql .= ", " . $sor[$i];
                    }
                    $sql .= ") VALUES ( '" . $_POST[$sor[1]] . "' ";
                    for ($i = 2; $i < count($sor); $i++) {
                        $sql .= ", '" . $_POST[$sor[$i]] . "' ";
                    }
                    $sql .= ")";
                    $conn->query($sql);
                    $log=fopen('log.txt','a');
                    $txt=$_SESSION["nev"]." Az adatok táblához egy új adatott adott hozzá \n";
                    fwrite($log, $txt); 
                    fclose($log);
                }
            } else {
                $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $_POST["tabla"] . "'";
                $result = $conn->query($sql);
                $sor = array();
                while ($row = $result->fetch_assoc()) {
                    array_push($sor, $row["COLUMN_NAME"]);
                }
                $sql = "INSERT INTO " . $_POST["tabla"] . "( " . $sor[1];
                for ($i = 2; $i < count($sor); $i++) {
                    $sql .= ", " . $sor[$i];
                }
                $sql .= ") VALUES ( '" . $_POST[$sor[1]] . "' ";
                for ($i = 2; $i < count($sor); $i++) {
                    $sql .= ", '" . $_POST[$sor[$i]] . "' ";
                }
                $sql .= ")";
                $conn->query($sql);
                $log=fopen('log.txt','a');
                $txt=$_SESSION["nev"]." A ".$_POST["tabla"]." táblához adatot adott hozzá! \n";
                fwrite($log, $txt);  
                fclose($log);
            }
            $conn->close();
            header("Location: " . $_SERVER["PHP_SELF"]);
        }
        ?>
    </div>

    <div id="letrehozas" hidden>
        <div id="elso">
            <p>Mi legyen az új táblázat neve</p><input type="text" id="tnev"><br>
            <p>Hány oszlop legyen benne(Az id sor alapból benne van nem kell beleszámolni):</p><input type="number" id="oszlopszam"><br>
            <button onclick="ujtabla()">Tovább</button>
        </div>
        <div id="masodik" hidden>
            <form id="ujt" action="admin.php" method="POST"></form>
            <?php
            if (isset($_POST['ujtsend'])) {
                $conn = new mysqli("localhost", "root", "", "nagybeadando");
                if ($conn->connect_error) {
                    die("Nem sikerült az adatbázishoz csatlakozni!");
                }

                $sql = "CREATE TABLE " . $_POST["ujtnev"] . " (";
                $sql .= "id int NOT NULL AUTO_INCREMENT, ";
                for ($i = 1; $i <= $_POST["ujtoszlopszam"]; $i++) {
                    if (!isset($_POST["on" . $i]) || !isset($_POST["ot" . $i])) {
                        die("Nem sikerült jól megadni az adatokat!");
                    }
                    $sql .= $_POST["on" . $i] . " " . $_POST["ot" . $i];
                    if ($_POST["om" . $i] != "") {
                        $sql .= "(" . $_POST["om" . $i] . ")";
                    }
                    $sql .= ", ";
                }
                $sql .= "kie int NOT NULL DEFAULT(0), ";
                $sql .= " PRIMARY KEY (id) );";
                $conn->query($sql);
                $conn->close();
                header("Location: " . $_SERVER["PHP_SELF"]);
            }
            ?>
        </div>
    </div>

    <div id="ohozzaadas" hidden>
        <form id="ujoszlop" action="admin.php" method="POST"></form>
        <?php
            if (isset($_POST['ujosend'])) {
                $conn = new mysqli("localhost", "root", "", "nagybeadando");
                if ($conn->connect_error) {
                    die("Nem sikerült az adatbázishoz csatlakozni!");
                }
                if (!isset($_POST["on" . $i]) || !isset($_POST["ot" . $i])) {
                    die("Nem sikerült jól megadni az adatokat!");
                }
                $sql ="ALTER TABLE ".$_POST["tabla"]." ADD ".$_POST["on"] . " " . $_POST["ot"];
                if ($_POST["om"] != "") {
                    $sql .= "(" . $_POST["om" . $i] . ")";
                }
                if($_POST["alap"]!=""){
                    $sql .= " DEFAULT(".$_POST["alap"].")";
                }
                $conn->query($sql);
                $conn->close();
                header("Location: " . $_SERVER["PHP_SELF"]);
            }
            ?>
    </div>


    <script>
        function tablafeltolt() {
            document.getElementById("tabla").hidden = false;
            var x = document.getElementById("select").value;
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("tabla").innerHTML = this.responseText;
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            document.getElementById("oszlop").innerHTML = this.responseText;
                        }
                    };
                    xhttp.open("POST", "oszlop.php", true);
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhttp.send("tabla=" + x);
                }
            };
            xhttp.open("POST", "table.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("tabla=" + x+"&jog="+1);
        }

        function torol(id) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    tablafeltolt();
                }
            };
            var x = document.getElementById("select").value;
            xhttp.open("POST", "torl.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("id=" + id + "&tabla=" + x+"&jog="+1);
        }

        function modosit(id) {
            document.getElementById("listaz").hidden = true;
            document.getElementById("modositas").hidden = false;
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("modosit").innerHTML = this.responseText;
                }
            };
            var x = document.getElementById("select").value;
            xhttp.open("POST", "edit.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("id=" + id + "&tabla=" + x+"&jog="+1);
        }

        function hozzaad() {
            document.getElementById("listaz").hidden = true;
            document.getElementById("hozzaadas").hidden = false;
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("hozzaad").innerHTML = this.responseText;
                }
            };
            var x = document.getElementById("select").value;
            xhttp.open("POST", "hozzaad.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("tabla=" + x);
        }

        var sorrend = 1;

        function sorba(szam) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("tabla").innerHTML = this.responseText;
                }
            };
            if (sorrend == 1) sorrend = 0;
            else sorrend = 1;
            var x = document.getElementById("select").value;
            xhttp.open("POST", "sorbarendez.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("tabla=" + x + "&sor=" + szam + "&hanyszor=" + sorrend+"&jog="+1);
        }

        function szures() {
            var szoveg = document.getElementById("kereso").value;
            var oszlop = document.getElementById("oszlop").value;
            var tabla = document.getElementById("select").value;
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("tabla").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "szures.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("tabla=" + tabla + "&szoveg=" + szoveg + "&oszlop=" + oszlop+"&jog="+1);
        }

        function uj() {
            document.getElementById("listaz").hidden = true;
            document.getElementById("letrehozas").hidden = false;
        }

        function ujtabla() {
            document.getElementById("elso").hidden = true;
            document.getElementById("masodik").hidden = false;
            var oszlopszam = document.getElementById("oszlopszam").value;
            var str = "";
            str += '<input type="hidden" name="ujtnev" value="' + document.getElementById("tnev").value + '">';
            str += '<input type="hidden" name="ujtoszlopszam" value="' + oszlopszam + '">';
            for (var i = 1; i <= oszlopszam; i++) {
                str += i + 'oszlop neve: <input type="text" name="on' + i + '" > ';
                str += ' ' + i + '  oszlop tipusa (SQL-nek megfelelően): <input type="text" name="ot' + i + '" > ';
                str += ' ' + i + ' oszlop max hossz: <input type="text" name="om' + i + '" ><br>';
            }
            str += '<input type="submit" name="ujtsend" value="Küldés">';
            document.getElementById("ujt").innerHTML = str;
        }

        function otorol(szam) {
            var xhttp = new XMLHttpRequest();
            var t = document.getElementById("select").value;
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("tabla").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "oszloptorol.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("tabla=" + t + "&oszlop=" + szam);
        }

        function ohozzaad() {
            document.getElementById("listaz").hidden = true;
            document.getElementById("ohozzaadas").hidden = false;
            str = '<input type="hidden" name="tabla" value="' + document.getElementById("select").value + '">';
            str +='oszlop neve: <input type="text" name="on" > ';
            str +='oszlop tipusa (SQL-nek megfelelően): <input type="text" name="ot" > ';
            str +='oszlop max hossz: <input type="text" name="om" ><br>';
            str +='alapértelmezett érték: <input type="text" name="alap"><br>';
            str +='<input type="submit" name="ujosend" value="Küldés">';
            document.getElementById("ujoszlop").innerHTML = str;
        }

        function ki(){
            var xhttp = new XMLHttpRequest();
            var t = document.getElementById("select").value;
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    window.location.href = "http://localhost/nagybeadando/login.php";
                }
            };
            xhttp.open("POST", "kilepes.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send();

        }
    </script>
</body>

</html> 