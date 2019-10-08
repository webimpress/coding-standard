<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_OBJECT_OPERATOR;
use const T_OPEN_CURLY_BRACKET;

class DisallowCurlyOffsetAccessBraceSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_CURLY_BRACKET];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'])) {
            return;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if ($tokens[$prev]['code'] === T_OBJECT_OPERATOR) {
            return;
        }

        $closerPtr = $tokens[$stackPtr]['bracket_closer'];

        $error = 'Invalid access offset bracket, use square brackets instead of curly';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Invalid');
        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($stackPtr, '[');
            $phpcsFile->fixer->replaceToken($closerPtr, ']');
            $phpcsFile->fixer->endChangeset();
        }
    }
}
