<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;
use function max;
use function strpos;

use const T_ANON_CLASS;
use const T_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_DOC_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_SEMICOLON;
use const T_TRAIT;
use const T_WHITESPACE;

class LineAfterSniff extends AbstractScopeSniff
{
    public function __construct()
    {
        parent::__construct([T_CLASS, T_INTERFACE, T_TRAIT, T_ANON_CLASS], [T_FUNCTION]);
    }

    /**
     * @param int $stackPtr
     * @param int $currScope
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope) : void
    {
        $tokens = $phpcsFile->getTokens();

        // Methods with body.
        if (isset($tokens[$stackPtr]['scope_closer'])) {
            $closer = $tokens[$stackPtr]['scope_closer'];
        } else {
            $closer = $phpcsFile->findNext(T_SEMICOLON, $tokens[$stackPtr]['parenthesis_closer'] + 1);
        }

        $emptyTokens = Tokens::$emptyTokens;
        unset(
            $emptyTokens[T_DOC_COMMENT],
            $emptyTokens[T_DOC_COMMENT_STAR],
            $emptyTokens[T_DOC_COMMENT_WHITESPACE],
            $emptyTokens[T_DOC_COMMENT_TAG],
            $emptyTokens[T_DOC_COMMENT_OPEN_TAG],
            $emptyTokens[T_DOC_COMMENT_CLOSE_TAG],
            $emptyTokens[T_DOC_COMMENT_STRING]
        );

        $lastInLine = $closer;
        while ($tokens[$lastInLine + 1]['line'] === $tokens[$closer]['line']
            && in_array($tokens[$lastInLine + 1]['code'], $emptyTokens, true)
        ) {
            ++$lastInLine;
        }
        while ($tokens[$lastInLine]['code'] === T_WHITESPACE) {
            --$lastInLine;
        }

        $contentAfter = $phpcsFile->findNext(T_WHITESPACE, $lastInLine + 1, null, true);
        if ($contentAfter !== false
            && $tokens[$contentAfter]['line'] - $tokens[$closer]['line'] !== 2
            && $tokens[$contentAfter]['code'] !== T_CLOSE_CURLY_BRACKET
        ) {
            $error = 'Expected 1 blank line after method; %d found';
            $found = max($tokens[$contentAfter]['line'] - $tokens[$closer]['line'] - 1, 0);
            $data = [$found];
            $fix = $phpcsFile->addFixableError($error, $closer, 'BlankLinesAfter', $data);

            if ($fix) {
                if ($found) {
                    $skip = 2;

                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $contentAfter - 1; $i > $closer; --$i) {
                        if ($skip) {
                            if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) !== false) {
                                --$skip;
                            }

                            continue;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                } else {
                    $phpcsFile->fixer->addNewline($lastInLine);
                }
            }
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @param int $stackPtr
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr) : void
    {
        // we process only function inside class/interface/trait
    }
}
