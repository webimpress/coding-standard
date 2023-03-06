<?php

declare(strict_types=1);

namespace WebimpressCodingStandardTest;

use Generator;
use PHPUnit\Framework\TestCase;

use function basename;
use function copy;
use function exec;
use function file_exists;
use function glob;
use function implode;
use function str_replace;
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

        $rulesetFile = str_replace('.php', '.xml', $file);
        $options = file_exists($rulesetFile) ? ' --standard=' . $rulesetFile : '';

        exec('vendor/bin/phpcbf ' . $tmpname . $options, $output, $returnVal);

        self::assertSame(1, $returnVal, 'Output: ' . "\n" . implode("\n", $output));
        self::assertFileEquals($file . '.fixed', $tmpname);
    }

    public static function files() : Generator
    {
        $files = glob(__DIR__ . '/Integration/*.php');

        foreach ($files as $file) {
            yield $file => [$file];
        }
    }
}
