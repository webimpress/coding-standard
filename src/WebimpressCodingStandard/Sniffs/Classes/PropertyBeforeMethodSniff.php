<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Tokens;

use function array_reverse;
use function key;

use const T_FUNCTION;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_STATIC;
use const T_STRING;
use const T_VAR;

class PropertyBeforeMethodSniff extends AbstractVariableSniff
{
    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $scopePtr = key(array_reverse($tokens[$stackPtr]['conditions'], true));
        $scopeOpener = $tokens[$scopePtr]['scope_opener'];

        $find = $phpcsFile->findNext(T_FUNCTION, $scopeOpener + 1, $stackPtr);
        if ($find) {
            $error = 'Method declaration forbidden in line %d before property declaration';

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'BeforeProperty', [$tokens[$find]['line']]);
            if ($fix) {
                $before = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens + Tokens::$methodPrefixes,
                    $find - 1,
                    null,
                    true
                );

                $from = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens + Tokens::$scopeModifiers + [
                        T_VAR => T_VAR,
                        T_STATIC => T_STATIC,
                        // as of PHP 7.4 we can have defined types with properties
                        T_NS_SEPARATOR => T_NS_SEPARATOR,
                        T_STRING => T_STRING,
                        T_NULLABLE => T_NULLABLE,
                    ],
                    $stackPtr - 1,
                    null,
                    true
                );
                $eos = $phpcsFile->findEndOfStatement($stackPtr);

                $toMove = $phpcsFile->getTokensAsString($from + 1, $eos - $from);

                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContent($before, $toMove);
                for ($i = $from + 1; $i <= $eos; ++$i) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr) : void
    {
        // Normal variables are not processed in this sniff.
    }

    /**
     * @codeCoverageIgnore
     *
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) : void
    {
        // Variables in string are not processed in this sniff.
    }
}
