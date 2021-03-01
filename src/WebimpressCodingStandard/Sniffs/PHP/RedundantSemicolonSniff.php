<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function current;
use function in_array;

use const T_ANON_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSURE;
use const T_COLON;
use const T_FOR;
use const T_GOTO_LABEL;
use const T_MATCH;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_TAG;
use const T_SEMICOLON;

class RedundantSemicolonSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_SEMICOLON];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->checkBeginningOfScope($phpcsFile, $stackPtr);
        $this->checkAfterScope($phpcsFile, $stackPtr);
        $this->checkMultipleSemicolons($phpcsFile, $stackPtr);
    }

    private function checkBeginningOfScope(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if (in_array($tokens[$prev]['code'], [T_OPEN_TAG, T_OPEN_CURLY_BRACKET, T_COLON, T_GOTO_LABEL], true)) {
            $error = 'Redundant semicolon at the beginning of scope';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'BeginningOfScope');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr, '');
            }
        }
    }

    private function checkAfterScope(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if ($tokens[$prev]['code'] !== T_CLOSE_CURLY_BRACKET) {
            return;
        }

        if (! isset($tokens[$prev]['scope_condition'])) {
            return;
        }

        $scopeCondition = $tokens[$prev]['scope_condition'];
        if (in_array($tokens[$scopeCondition]['code'], [T_ANON_CLASS, T_CLOSURE, T_MATCH], true)) {
            return;
        }

        $error = 'Redundant semicolon after scope';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'AfterScope');

        if ($fix) {
            $phpcsFile->fixer->replaceToken($stackPtr, '');
        }
    }

    private function checkMultipleSemicolons(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);

        // If it is double semicolon in for loop: for (;;) {}
        if (! empty($tokens[$stackPtr]['nested_parenthesis'])
            && ($closer = current($tokens[$stackPtr]['nested_parenthesis']))
            && ! empty($tokens[$closer]['parenthesis_owner'])
            && $tokens[$tokens[$closer]['parenthesis_owner']]['code'] === T_FOR
        ) {
            return;
        }

        if ($next && $tokens[$next]['code'] === T_SEMICOLON) {
            $error = 'Duplicated semicolon';
            $fix = $phpcsFile->addFixableError($error, $next, 'DoubleSemicolon');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($next, '');
            }
        }
    }
}
