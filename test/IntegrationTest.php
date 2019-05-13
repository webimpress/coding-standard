<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest;

use Generator;
use PHPUnit\Framework\TestCase;

use function basename;
use function copy;
use function exec;
use function glob;
use function sys_get_temp_dir;
use function tempnam;

class IntegrationTest extends TestCase
{
    /**
     * @dataProvider files
     */
    public function testIntegration(string $file) : void
    {
        $tmpname = tempnam(sys_get_temp_dir(), '') . '_' . basename($file);
        copy($file, $tmpname);

        exec('vendor/bin/phpcbf ' . $tmpname);

        self::assertFileEquals($file . '.fixed', $tmpname);
    }

    public function files() : Generator
    {
        $files = glob(__DIR__ . '/Integration/*.php');

        foreach ($files as $file) {
            yield $file => [$file];
        }
    }
}
