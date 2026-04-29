<?php
$code = file_get_contents('app/Http/Controllers/PaymentController.php');
$tokens = token_get_all($code);
$braceCount = 0;
foreach($tokens as $token) {
    if (is_array($token)) {
        if ($token[0] == T_RETURN || $token[0] == T_FUNCTION || $token[0] == T_CLASS) {
            echo "Line " . $token[2] . ": " . token_name($token[0]) . " (Brace: $braceCount)
";
        }
    } else {
        if ($token === '{') { $braceCount++; echo "Brace { (Count: $braceCount)
"; }
        if ($token === '}') { $braceCount--; echo "Brace } (Count: $braceCount)
"; }
    }
}
