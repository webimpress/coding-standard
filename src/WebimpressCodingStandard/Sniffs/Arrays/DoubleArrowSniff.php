<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractArraySniff;
use PHP_CodeSniffer\Util\Tokens;

use function abs;
use function str_repeat;

use const T_WHITESPACE;

/**
 * - check if `=>` in array is at the end of the line (code: `AtTheEnd`)
 * - check spaces before `=>` - by default expected only one space, but can
 *   be configured by setting `maxPadding` to align array arrows `=>` in
 *   multi-line arrays. New group of alignment starts when there is an empty line, line with comment or arrow
 *   is in new line.
 *   (code: `SpacesBefore`)
 */
class DoubleArrowSniff extends AbstractArraySniff
{
    /**
     * The maximum amount of padding before the alignment is ignored.
     *
     * If the amount of padding required to align this assignment with the
     * surrounding assignments exceeds this number, the assignment will be
     * ignored and no errors or warnings will be thrown.
     *
     * @var int
     */
    public $maxPadding = 1;

    /**
     * Whether array arrows which are in new lines should be ignored when
     * aligning array arrows (when $maxPadding property is > 1).
     *
     * @var bool
     */
    public $ignoreNewLineArrayArrow = true;

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
    protected function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        foreach ($indices as $data) {
            if (! isset($data['arrow'])) {
                continue;
            }

            $this->checkSpace($phpcsFile, $data);
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
    protected function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        $tokens = $phpcsFile->getTokens();

        $spaces = $this->calculateExpectedSpaces($phpcsFile, $indices);

        foreach ($indices as $k => $data) {
            if (! isset($data['arrow'])) {
                continue;
            }

            $arrow = $tokens[$data['arrow']];
            $value = $tokens[$data['value_start']];

            if ($value['line'] > $arrow['line']) {
                $error = 'Double arrow in array cannot be at the end of the line';
                $fix = $phpcsFile->addFixableError($error, $data['arrow'], 'AtTheEnd');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($data['arrow'], '');
                    $token = $data['arrow'] - 1;
                    while ($tokens[$token]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($token, '');
                        --$token;
                    }
                    $phpcsFile->fixer->addContentBefore($data['value_start'], '=> ');
                    $phpcsFile->fixer->endChangeset();
                }

                continue;
            }

            $index = $tokens[$data['index_end']];
            if ($index['line'] === $arrow['line']) {
                $this->checkSpace($phpcsFile, $data, $spaces[$k] ?? 1);
            } elseif (! $this->ignoreNewLineArrayArrow && $index['line'] < $arrow['line']) {
                $this->checkSpace($phpcsFile, $data, $spaces[$k] ?? 0);
            }
        }
    }

    private function calculateExpectedSpaces(File $phpcsFile, array $indices) : array
    {
        if ($this->maxPadding <= 1) {
            return [];
        }

        $tokens = $phpcsFile->getTokens();

        $newLineArrow = [];

        $chars = [];
        foreach ($indices as $k => $data) {
            if (! isset($data['arrow'])) {
                continue;
            }

            $arrow = $tokens[$data['arrow']];
            $index = $tokens[$data['index_end']];

            if ($arrow['line'] !== $index['line']) {
                if (! $this->ignoreNewLineArrayArrow) {
                    $newLineArrow[$k] = true;
                    $chars[$k] = $index['column'] - 1;
                }
                continue;
            }

            $chars[$k] = $index['column'] + $index['length'];
        }

        $res = [];
        $i = null;
        $min = null;
        $current = null;
        foreach ($chars as $k => $length) {
            if ($min === null) {
                $min = $length;
                $current = $length;
            }

            if (abs($length - $min) > $this->maxPadding) {
                $res[$i] = $current;
                $min = $length;
                $current = $length;
            } else {
                if ($k > 0) {
                    $valueEnd = $phpcsFile->findPrevious(
                        Tokens::$emptyTokens,
                        $indices[$k]['index_start'] - 1,
                        null,
                        true
                    );

                    if ($valueEnd && $tokens[$valueEnd]['line'] !== $tokens[$indices[$k]['index_start']]['line'] - 1) {
                        $res[$i] = $current;
                        $min = $length;
                        $current = $length;
                    }
                }

                if ($length < $min) {
                    $min = $length;
                }

                if ($length > $current) {
                    $current = $length;
                }
            }

            if (! isset($chars[$k + 1])) {
                $res[$k] = $current;
                $min = null;
                $current = null;
            }

            $i = $k;
        }

        $spaces = [];
        foreach ($chars as $k => $length) {
            foreach ($res as $i => $max) {
                if ($k <= $i) {
                    break;
                }
            }

            $spaces[$k] = isset($newLineArrow[$k]) ? $max : $max - $length + 1;
        }

        return $spaces;
    }

    private function checkSpace(File $phpcsFile, array $element, int $expectedSpaces = 1) : void
    {
        $tokens = $phpcsFile->getTokens();

        $space = $tokens[$element['arrow'] - 1];
        $expected = str_repeat(' ', $expectedSpaces);
        if ($space['code'] === T_WHITESPACE
            && $space['line'] === $tokens[$element['arrow']]['line']
            && $space['content'] !== $expected
        ) {
            $error = 'Expected %s before double arrow; %d found';
            $data = [
                $expectedSpaces === 1 ? '1 space' : $expectedSpaces . ' spaces',
                $space['length'],
            ];
            $fix = $phpcsFile->addFixableError($error, $element['arrow'], 'SpacesBefore', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($element['arrow'] - 1, $expected);
            }
        } elseif ($expected !== ''
            && ($space['code'] !== T_WHITESPACE
                || $space['line'] !== $tokens[$element['arrow']]['line'])
        ) {
            $error = 'Expected %s before double arrow; 0 found';
            $data = [
                $expectedSpaces === 1 ? '1 space' : $expectedSpaces . ' spaces',
            ];
            $fix = $phpcsFile->addFixableError($error, $element['arrow'], 'SpacesBefore', $data);

            if ($fix) {
                $phpcsFile->fixer->addContentBefore($element['arrow'], $expected);
            }
        }
    }
}
