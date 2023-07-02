<?php

namespace Keruald\Cache\Exceptions;

use Psr\SimpleCache\CacheException as CacheExceptionInterface;

use RuntimeException;

class CacheException extends RuntimeException implements CacheExceptionInterface {

}
