<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_ANON_CLASS;
use const T_CLOSURE;
use const T_FN;
use const T_STATIC;
use const T_VARIABLE;
use const T_WHITESPACE;

class StaticCallbackSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CLOSURE, T_FN];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(
            Tokens::$emptyTokens,
            $stackPtr - 1,
            null,
            true
        );

        $isStatic = $prev && $tokens[$prev]['code'] === T_STATIC;

        $start = $tokens[$stackPtr]['scope_opener'];
        $close = $tokens[$stackPtr]['scope_closer'];
        $hasThis = $this->hasThis($phpcsFile, $start, $close);

        if (! $isStatic && ! $hasThis) {
            $fix = $phpcsFile->addFixableError('Closure can be static', $stackPtr, 'Static');

            if ($fix) {
                $phpcsFile->fixer->addContentBefore($stackPtr, 'static ');
            }

            return;
        }

        if ($isStatic && $hasThis) {
            $fix = $phpcsFile->addFixableError('Closure cannot be static', $stackPtr, 'NonStatic');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($prev, '');
                if ($tokens[$prev + 1]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($prev + 1, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    private function hasThis(File $phpcsFile, int $start, int $close) : bool
    {
        $tokens = $phpcsFile->getTokens();

        while ($next = $phpcsFile->findNext([T_ANON_CLASS, T_VARIABLE], $start + 1, $close)) {
            if ($tokens[$next]['code'] === T_ANON_CLASS) {
                if ($this->hasThis($phpcsFile, $next, $tokens[$next]['scope_opener'])) {
                    return true;
                }

                $start = $tokens[$next]['scope_closer'];
                continue;
            }

            if ($tokens[$next]['code'] === T_VARIABLE
                && $tokens[$next]['content'] === '$this'
            ) {
                return true;
            }

            $start = $next;
        }

        return false;
    }
}
