<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings\Multibyte;

use InvalidArgumentException;

trait WithEncoding {

    /**
     * @var string
     */
    private $encoding;

    public function getEncoding () : string {
        return $this->encoding;
    }

    public function setEncoding (string $encoding) : self {
        if (!StringUtilities::isSupportedEncoding($encoding)) {
            throw new InvalidArgumentException;
        }

        $this->encoding = $encoding;

        return $this;
    }

}
