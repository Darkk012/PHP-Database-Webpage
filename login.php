<?php
session_start();
?>
<html>

<head>

</head>

<body>
    <form action="login.php" method="POST">
        <fieldset>
            <legend>Belépépsi adatok</legend>
            Felhasználói név: <input type="text" name="felhasznalo" maxlength="20"><br>
            Jelszó: <input type="password" name="psw" maxlength="50"><br>
        </fieldset>
        <input type="submit" value="Belépés" name="belep">
    </form>
    <?php
    if (isset($_POST["belep"])) {
        $conn = new mysqli("localhost", "root", "", "nagybeadando");
        if ($conn->connect_error)
            die("Nem sikerült az adatbázishoz kapcsolódni!");
        $sql = "SELECT * FROM adatok WHERE felhasznalo='" . $_POST["felhasznalo"] . "' AND jelszo='" . $_POST["psw"] . "'";
        $result = $conn->query($sql);
        if (mysqli_num_rows($result) == 0) {
            echo 'Nincs ilyen felhasználó!';
        } else {            
            $log=fopen('log.txt','a');
            $row = $result->fetch_assoc();
            $_SESSION["nev"]=$row["felhasznalo"];
            $_SESSION["id"]=$row["id"];
            if ($row["jogosultsag"] == 1) {                
                $txt=$_POST["felhasznalo"]." admin jelentkezett be \n";
                fwrite($log, $txt);
                fclose($log);                 
                header("Location: http://localhost/nagybeadando/admin.php");
            } else if ($row["jogosultsag"] == 2) {
                $txt=$_POST["felhasznalo"]." moderátor jelentkezett be \n";
                fwrite($log, $txt);
                fclose($log); 
                header("Location: http://localhost/nagybeadando/moderator.php");
            } else if ($row["jogosultsag"] == 3) {
                $txt=$_POST["felhasznalo"]." felhasználó jelentkezett be \n";
                fwrite($log, $txt);
                fclose($log); 
                header("Location: http://localhost/nagybeadando/felhasznalo.php");
            }
        }

        $conn->close();
    }
    ?>
</body>

</html>