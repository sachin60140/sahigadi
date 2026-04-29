<?php
$code = file_get_contents('app/Http/Controllers/PaymentController.php');
$tokens = token_get_all($code);
$braceCount = 0;
foreach($tokens as $token) {
    if (is_array($token)) continue;
    if ($token === '{') $braceCount++;
    if ($token === '}') $braceCount--;
}
echo "Proper Token Brace Count: $braceCount
";
