<?php
$code = file_get_contents('app/Http/Controllers/PaymentController.php');
$lines = explode("
", $code);
$braceCount = 0;
foreach($lines as $i => $line) {
    for($j=0; $j<strlen($line); $j++) {
        if ($line[$j] === '{') $braceCount++;
        if ($line[$j] === '}') $braceCount--;
    }
    echo "Line " . ($i+1) . " (Count: $braceCount): $line
";
}
