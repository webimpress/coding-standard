<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use const T_ARRAY_CAST;
use const T_BOOL_CAST;
use const T_BOOLEAN_NOT;
use const T_DOUBLE_CAST;
use const T_INT_CAST;
use const T_OBJECT_CAST;
use const T_STRING_CAST;
use const T_WHITESPACE;

class TypeCastingSniff implements Sniff
{
    /**
     * @var array
     */
    public $castMap = [
        T_INT_CAST => '(int)',
        T_STRING_CAST => '(string)',
        T_DOUBLE_CAST => '(float)',
        T_ARRAY_CAST => '(array)',
        T_BOOL_CAST => '(bool)',
        T_OBJECT_CAST => '(object)',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return Tokens::$castTokens
            + [T_BOOLEAN_NOT => T_BOOLEAN_NOT];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_BOOLEAN_NOT) {
            $nextToken = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
            if (! $nextToken || $tokens[$nextToken]['code'] !== T_BOOLEAN_NOT) {
                return;
            }
            $error = 'Double negation casting is not allowed. Please use (bool) instead';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'DoubleNot');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, '(bool)');
                $phpcsFile->fixer->replaceToken($nextToken, '');
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        $code = $tokens[$stackPtr]['code'];
        if (! isset($this->castMap[$code])) {
            $error = 'Type cast %s is disallowed';
            $data = [$tokens[$stackPtr]['content']];

            $phpcsFile->addError($error, $stackPtr, 'DisallowedCast', $data);
            return;
        }

        $content = $tokens[$stackPtr]['content'];
        $expected = $this->castMap[$code];
        if ($content !== $expected) {
            $error = 'Invalid casting used. Expected %s, found %s';
            $data = [
                $expected,
                $content,
            ];
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Invalid', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        }
    }
}
