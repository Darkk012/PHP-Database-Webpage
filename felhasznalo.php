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
            xhttp.send("tabla=" + x + "&jog=" + 3);
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
            xhttp.send("tabla=" + x + "&sor=" + szam + "&hanyszor=" + sorrend + "&jog=" + 3);
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
            xhttp.send("tabla=" + tabla + "&szoveg=" + szoveg + "&oszlop=" + oszlop+"&jog="+3);
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