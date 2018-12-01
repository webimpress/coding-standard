<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;

use const T_CONST;

class ConstVisibilitySniff extends AbstractScopeSniff
{
    /**
     * @var bool
     */
    public $fixable = false;

    public function __construct()
    {
        parent::__construct(Tokens::$ooScopeTokens, [T_CONST]);
    }

    /**
     * @param int $stackPtr
     * @param int $currScope
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope) : void
    {
        $tokens = $phpcsFile->getTokens();
        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);

        if (! in_array($tokens[$prev]['code'], Tokens::$scopeModifiers, true)) {
            $error = 'Missing constant visibility';

            if ($this->fixable) {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'MissingVisibility');

                if ($fix) {
                    $phpcsFile->fixer->addContentBefore($stackPtr, 'public ');
                }
            } else {
                $phpcsFile->addError($error, $stackPtr, 'MissingVisibility');
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
        // do not process constant outside the scope
    }
}
