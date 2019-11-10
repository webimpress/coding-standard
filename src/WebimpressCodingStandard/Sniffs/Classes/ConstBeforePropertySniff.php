<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_ANON_CLASS;
use const T_CLASS;
use const T_CONST;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_VAR;
use const T_VARIABLE;

class ConstBeforePropertySniff extends AbstractScopeSniff
{
    public function __construct()
    {
        parent::__construct([T_ANON_CLASS, T_CLASS, T_INTERFACE], [T_CONST]);
    }

    /**
     * @param int $stackPtr
     * @param int $currScope
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        $scopeOpener = $tokens[$currScope]['scope_opener'];

        $find = $phpcsFile->findNext([T_FUNCTION, T_VARIABLE], $scopeOpener, $stackPtr);
        if ($find) {
            $error = $tokens[$find]['code'] === T_FUNCTION
                ? 'Method declaration forbidden in line %d before constant declaration'
                : 'Property declaration forbidden in line %d before constant declaration';

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'BeforeConstant', [$tokens[$find]['line']]);
            if ($fix) {
                $before = $phpcsFile->findPrevious(Tokens::$emptyTokens + Tokens::$methodPrefixes + [
                    T_VAR => T_VAR,
                ], $find - 1, null, true);

                $from = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens + Tokens::$scopeModifiers,
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
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
        // do not process constants outside the scope
    }
}
