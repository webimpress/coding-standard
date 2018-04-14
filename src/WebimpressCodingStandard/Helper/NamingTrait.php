<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Helper;

use PHP_CodeSniffer\Files\File;

use function strlen;
use function strpos;
use function substr;

/**
 * @internal
 *
 * @property string $prefix
 * @property string $suffix
 */
trait NamingTrait
{
    private function check(File $phpcsFile, int $stackPtr, string $msg)
    {
        $string = $phpcsFile->getTokens()[$stackPtr]['content'];

        if ($this->suffix) {
            $len = strlen($this->suffix);
            if (substr($string, -$len) !== $this->suffix) {
                $error = '%s name must have %s suffix';
                $phpcsFile->addError($error, $stackPtr, 'Suffix', [$msg, $this->suffix]);
            }
        }

        if ($this->prefix && strpos($string, $this->prefix) !== 0) {
            $error = '%s name must have %s prefix';
            $phpcsFile->addError($error, $stackPtr, 'Prefix', [$msg, $this->prefix]);
        }
    }
}
