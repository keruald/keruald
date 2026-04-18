<?php

define('SECRET', 'secret123');

define('TIGER_ALGO', 'tiger192,4');
define('TIGER_PAYLOAD', 'lorem ipsum dolor');
define('TIGER_SIGNATURE', '7081ac97e50ad97e13b2fb9364b9df376c26b920080245ce');

// Current default algo is sha-256. This test uses documentation values.
define('DEFAULT_256_PAYLOAD', "Hello, World!");
define('DEFAULT_256_SECRET', "It's a Secret to Everybody");
define('DEFAULT_256_SIGNATURE', "757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17");

// Legacy default algo is sha-1.
define('DEFAULT_PAYLOAD', file_get_contents(__DIR__ . '/payload.json'));
define('DEFAULT_SIGNATURE', 'ab05a3aef13696b60a60a8064b9fda31a8c77582');

define('EMPTY_DEFAULT_HASH_ALGO_SIGNATURE', 'fbdb1d1b18aa6c08324b7d64b71fb76370690e1d');
