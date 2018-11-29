<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_NAMESPACE;
use const T_WHITESPACE;

class NamespaceSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_NAMESPACE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        $content = $phpcsFile->getTokensAsString($stackPtr + 1, $next - $stackPtr - 1);

        if ($content !== ' ') {
            $error = 'Expected one space after namespace keyword';
            $fix = $phpcsFile->addFixableError($error, $stackPtr + 1, 'SpaceAfter');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                if ($content === '') {
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                } else {
                    $phpcsFile->fixer->replaceToken($stackPtr + 1, ' ');
                    for ($i = $stackPtr + 2; $i < $next; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
