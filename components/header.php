<?php

if (!isset($_SESSION))
    session_start();

$_pages = [
    "Kezdőlap" => "index.php",
    "Könyvek" => "konyvek.php",
];

if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
    $_pages["Profilom"] = "profil.php";
    if (isset($_SESSION["admin"]) && !empty($_SESSION["admin"])) {
        $_pages["Rendelések"] = urlencode("rendelesek.php");
        $_pages["Új könyv hozzáadása"] = urlencode("ujKonyv.php");
    }
    $_pages["Kosár"] = "kosar.php";
    $_pages["Kijelentkezés"] = "backend/logout.php";

} else {
    $_pages["Bejelentkezés"] = urlencode("bejelentkezes.php");
}

if (isset($ACTIVE) && !empty($ACTIVE)) {
    $_active = $ACTIVE;
} else {
    $_active = 'Kezdőlap';
}
?>
<header>
    <div class="company">
        <img id="logo" src="img/logo/logo.png" alt="Cég logó">
        <h1>Könyvesbolt</h1>
        <blockquote>A könyv a legjobb barát.</blockquote>
    </div>
    <nav>
        <?php
        foreach ($_pages as $_page_name => $_href) {
            if ($_page_name === $_active) {
                $_class =" active";
            } else {
                $_class = "";
            }
            ?>
            <a class="nav-link<?= $_class ?>" href="<?= $_href ?>"><?= $_page_name ?></a>
        <?php } ?>
    </nav>
</header>
<hr>
<?php unset($_active, $_page_name, $_href, $_class, $_pages); ?>
