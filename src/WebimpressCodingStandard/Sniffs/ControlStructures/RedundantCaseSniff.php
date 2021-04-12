<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function array_reverse;
use function key;

use const T_BREAK;
use const T_CASE;
use const T_CLOSE_CURLY_BRACKET;
use const T_DEFAULT;
use const T_ENDSWITCH;
use const T_LNUMBER;
use const T_OPEN_PARENTHESIS;
use const T_SEMICOLON;
use const T_WHITESPACE;

class RedundantCaseSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_CASE,
            T_DEFAULT,
        ];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr) : int
    {
        $tokens = $phpcsFile->getTokens();

        $default = $tokens[$stackPtr];
        $switchPtr = key(array_reverse($default['conditions'], true));

        // in case of PHP 8 match expression
        if ($switchPtr === null) {
            return $stackPtr + 1;
        }

        if ($tokens[$stackPtr]['code'] === T_CASE) {
            $next = $stackPtr;

            do {
                $last = $tokens[$next]['scope_opener'];
                $next = $phpcsFile->findNext(Tokens::$emptyTokens + [
                    T_SEMICOLON => T_SEMICOLON,
                ], $last + 1, null, true);
            } while ($tokens[$next]['code'] === T_CASE);

            while ($semicolon = $phpcsFile->findNext(Tokens::$emptyTokens, $last + 1, null, true)) {
                if ($tokens[$semicolon]['code'] === T_SEMICOLON) {
                    $last = $semicolon;
                    continue;
                }

                break;
            }

            if ($tokens[$next]['code'] === T_DEFAULT
                || $tokens[$next]['code'] === T_CLOSE_CURLY_BRACKET
                || $tokens[$next]['code'] === T_ENDSWITCH
            ) {
                $fix = $phpcsFile->addFixableError('Redundant case', $stackPtr, 'RedundantCase');

                if ($fix) {
                    $this->deleteCase($phpcsFile, $stackPtr, $last);
                }
            } elseif ($tokens[$next]['code'] === T_BREAK
                && $this->hasEmptyDefault($phpcsFile, key(array_reverse($tokens[$stackPtr]['conditions'], true)))
            ) {
                $this->onlyBreak($phpcsFile, $stackPtr, $next);
            }

            return $stackPtr + 1;
        }

        $next = $stackPtr;

        do {
            $last = $tokens[$next]['scope_opener'];
            $next = $phpcsFile->findNext(Tokens::$emptyTokens + [
                T_SEMICOLON => T_SEMICOLON,
            ], $last + 1, null, true);

            while ($semicolon = $phpcsFile->findNext(Tokens::$emptyTokens, $last + 1, null, true)) {
                if ($tokens[$semicolon]['code'] === T_SEMICOLON) {
                    $last = $semicolon + 1;
                    continue;
                }

                break;
            }

            $isCase = $tokens[$next]['code'] === T_CASE;
            if ($isCase) {
                $fix = $phpcsFile->addFixableError('Redundant case', $next, 'RedundantCase');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $last + 1; $i <= $tokens[$next]['scope_opener']; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            }
        } while ($isCase);

        if ($tokens[$next]['code'] === T_CLOSE_CURLY_BRACKET
            || $tokens[$next]['code'] === T_ENDSWITCH
        ) {
            $fix = $phpcsFile->addFixableError('Redundant case', $stackPtr, 'RedundantCase');

            if ($fix) {
                $this->deleteCase($phpcsFile, $stackPtr, $last);
            }

            return $next + 1;
        }

        if ($tokens[$next]['code'] === T_BREAK) {
            $this->onlyBreak($phpcsFile, $stackPtr, $next);

            return $next + 1;
        }

        return $stackPtr + 1;
    }

    private function deleteCase(File $phpcsFile, int $casePtr, int $lastPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $phpcsFile->fixer->beginChangeset();
        $i = $casePtr - 1;
        while ($tokens[$i]['code'] === T_WHITESPACE
            && $tokens[$i]['line'] === $tokens[$casePtr]['line']
        ) {
            $phpcsFile->fixer->replaceToken($i, '');
            --$i;
        }

        for ($i = $casePtr; $i <= $lastPtr; ++$i) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        $i = $lastPtr + 1;
        while ($tokens[$i]['code'] === T_WHITESPACE
            && $tokens[$i]['line'] === $tokens[$lastPtr]['line']
        ) {
            $phpcsFile->fixer->replaceToken($i, '');
            ++$i;
        }
        $closer = $tokens[$casePtr]['scope_closer'];
        if ($closer > $lastPtr) {
            $phpcsFile->fixer->replaceToken($closer, $tokens[$closer]['content']);
        }
        $phpcsFile->fixer->endChangeset();
    }

    private function onlyBreak(File $phpcsFile, int $casePtr, int $breakPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $value = $phpcsFile->findNext(Tokens::$emptyTokens + [
            T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS,
        ], $breakPtr + 1, null, true);

        if ($tokens[$value]['code'] === T_SEMICOLON
            || ($tokens[$value]['code'] === T_LNUMBER
                && $tokens[$value]['content'] === '1')
        ) {
            $fix = $phpcsFile->addFixableError('Redundant case', $casePtr, 'RedundantCase');

            if ($fix) {
                $last = $tokens[$breakPtr]['scope_condition'] !== $casePtr
                    ? $tokens[$casePtr]['scope_opener']
                    : $phpcsFile->findNext(T_SEMICOLON, $value);

                $this->deleteCase($phpcsFile, $casePtr, $last);
            }
        }
    }

    private function hasEmptyDefault(File $phpcsFile, int $switchPtr) : bool
    {
        $tokens = $phpcsFile->getTokens();
        $from = $tokens[$switchPtr]['scope_opener'];
        $to = $tokens[$switchPtr]['scope_closer'];

        while ($default = $phpcsFile->findNext(T_DEFAULT, $from, $to)) {
            $conditions = array_reverse($tokens[$default]['conditions'], true);

            if (key($conditions) !== $switchPtr) {
                $from = $default + 1;
                continue;
            }

            $opener = $tokens[$default]['scope_opener'];
            $closer = $tokens[$default]['scope_closer'];
            if ($tokens[$closer]['code'] === T_BREAK) {
                $value = $phpcsFile->findNext(Tokens::$emptyTokens + [
                    T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS,
                ], $closer + 1, null, true);

                if ($tokens[$value]['code'] !== T_SEMICOLON
                    && ($tokens[$value]['code'] !== T_LNUMBER
                        || $tokens[$value]['content'] !== '1')
                ) {
                    return false;
                }
            } elseif ($tokens[$closer]['code'] !== T_CLOSE_CURLY_BRACKET) {
                return false;
            }

            $start = $opener;
            $end = $closer;

            while ($token = $phpcsFile->findNext(Tokens::$emptyTokens, $start + 1, $end, true)) {
                if ($tokens[$token]['code'] === T_SEMICOLON) {
                    $start = $token + 1;
                    continue;
                }

                if ($tokens[$token]['code'] === T_CASE) {
                    $start = $tokens[$token]['scope_opener'] + 1;
                    continue;
                }

                return false;
            }

            return true;
        }

        return true;
    }
}
