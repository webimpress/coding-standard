<?php

declare(strict_types=1);

namespace WebimpressCodingStandard;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;

use function strtolower;

use const T_ANON_CLASS;
use const T_CLASS;
use const T_OPEN_PARENTHESIS;
use const T_TRAIT;

/**
 * @internal
 */
class CodingStandard
{
    public const TAG_WITH_TYPE = [
        '@var',
        '@param',
        '@return',
        '@throws',
        '@method',
        '@property',
        '@property-read',
        '@property-write',
    ];

    /**
     * Returns a valid variable type for param/var tag.
     *
     * If type is not one of the standard type, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string $varType The variable type to process.
     */
    public static function suggestType(string $varType) : string
    {
        $lowerVarType = strtolower($varType);
        switch ($lowerVarType) {
            case 'bool':
            case 'boolean':
                return 'bool';
            case 'int':
            case 'integer':
                return 'int';
        }

        return Common::suggestType($varType);
    }

    public static function isTraitUse(File $phpcsFile, int $stackPtr) : bool
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return false;
        }

        // Ignore global USE keywords.
        if (! $phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_ANON_CLASS])) {
            return false;
        }

        return true;
    }

    public static function isGlobalUse(File $phpcsFile, int $stackPtr) : bool
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return false;
        }

        // Ignore USE keywords for traits.
        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_ANON_CLASS])) {
            return false;
        }

        return true;
    }
}
