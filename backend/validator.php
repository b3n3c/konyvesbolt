<?php


if (extension_loaded('tidy')) {
    ob_start();

    register_shutdown_function(function() {
        $content = ob_get_clean();
        $tidy = new tidy();
        $tidy->parseString($content, [
            'indent' => true,
            'indent-spaces' => 4,
            'wrap' => 200,
        ], 'utf8');
        $tidy->cleanRepair();
        // $tidy->diagnose();
        if (strlen($tidy->errorBuffer) > 0) {   
            $lines = explode("\n", $tidy->errorBuffer);
            foreach ($lines as $k => $line) {
                if (strpos($line, 'proprietary attribute "minlength"') !== false) {
                    unset($lines[$k]);
                }
            }
            $filteredErrorBuffer = implode("\n", $lines);
            if (strlen($filteredErrorBuffer) > 0)
                error_log($_SERVER["PHP_SELF"]."\n".$filteredErrorBuffer."\n", 3, __DIR__ . '/../tidy.log');
        }

        echo $tidy;
    });
}

?>