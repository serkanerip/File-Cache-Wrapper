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
            $fileResource = fopen($filePath, "w+");

            if (file_exists($filePath) && filesize($filePath) > 42)
            {
                flock($fileResource, LOCK_EX);
                $fileExpTime = file_get_contents($filePath, false, null,0,10);
                if ($fileExpTime > strtotime("now"))
                {
                    // if there is no need to write unlock the file.
                    flock($fileResource, LOCK_UN);
                    $fileHashAndData = file_get_contents($filePath, false, null, 10);
                    $fileHash = substr($fileHashAndData, 0, 32);
                    $fileData = substr($fileHashAndData, 32, strlen($fileHashAndData));

                    if ($fileHash === md5($fileData)) {
                        return unserialize($fileData);
                    } else {
                        error_log("MD5 Fail");
                    }
                }
            } else
            {
                // Lock the file for writing cache for the first time.
                flock($fileResource, LOCK_EX);
            }

            $data = ($func)();
            $encodedData = serialize($data);
            $fileContent = strtotime($expTime).md5($encodedData).$encodedData;
            if (file_put_contents($filePath, $fileContent) === false) {
                throw new Exception("Cannot write to $filePath");
            }
            // UNLOCK File after writing operations.
            flock($fileResource, LOCK_UN);
            return $data;
        };
    }
}
