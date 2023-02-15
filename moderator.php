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
            $i = 0;
            while ($row = $result->fetch_assoc()) {
                if ($row["TABLE_NAME"] != "adatok") {
                    if ($i == 0) {
                        echo '<option value="' . $row["TABLE_NAME"] . '" selected>' . $row["TABLE_NAME"] . '</option>';
                    } else {
                        echo '<option value="' . $row["TABLE_NAME"] . '">' . $row["TABLE_NAME"] . '</option>';
                    }
                }
            }
            $conn->close();
            ?>
        </select>
        <input type="text" id="kereso" onchange="szures()">
        <select id="oszlop" onchange="szures()"></select>
        <button onclick="ki()">Kijelentkezés</button>
        <table id="tabla" hidden></table>
    </div>

    <div id="modositas" hidden>
        <form id="modosit" action="moderator.php" method="POST"></form>
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

            $sql="SELECT * FROM ". $_POST["tabla"] ." WHERE id=" . $_POST["id"];
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $ezkie=$row["kie"];

            for ($i = 1; $i < count($sor); $i++) {
                $sql = "UPDATE " . $_POST["tabla"] . " SET ";
                $sql .= $sor[$i];
                $sql .= "='";
                $sql .= $_POST[$sor[$i]] . "' ";
                $sql .= "WHERE id=" . $_POST["id"];
                $conn->query($sql);
            }
            $sql="UPDATE " . $_POST["tabla"] . " SET kie='".$ezkie."' WHERE id=" . $_POST["id"];
            $conn->query($sql);
            $conn->close();
            header("Location: " . $_SERVER["PHP_SELF"]);
        }
        ?>
    </div>

    <div id="hozzaadas" hidden>
        <form id="hozzaad" action="moderator.php" method="POST"></form>
        <?php
        if (isset($_POST["kuldes"])) {
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
            $sql = "INSERT INTO " . $_POST["tabla"] . "( " . $sor[1];
            for ($i = 2; $i < count($sor); $i++) {
                $sql .= ", " . $sor[$i];
            }
            $sql .= ") VALUES ( '" . $_POST[$sor[1]] . "' ";
            for ($i = 2; $i < count($sor) - 1; $i++) {
                $sql .= ", '" . $_POST[$sor[$i]] . "' ";
            }
            $sql .= ", '" . $_SESSION["id"] . "')";
            $conn->query($sql);
            $log = fopen('log.txt', 'a');
            $txt = $_SESSION["nev"] . " A " . $_POST["tabla"] . " táblához adatot adott hozzá! \n";
            fwrite($log, $sql);
            fclose($log);
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
            xhttp.send("tabla=" + x + "&jog=" + 2);
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
            xhttp.send("id=" + id + "&tabla=" + x + "&jog=" + 2);
        }

        function modosit(id) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText != "nem") {
                        document.getElementById("modosit").innerHTML = this.responseText;
                        document.getElementById("listaz").hidden = true;
                        document.getElementById("modositas").hidden = false;
                    }
                }
            };
            var x = document.getElementById("select").value;
            xhttp.open("POST", "edit.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("id=" + id + "&tabla=" + x + "&jog=" + 2);
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
            xhttp.send("tabla=" + x + "&sor=" + szam + "&hanyszor=" + sorrend + "&jog=" + 2);
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
            xhttp.send("tabla=" + tabla + "&szoveg=" + szoveg + "&oszlop=" + oszlop + "&jog=" + 2);
        }

        function ki() {
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