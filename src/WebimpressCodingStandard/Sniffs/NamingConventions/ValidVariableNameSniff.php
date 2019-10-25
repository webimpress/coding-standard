<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

use function ltrim;
use function preg_match_all;

use const T_DOUBLE_COLON;
use const T_WHITESPACE;

class ValidVariableNameSniff extends AbstractVariableSniff
{
    /**
     * @var array
     */
    protected $phpReservedVars = [
        '_SERVER' => true,
        '_GET' => true,
        '_POST' => true,
        '_REQUEST' => true,
        '_SESSION' => true,
        '_ENV' => true,
        '_COOKIE' => true,
        '_FILES' => true,
        'GLOBALS' => true,
    ];

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        // If it's a php reserved var, then its ok.
        if (isset($this->phpReservedVars[$varName])) {
            return;
        }

        $objOperator = $phpcsFile->findPrevious([T_WHITESPACE], $stackPtr - 1, null, true);
        if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
            return; // skip MyClass::$variable, there might be no control over the declaration
        }

        if (! Common::isCamelCaps($varName, false, true, true)) {
            $error = 'Variable "$%s" is not in valid camel caps format';
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        if (! Common::isCamelCaps($varName, false, true, true)) {
            $error = 'Property "$%s" is not in valid camel caps format';
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCapsProperty', $data);
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];

        $pattern = '|(?<!\\\\)(?:\\\\{2})*\${?([a-zA-Z0-9_]+)}?|';
        preg_match_all($pattern, $content, $matches);

        foreach ($matches[1] ?? [] as $varName) {
            if (! Common::isCamelCaps($varName, false, true, true)) {
                $error = 'Variable "$%s" is not in valid camel caps format';
                $data = [$varName];
                $phpcsFile->addError($error, $stackPtr, 'NotCamelCapsInString', $data);
            }
        }
    }
}
