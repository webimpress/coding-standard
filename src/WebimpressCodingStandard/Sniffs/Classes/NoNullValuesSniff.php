<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;
use function strpos;
use function strtolower;
use function substr;

use const T_EQUAL;
use const T_NULL;
use const T_WHITESPACE;

class NoNullValuesSniff extends AbstractVariableSniff
{
    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$next]['code'] !== T_EQUAL) {
            return;
        }

        $value = $phpcsFile->findNext(Tokens::$emptyTokens, $next + 1, null, true);
        if ($tokens[$value]['code'] === T_NULL) {
            $props = $phpcsFile->getMemberProperties($stackPtr);

            $type = strtolower($props['type']);

            $nullableType = $props['nullable_type'] === true
                || strpos($type, '|null|') !== false
                || strpos($type, 'null|') === 0
                || substr($type, -5) === '|null';

            if ($type !== '' && $nullableType === true) {
                return;
            }

            $error = $type !== '' && $nullableType === false
                ? 'Default null value for not-nullable property is invalid'
                : 'Default null value for the property is redundant';

            $code = $type !== '' && $nullableType === false
                ? 'Invalid'
                : 'NullValue';

            $fix = $phpcsFile->addFixableError($error, $value, $code);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $stackPtr + 1; $i <= $value; ++$i) {
                    if (! in_array($tokens[$i]['code'], [T_WHITESPACE, T_EQUAL, T_NULL], true)) {
                        continue;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr) : void
    {
        // Normal variables are not processed in this sniff.
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) : void
    {
        // Variables in string are not processed in this sniff.
    }
}
