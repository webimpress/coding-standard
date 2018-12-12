<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_IS_EQUAL;
use const T_IS_NOT_EQUAL;

class RequireStrictComparisonSniff implements Sniff
{
    /**
     * @var string[]
     */
    private $map = [
        T_IS_EQUAL => '===',
        T_IS_NOT_EQUAL => '!==',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_IS_EQUAL, T_IS_NOT_EQUAL];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $error = 'Expected strict comparison %s; found %s';
        $expected = $this->map[$tokens[$stackPtr]['code']];
        $data = [
            $expected,
            $tokens[$stackPtr]['content'],
        ];

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Invalid', $data);
        if ($fix) {
            $phpcsFile->fixer->replaceToken($stackPtr, $expected);
        }
    }
}
