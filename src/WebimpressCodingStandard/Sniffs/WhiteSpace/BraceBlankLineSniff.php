<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_OPEN_CURLY_BRACKET;
use const T_WHITESPACE;

class BraceBlankLineSniff implements Sniff
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
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if ($tokens[$next]['line'] > $tokens[$stackPtr]['line'] + 1) {
            $error = 'Blank line found after opening brace';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterOpen');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $stackPtr + 1;
                while ($tokens[$i]['line'] + 1 < $tokens[$next]['line']) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    ++$i;
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        $closer = $tokens[$stackPtr]['bracket_closer'];
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $closer - 1, $stackPtr + 1, true);
        if ($prev && $tokens[$closer]['line'] > $tokens[$prev]['line'] + 1) {
            $error = 'Blank line found before closing brace';
            $fix = $phpcsFile->addFixableError($error, $closer, 'SpacingBeforeClose');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $prev + 1;
                while ($tokens[$i]['line'] < $tokens[$closer]['line']) {
                    if ($tokens[$i]['line'] > $tokens[$prev]['line']) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    ++$i;
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
