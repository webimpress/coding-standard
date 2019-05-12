<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function class_exists;
use function interface_exists;
use function ltrim;
use function preg_match;
use function str_replace;
use function strpos;
use function substr;
use function trait_exists;

use const T_CONSTANT_ENCAPSED_STRING;

class StringClassReferenceSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CONSTANT_ENCAPSED_STRING];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (strpos($tokens[$stackPtr]['content'], '\\') === false) {
            return;
        }

        $name = substr(str_replace('\\\\', '\\', $tokens[$stackPtr]['content']), 1, -1);
        if (! preg_match('/^(\\\\?[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)+$/', $name)) {
            return;
        }

        if (class_exists($name) || interface_exists($name) || trait_exists($name)) {
            $error = 'String "%s" contains class reference, use ::class instead';
            $data = [$name];
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);

            if ($fix) {
                $expected = '\\' . ltrim($name, '\\') . '::class';
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        }
    }
}
