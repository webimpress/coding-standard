<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Helper;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

use const T_COMMA;
use const T_DOUBLE_ARROW;
use const T_WHITESPACE;

/**
 * @internal
 * @see https://github.com/squizlabs/PHP_CodeSniffer/issues/2745
 *     As PHP_CodeSniffer is not detecting correctly indices in array,
 *     we need to handle it here until it is fixed in there.
 */
trait ArrayTrait
{
    private function getIndices(File $phpcsFile, int $arrayStart, int $arrayEnd) : array
    {
        $tokens = $phpcsFile->getTokens();

        $indices = [];

        $current = $arrayStart;
        while ($next = $phpcsFile->findNext(Tokens::$emptyTokens, $current + 1, $arrayEnd, true)) {
            $end = $this->getNext($phpcsFile, $next, $arrayEnd);

            if ($tokens[$end]['code'] === T_DOUBLE_ARROW) {
                $indexEnd = $phpcsFile->findPrevious(T_WHITESPACE, $end - 1, null, true);
                $valueStart = $phpcsFile->findNext(Tokens::$emptyTokens, $end + 1, null, true);

                $indices[] = [
                    'index_start' => $next,
                    'index_end' => $indexEnd,
                    'arrow' => $end,
                    'value_start' => $valueStart,
                ];
            } else {
                $valueStart = $next;
                $indices[] = ['value_start' => $valueStart];
            }

            $current = $this->getNext($phpcsFile, $valueStart, $arrayEnd);
        }

        return $indices;
    }

    private function getNext(File $phpcsFile, int $ptr, int $arrayEnd) : int
    {
        $tokens = $phpcsFile->getTokens();

        while ($ptr < $arrayEnd) {
            if (isset($tokens[$ptr]['scope_closer'])) {
                $ptr = $tokens[$ptr]['scope_closer'];
            } elseif (isset($tokens[$ptr]['parenthesis_closer'])) {
                $ptr = $tokens[$ptr]['parenthesis_closer'];
            } elseif (isset($tokens[$ptr]['bracket_closer'])) {
                $ptr = $tokens[$ptr]['bracket_closer'];
            }

            if ($tokens[$ptr]['code'] === T_COMMA
                || $tokens[$ptr]['code'] === T_DOUBLE_ARROW
            ) {
                return $ptr;
            }

            ++$ptr;
        }

        return $ptr;
    }
}
