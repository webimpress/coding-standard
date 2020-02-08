<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractArraySniff;
use PHP_CodeSniffer\Util\Tokens;

use function ltrim;
use function str_repeat;
use function strlen;
use function trim;

use const T_CLOSE_SHORT_ARRAY;
use const T_WHITESPACE;

/**
 * Single Line Arrays:
 *   - no spaces after opening bracket (code: `SingleLineSpaceBefore`),
 *   - no spaces before closing bracket (code: `SingleLineSpaceAfter`).
 *
 * Multiline Arrays:
 *   - empty array in one line: `[]` (code: `EmptyArrayInOneLine`),
 *   - no blank line before closing bracket (code: `BlankLineAtTheEnd`),
 *   - one element per line (code: `OneElementPerLine`),
 *   - no blank lines between elements; only allowed before comment (code: `BlankLine`):
 *     ```php
 *     $array = [
 *         'elem1',
 *         'elem2',
 *
 *         // some comment
 *         'key' => 'value',
 *         'foo' => 'baz',
 *     ];
 *     ```
 *   - array closing bracket in new line (code: `ClosingBracketInNewLine`).
 *   - no comment before arrow (code `CommentBeforeArrow`)
 *   - no blank line before arrow (code: `BlankLineBeforeArrow`)
 */
class FormatSniff extends AbstractArraySniff
{
    /**
     * Processes a single-line array definition.
     *
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token
     *     in the stack passed in $tokens.
     * @param int $arrayStart The token that starts the array definition.
     * @param int $arrayEnd The token that ends the array definition.
     * @param array $indices An array of token positions for the array keys,
     *     double arrows, and values.
     */
    protected function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices) : void
    {
        $tokens = $phpcsFile->getTokens();

        // Single-line array - spaces before first element
        if ($tokens[$arrayStart + 1]['code'] === T_WHITESPACE) {
            $error = 'Expected 0 spaces after array bracket opener; %d found';
            $data = [strlen($tokens[$arrayStart + 1]['content'])];
            $fix = $phpcsFile->addFixableError($error, $arrayStart + 1, 'SingleLineSpaceBefore', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($arrayStart + 1, '');
            }
        }

        // Single-line array - spaces before last element
        if ($tokens[$arrayEnd - 1]['code'] === T_WHITESPACE) {
            $error = 'Expected 0 spaces before array bracket closer; %d found';
            $data = [strlen($tokens[$arrayEnd - 1]['content'])];
            $fix = $phpcsFile->addFixableError($error, $arrayEnd - 1, 'SingleLineSpaceAfter', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($arrayEnd - 1, '');
            }
        }
    }

    /**
     * Processes a multi-line array definition.
     *
     * @param File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token
     *     in the stack passed in $tokens.
     * @param int $arrayStart The token that starts the array definition.
     * @param int $arrayEnd The token that ends the array definition.
     * @param array $indices An array of token positions for the array keys,
     *     double arrows, and values.
     */
    protected function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices) : void
    {
        $tokens = $phpcsFile->getTokens();

        $firstContent = $phpcsFile->findNext(T_WHITESPACE, $arrayStart + 1, null, true);
        if ($tokens[$firstContent]['code'] === T_CLOSE_SHORT_ARRAY) {
            $error = 'Empty array must be in one line';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'EmptyArrayInOneLine');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($arrayStart + 1, '');
            }

            return;
        }

        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, $arrayEnd - 1, null, true);
        if ($tokens[$arrayEnd]['line'] > $tokens[$lastContent]['line'] + 1) {
            $error = 'Blank line found at the end of the array';
            $fix = $phpcsFile->addFixableError($error, $arrayEnd - 1, 'BlankLineAtTheEnd');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $lastContent + 1;
                while ($tokens[$i]['line'] !== $tokens[$arrayEnd]['line']) {
                    $phpcsFile->fixer->replaceToken($i, '');
                    ++$i;
                }
                $phpcsFile->fixer->addNewlineBefore($arrayEnd);
                $phpcsFile->fixer->endChangeset();
            }
        }

        foreach ($indices as $element) {
            $start = $element['index_start'] ?? $element['value_start'];

            $nonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, $start - 1, null, true);
            if ($tokens[$start]['line'] === $tokens[$nonEmpty]['line']) {
                $error = 'There must be one array element per line';
                $fix = $phpcsFile->addFixableError($error, $start, 'OneElementPerLine');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addNewline($nonEmpty);
                    if ($tokens[$nonEmpty + 1]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($nonEmpty + 1, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            }

            $prev = $phpcsFile->findPrevious(T_WHITESPACE, $start - 1, null, true);
            if ($tokens[$prev]['line'] < $tokens[$start]['line'] - 1) {
                $blankLine = $tokens[$prev]['line'] === $tokens[$prev + 1]['line'] ? $prev + 2 : $prev + 1;
                $error = 'Blank line is not allowed here';

                $fix = $phpcsFile->addFixableError($error, $blankLine, 'BlankLine');
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($blankLine, '');
                }
            }

            if (! isset($element['arrow'])) {
                continue;
            }

            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $element['arrow'] - 1, null, true);
            $content = $phpcsFile->getTokensAsString($prev + 1, $element['arrow'] - $prev - 1);
            if (trim($content) !== '') {
                $error = 'Comment is not allowed before arrow in array';
                $comment = $phpcsFile->findNext(Tokens::$commentTokens, $prev + 1);

                $fix = $phpcsFile->addFixableError($error, $comment, 'CommentBeforeArrow');
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContentBefore($element['index_start'], ltrim($content));
                    for ($i = $comment; $i < $element['arrow']; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            } elseif ($tokens[$prev]['line'] < $tokens[$element['arrow']]['line'] - 1) {
                $error = 'Blank line is not allowed before arrow in array';

                $fix = $phpcsFile->addFixableError($error, $prev + 2, 'BlankLineBeforeArrow');
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev + 2, '');
                }
            }
        }

        if ($first = $phpcsFile->findFirstOnLine([], $arrayEnd, true)) {
            if ($first < $arrayEnd - 1) {
                $error = 'Array closing bracket should be in new line';
                $fix = $phpcsFile->addFixableError($error, $arrayEnd, 'ClosingBracketInNewLine');

                if ($fix) {
                    $first = $phpcsFile->findFirstOnLine([], $arrayStart, true);
                    $indent = $tokens[$first]['code'] === T_WHITESPACE
                        ? strlen($tokens[$first]['content'])
                        : 0;

                    $phpcsFile->fixer->beginChangeset();
                    if ($indent > 0) {
                        $phpcsFile->fixer->addContentBefore($arrayEnd, str_repeat(' ', $indent));
                    }
                    $phpcsFile->fixer->addNewlineBefore($arrayEnd);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }
}
