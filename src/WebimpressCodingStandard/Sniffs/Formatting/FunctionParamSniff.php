<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_BITWISE_AND;
use const T_CLOSURE;
use const T_ELLIPSIS;
use const T_FN;
use const T_FUNCTION;
use const T_WHITESPACE;

class FunctionParamSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CLOSURE, T_FN, T_FUNCTION];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        // Skip broken function declarations.
        if (! isset($token['scope_opener'], $token['parenthesis_opener'])) {
            return;
        }

        $params = $phpcsFile->getMethodParameters($stackPtr);
        foreach ($params as $param) {
            if (! $param['type_hint']) {
                continue;
            }

            $last = $phpcsFile->findPrevious(
                Tokens::$emptyTokens + [T_BITWISE_AND => T_BITWISE_AND, T_ELLIPSIS => T_ELLIPSIS],
                $param['token'] - 1,
                null,
                true
            );

            if ($tokens[$last + 1]['content'] !== ' ') {
                $error = 'Expected one space after param type hint';
                $fix = $phpcsFile->addFixableError($error, $last + 1, 'SpaceAfterTypeHint');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    if ($tokens[$last + 1]['code'] === T_WHITESPACE) {
                        $token = $last + 1;
                        $phpcsFile->fixer->replaceToken($token, ' ');
                        while ($tokens[++$token]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken($token, '');
                        }
                    } else {
                        $phpcsFile->fixer->addContent($last, ' ');
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }
}
