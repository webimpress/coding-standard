<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest\Integration;

class ImportConflict
{
    /**
     * @throws \Exception
     */
    public function __construct(array $a)
    {
        throw new \Exception(current($a));
    }
}
