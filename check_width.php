<?php
require __DIR__ . '/vendor/autoload.php';

$r = new ReflectionClass('Filament\Resources\Pages\CreateRecord');
echo "Filename: " . $r->getFileName() . PHP_EOL;
