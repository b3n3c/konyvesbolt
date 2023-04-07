<?php
include(__DIR__ . '/backend/validator.php');
include(__DIR__ . '/backend/dbhelper.php');

session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('Location: bejelentkezés.php');
    exit();
}

if (isset($_POST["quantity"])){
    $_SESSION["cart"][$_POST["isbn"]] = $_POST["quantity"];
}
if(isset($_POST["remove"])){
    unset($_SESSION["cart"][$_POST["isbn"]]);
}
?>

<!DOCTYPE html>
<html>
<?php
$TITLE_SUFFIX = "Kosár";
include(__DIR__ . '/components/head.php');
?>

<body>
<?php
$ACTIVE = 'Kosár';
include(__DIR__ . '/components/header.php');
?>
<main>
    <?php
        if((isset($_GET["success"]) && $_GET["success"] == "1")){
            print "<span id='success' class='ok'>A megrendelés sikeresen elküldve!</span><br>";
        }
        if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])){
            print "<form action='backend/newOffer.php' method='post'>";
            print "<table>";
            print "<thead>";
            print "<th>Könyv címe</th>";
            print "<th>Mennyiég</th>";
            print "<th>Ár</th>";
            print "<th></th>";
            print "</thead>";
            print "<tbody>";
            $vegosszeg = 0;
            foreach (array_keys($_SESSION["cart"]) as $isbn){
                print "<form action='' method='post'>";
                print "<input type='hidden' name='isbn' value='{$isbn}'/>";
                $konyv = getBookByISBN($isbn);
                print "<tr>";
                print "<td>{$konyv["CIM"]}</td>";
                print "<td><input name='quantity' type='number' min='1' value='{$_SESSION["cart"][$isbn]}' onchange='this.form.submit()'></td>";
                $ar = $_SESSION["cart"][$isbn] * $konyv["AR"];
                $vegosszeg += $ar;
                print "<td>{$ar} Ft</td>";
                print "<td><input type='submit' name='remove' value='Eltávolítás'></td>";
                print "</tr>";
                print "</form>";
            }
            print "<tr><td colspan='4'>Végösszeg: {$vegosszeg} Ft</td></tr>";
            print "</tbody>";
            print "</table>";
            print "</br>";
            print "<input type='hidden' name='ar' value='{$vegosszeg}'>";
            print "<input type='submit' name=newOffer' value='Kiválasztott könyvek megrendelése'>";
            print "</form>";
        }else{
            print "<p>A kosár üres.</p>";
        }
    ?>
</main>

<?php include(__DIR__ . '/components/footer.php'); ?>
</body>

</html>
