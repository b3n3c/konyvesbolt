<?php

if (isset($TITLE_SUFFIX) && !empty($TITLE_SUFFIX)) {
    $_title = 'Könyvesbolt - ' . $TITLE_SUFFIX;
} else {
    $_title = 'Könyvesbolt';
}



?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="icon" type="image/x-icon" href="img/logo/logo.png">
    <?php if (isset($CSS) && count($CSS) > 0) {
        foreach ($CSS as $css) {
            echo "<link rel=\"stylesheet\" href=\"" . $css . "\">\n";
        }
    } ?>
</head>
<?php unset($_title); ?>
