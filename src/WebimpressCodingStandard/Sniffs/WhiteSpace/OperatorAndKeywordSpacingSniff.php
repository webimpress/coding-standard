<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;

use const T_AS;
use const T_DECLARE;
use const T_FN_ARROW;
use const T_INSTANCEOF;
use const T_INSTEADOF;
use const T_WHITESPACE;

class OperatorAndKeywordSpacingSniff extends OperatorSpacingSniff
{
    /** @var bool Override default value from parent sniff */
    public $ignoreNewlines = true;

    /** @var bool Override default value from parent sniff */
    public $ignoreSpacingBeforeAssignments = false;

    /** @var int[] */
    private $doNotIgnoreNewLineForTokens = [
        T_INSTEADOF,
        T_INSTANCEOF,
        T_AS,
        T_FN_ARROW,
    ];

    public function register() : array
    {
        $tokens = parent::register();
        $tokens += Tokens::$booleanOperators;
        $tokens[] = T_AS;
        $tokens[] = T_INSTEADOF;
        $tokens[] = T_FN_ARROW;

        // Also register the contexts we want to specifically skip over.
        $tokens[] = T_DECLARE;

        return $tokens;
    }

    /**
     * @param int $stackPtr
     * @return null|int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Skip over declare statements as those should be handled by different sniffs.
        if ($tokens[$stackPtr]['code'] === T_DECLARE) {
            if (isset($tokens[$stackPtr]['parenthesis_closer']) === false) {
                // Parse error / live coding.
                return $phpcsFile->numTokens;
            }

            return $tokens[$stackPtr]['parenthesis_closer'];
        }

        $originalValue = $this->ignoreNewlines;
        if (in_array($tokens[$stackPtr]['code'], $this->doNotIgnoreNewLineForTokens, true)) {
            $this->ignoreNewlines = false;
        }

        parent::process($phpcsFile, $stackPtr);

        if ($this->ignoreNewlines === true) {
            if (isset($tokens[$stackPtr + 2])
                && $tokens[$stackPtr + 1]['code'] === T_WHITESPACE
                && $tokens[$stackPtr + 2]['line'] !== $tokens[$stackPtr]['line']
            ) {
                $error = 'Expected 1 space after "%s"; newline found';
                $data = [$tokens[$stackPtr]['content']];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfter', $data);
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $j = $stackPtr - 1;
                    while ($tokens[$j]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($j, '');
                        --$j;
                    }
                    $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
                    $phpcsFile->fixer->replaceToken($stackPtr, '');
                    $phpcsFile->fixer->addContentBefore($next, $tokens[$stackPtr]['content'] . ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            }

            $prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
            if ($tokens[$prev]['line'] + 1 < $tokens[$stackPtr]['line']) {
                $j = $stackPtr - 1;
                while ($tokens[$j]['line'] === $tokens[$stackPtr]['line']) {
                    --$j;
                }

                $error = 'Empty line is not allowed here';
                $fix = $phpcsFile->addFixableError($error, $j, 'EmptyLine');

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($j, '');
                }
            }
        }

        $this->ignoreNewlines = $originalValue;
    }
}
