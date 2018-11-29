<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_NS_SEPARATOR;
use const T_STRING;
use const T_WHITESPACE;

class NamespaceSeparatorSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_NS_SEPARATOR];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
            $error = 'Unexpected whitespace after namespace separator';
            $fix = $phpcsFile->addFixableError($error, $stackPtr + 1, 'SpaceAfter');

            if ($fix) {
                $token = $stackPtr + 1;
                $phpcsFile->fixer->beginChangeset();
                while ($tokens[$token]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($token, '');
                    ++$token;
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE) {
            $before = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);

            if ($tokens[$before]['code'] === T_STRING) {
                $error = 'Unexpected whitespace before namespace separator';
                $fix = $phpcsFile->addFixableError($error, $stackPtr - 1, 'SpaceBefore');

                if ($fix) {
                    $token = $stackPtr - 1;
                    $phpcsFile->fixer->beginChangeset();
                    while ($tokens[$token]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($token, '');
                        --$token;
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }
}
