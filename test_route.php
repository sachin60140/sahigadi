<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/dealer/register', 'GET');
$response = $kernel->handle($request);
echo "Status: " . $response->status() . "\n";
echo "Content: " . substr($response->getContent(), 0, 500) . "...\n";
