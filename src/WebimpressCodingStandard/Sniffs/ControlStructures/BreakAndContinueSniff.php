<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_BREAK;
use const T_CONTINUE;
use const T_LNUMBER;
use const T_WHITESPACE;

class BreakAndContinueSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_BREAK, T_CONTINUE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $arg = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);

        if ($tokens[$arg]['code'] !== T_LNUMBER) {
            return;
        }

        if ($tokens[$stackPtr + 1]['content'] !== ' ') {
            $error = 'Required one space after %s instruction';
            $data = [$tokens[$stackPtr]['content']];
            $fix = $phpcsFile->addFixableError($error, $stackPtr + 1, 'RequiredSpace', $data);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $stackPtr + 1;
                while ($tokens[$i]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    ++$i;
                }
                $phpcsFile->fixer->addContent($stackPtr, ' ');
                $phpcsFile->fixer->endChangeset();
            }
        }

        if ($tokens[$arg]['content'] === '1') {
            $error = 'Argument "1" is redundant with %s instruction';
            $data = [$tokens[$stackPtr]['content']];
            $fix = $phpcsFile->addFixableError($error, $arg, 'RedundantArgument', $data);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $arg - 1;
                while ($tokens[$i]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    --$i;
                }
                $phpcsFile->fixer->replaceToken($arg, '');
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
