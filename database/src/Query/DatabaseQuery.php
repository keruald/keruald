<?php

namespace Keruald\Database\Query;

use Keruald\Database\Result\DatabaseResult;

abstract class DatabaseQuery {

    public abstract function query() : ?DatabaseResult;

    public abstract function __toString() : string;

}
