<?php

namespace Erip\FileCacheWrapper;

class FileCacheWrapper
{
    public static function getCachedFunc(string $fileName, string $expTime, callable $func, bool $disable=false)
    {
        if (empty(str_replace(" ", "", $fileName))) {
            throw new Exception("Filename cannot bet empty!");
        }
        $trimmedFileName = trim($fileName);
        $filePath = $trimmedFileName[0] == "/" ? $trimmedFileName : "/tmp/$trimmedFileName";

        if (strtotime($expTime) === false) {
            throw new Exception("Exptime should be a valid strtotime string.");
        }

        if ($disable) {
            return $func;
        }

        return function() use($filePath, $func, $expTime)
        {
            if (file_exists($filePath) && filesize($filePath) > 42)
            {
                $fileExpTime = file_get_contents($filePath, false, null,0,10);
                if ($fileExpTime > strtotime("now"))
                {
                    $fileHashAndData = file_get_contents($filePath, false, null, 10);
                    $fileHash = substr($fileHashAndData, 0, 32);
                    $fileData = substr($fileHashAndData, 32, strlen($fileHashAndData));

                    if ($fileHash === md5($fileData)) {
                        return unserialize($fileData);
                    } else {
                        error_log("MD5 Fail");
                    }
                }
            }

            $data = ($func)();
            $encodedData = serialize($data);
            $fileContent = strtotime($expTime).md5($encodedData).$encodedData;
            if (file_put_contents($filePath, $fileContent) === false) {
                throw new Exception("Cannot write to $filePath");
            }
            return $data;
        };
    }
}