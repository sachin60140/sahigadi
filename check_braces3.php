<?php
$code = file_get_contents('app/Http/Controllers/PaymentController.php');
$lines = explode("
", $code);
$braceCount = 0;
foreach($lines as $i => $line) {
    for($j=0; $j<strlen($line); $j++) {
        if ($line[$j] === '{') $braceCount++;
        if ($line[$j] === '}') {
            $braceCount--;
            if ($braceCount < 0) {
                echo "Extra closing brace at line: " . ($i+1) . "
";
                exit;
            }
        }
    }
}
echo "Final brace count: " . $braceCount . "
";
