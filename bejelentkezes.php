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

    <h1>Regisztráció</h1>
    <form action="backend/register.php" method="post">
        <fieldset>
            <?php
            $faultyRegister = (!empty($_GET["error"]) && $_GET["form"] == "register")
            ?>
            <label for="v_name">Vezetéknév:</label>
            <input required type="text" maxlength="100" id="v_name" name="v_name" />
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "v_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <label for="k_name">Keresztnév:</label>
            <input required type="text" maxlength="100" id="k_name" name="k_name" >
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "display_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <label for="email">E-mail cím:</label>
            <input required type="email" id="email" name="email" maxlength="100">
            <?php if ($faultyRegister && $_GET["error"] == "EmptyField" && isset($_GET["field"]) && $_GET["field"] == "email") { ?>
                <span class="error">Nem maradhat üresen!</span>
            <?php } ?>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "email") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?>
            <?php if ($faultyRegister && $_GET["error"] == "InvalidEmail") { ?>
                <span class="error">Ez az e-mail cím érvénytelen!</span>
            <?php } ?>
            <?php if ($faultyRegister && $_GET["error"] == "EmailIsInUse") { ?>
                <span class="error">Ez az e-mail cím már foglalt!</span>
            <?php } ?><br />

            <label for="password">Jelszó:</label>
            <input required placeholder="Legalább 8 karakter" minlength="8" maxlength="100" type="password" id="password" name="password" />
            <?php if ($faultyRegister && $_GET["error"] == "EmptyField" && isset($_GET["field"]) && $_GET["field"] == "password") { ?>
                <span class="error">Nem maradhat üresen!</span>
            <?php } ?>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "password") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?>
            <?php if ($faultyRegister && $_GET["error"] == "PasswordTooShort") { ?>
                <span class="error">A jelszó túl rövid (legyen legalább 8 karakter)!</span>
            <?php } ?><br />

            <label for="password1">Jelszó mégegyszer:</label>
            <input required placeholder="Legalább 8 karakter" minlength="8" maxlength="100" type="password" id="password1" name="password1" />
            <?php if ($faultyRegister && $_GET["error"] == "EmptyField" && isset($_GET["field"]) && $_GET["field"] == "password1") { ?>
                <span class="error">Nem maradhat üresen!</span>
            <?php } ?>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "password1") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?>
            <?php if ($faultyRegister && $_GET["error"] == "PasswordsDontMatch") { ?>
                <span class="error">A jelszavak nem egyeznek!</span>
            <?php } ?><br />

            <label for="tel">Telefonszám:</label>
            <input required maxlength="100" id="tel" name="tel" type="text"/>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "display_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <label for="country">Ország:</label>
            <input required maxlength="100" id="country" name="country" type="text"/>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "display_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <label for="megye">Vármegye:</label>
            <input required maxlength="100" id="megye" name="megye" type="text"/>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "display_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <label for="irsz">Irányítószám:</label>
            <input required maxlength="100" id="tel" name="irsz" type="text"/>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "display_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <label for="city">Város:</label>
            <input required maxlength="100" id="city" name="city" type="text"/>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "display_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <label for="street">Utca:</label>
            <input required maxlength="100" id="street" name="street" type="text"/>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "display_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <label for="h-number">Házszám:</label>
            <input required maxlength="100" id="h-number" name="h-number" type="text"/>
            <?php if ($faultyRegister && $_GET["error"] == "FieldTooLong" && isset($_GET["field"]) && $_GET["field"] == "display_name") { ?>
                <span class="error">Legfeljebb 100 karakter hosszú lehet!</span>
            <?php } ?><br />

            <input type="submit" value="Regisztráció">
        </fieldset>
    </form>

    <?php include(__DIR__ . "/components/footer.php"); ?>
</body>

</html>