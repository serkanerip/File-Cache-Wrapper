<?php
require_once __DIR__."/../vendor/autoload.php";

use Erip\FileCacheWrapper\FileCacheWrapper;

$getCachedDate = FileCacheWrapper::getCachedFunc("q", "15 seconds", function()
{
    return date("H:i:s");
});

echo $getCachedDate."\n";