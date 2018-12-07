<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function end;
use function in_array;
use function strtolower;

use const T_AS;
use const T_DOUBLE_COLON;
use const T_NS_SEPARATOR;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;
use const T_WHITESPACE;

class RedundantAliasSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_AS];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);

        if (! $prev
            || ! $next
            || $tokens[$prev]['code'] !== T_STRING
            || $tokens[$next]['code'] !== T_STRING
            || strtolower($tokens[$prev]['content']) !== strtolower($tokens[$next]['content'])
        ) {
            return;
        }

        $error = 'Alias %s is redundant';
        $data = [$tokens[$next]['content']];
        $fix = $phpcsFile->addFixableError($error, $next, 'Redundant', $data);

        if ($fix) {
            $removableTokens = [
                T_AS => T_AS,
                T_STRING => T_STRING,
                T_WHITESPACE => T_WHITESPACE,
            ];

            $isTrait = ! empty($tokens[$stackPtr]['conditions'])
                && end($tokens[$stackPtr]['conditions']) === T_USE;

            if ($isTrait) {
                $removableTokens += [
                    T_DOUBLE_COLON => T_DOUBLE_COLON,
                    T_NS_SEPARATOR => T_NS_SEPARATOR,
                ];
                $prev = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens + $removableTokens,
                    $prev,
                    null,
                    true
                );
            }

            $phpcsFile->fixer->beginChangeset();
            $i = $prev;
            while (isset($tokens[++$i])) {
                if (isset($removableTokens[$tokens[$i]['code']])) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    continue;
                }
                if ($isTrait && $tokens[$i]['code'] === T_SEMICOLON) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    break;
                }
                if (in_array($tokens[$i]['code'], Tokens::$emptyTokens, true)) {
                    continue;
                }

                break;
            }
            $phpcsFile->fixer->endChangeset();
        }
    }
}
