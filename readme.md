# File Cache Wrapper

File Cache Wrapper, is a light and simple file cacher. You dont need to handle file operations with file cache wrapper.

## Install

Via Composer

```bash
$ composer require erip/file-cache-wrapper
```

## Usage

```php
use Erip\FileCacheWrapper\FileCacheWrapper;


/*
* First parameter is filename. It creates a file in tmp folder with given filename.
* If you give a absolute path that starting with /, it will create file in given path.
* Second parameter is time to cache expire.
* Third parameter is function that you want to cache.
* Fourth parameter is optional disable flag. If you give true flag it wont return cached data.
*/
$getCachedDate = FileCacheWrapper::getCachedFunc("cache.txt", "15 seconds", function()
{
    return date("H:i:s");
});

echo $getCachedDate();
```

## Credits

- [serkanerip](https://github.com/serkanerip)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
