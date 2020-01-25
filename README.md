# Keruald OmniTools library

This utilities library offers convenient functions to solve common problems,
like parse an URL, generate a random string or validate an IP address.

## Getting started

### With Composer 

To use this library in a project, you can require the following package: 

```
$ composer require keruald/omnitools
```

### As a bundle

The library follows PSR-4 conventions:
the `src` folder matches the `Keruald\OmniTools` namespace.

If you don't have a PSR-4 loader available:

```lang=php
<?php

use Keruald\OmniTools\Registration\Autoloader; 

require 'path/to/keruald/omnitools/src/Registration/Autoloader.php';
Autoloader::selfRegister();
```

## Contribute or report issues

The Nasqueron DevCentral Phabricator instance is used to coordinate
development. You can fill issues against the #Keruald project.

https://devcentral.nasqueron.org/u/keruald

## Versioning

The library is sorted in namespaces and contains mostly static methods.

The library adheres to semantic versioning.
The 0.* version will be used to integrate code from the sourcing projects,
like Keruald/Pluton, Keruald/Xen, Azhàr or Zed.

## Credits

This library is maintained by Sébastien Santoro aka Dereckson.

The Contributors file contains the list of the people who contributed
to the source code.

## License

This code is available under BSD-2-Clause license.
