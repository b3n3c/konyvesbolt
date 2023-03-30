<?php

include(__DIR__ . '/backend/validator.php');

session_start();

if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

// sanitize GET fields
foreach ($_GET as $key => $value) {
    $_GET[$key] = htmlspecialchars($value);
}
?>
<!DOCTYPE html>
<html lang="hu">

<?php
$TITLE_SUFFIX = 'Bejelentkezés';
include(__DIR__ . '/components/head.php');
?>

<body>

    <?php
    $ACTIVE = 'Bejelentkezés';
    include(__DIR__ . '/components/header.php');
    ?>


    <h1>Bejelentkezés</h1>
    <form action="backend/login.php" method="post">
        <fieldset>
            <label for="loginEmail">Email cím:</label>
            <input required type="text" id="loginEmail" name="email" value="<?= ((!empty($_GET["error"]) && $_GET["form"] == "login") ? $_GET["email"] : "") ?>" />
            <?php if (!empty($_GET["error"]) && $_GET["form"] == "login" && $_GET["error"] == "InvalidEmail") { ?>
                <span class="error">Érvénytelen e-mail cím!</span>
            <?php } ?>
            <?php if (!empty($_GET["error"]) && $_GET["form"] == "login" && $_GET["error"] == "UserDoesntExist") { ?>
                <span class="error">A felhasználó nem létezik!</span>
            <?php } ?><br />

            <label for="loginPassword">Jelszó:</label>
            <input required minlength="8" maxlength="100" type="password" id="loginPassword" name="password">
            <?php if (!empty($_GET["error"]) && $_GET["form"] == "login" && $_GET["error"] == "EmptyPassword") { ?>
                <span class="error">A jelszó mező nem lehet üres!</span>
            <?php } ?>
            <?php if (!empty($_GET["error"]) && $_GET["form"] == "login" && $_GET["error"] == "WrongPassword") { ?>
                <span class="error">Hibás jelszó!</span>
            <?php } ?><br />

            <input type="submit" value="Bejelentkezés" />
        </fieldset>
    </form>

    <?php include(__DIR__ . "/components/footer.php"); ?>
</body>

</html>