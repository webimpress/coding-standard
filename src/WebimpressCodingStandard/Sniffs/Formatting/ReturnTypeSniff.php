<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;
use function str_repeat;
use function strtolower;
use function trim;

use const T_CALLABLE;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_COLON;
use const T_FN;
use const T_FN_ARROW;
use const T_FUNCTION;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_OPEN_CURLY_BRACKET;
use const T_PARENT;
use const T_SELF;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;
use const T_WHITESPACE;

class ReturnTypeSniff implements Sniff
{
    /**
     * @var int
     */
    public $spacesBeforeColon = 1;

    /**
     * @var int
     */
    public $spacesAfterColon = 1;

    /**
     * @var string[]
     */
    private $simpleReturnTypes = [
        'void',
        'int',
        'float',
        'object',
        'string',
        'array',
        'iterable',
        'callable',
        'parent',
        'self',
        'bool',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CLOSURE, T_FN, T_FUNCTION];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->spacesBeforeColon = (int) $this->spacesBeforeColon;
        $this->spacesAfterColon = (int) $this->spacesAfterColon;

        $tokens = $phpcsFile->getTokens();

        $parenthesisCloser = $tokens[$stackPtr]['parenthesis_closer'];
        $eol = $phpcsFile->findNext([T_FN_ARROW, T_SEMICOLON, T_OPEN_CURLY_BRACKET], $stackPtr + 1);
        if ($use = $phpcsFile->findNext(T_USE, $parenthesisCloser + 1, $eol)) {
            $parenthesisCloser = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, $use + 1);
        }

        // There is no return type declaration on the function
        if (! $colon = $phpcsFile->findNext(T_COLON, $parenthesisCloser + 1, $eol)) {
            return;
        }

        // Check if between the closing parenthesis and return type are only allowed tokens.
        if ($invalid = $phpcsFile->findNext(
            [
                T_CALLABLE,
                T_COLON,
                T_NS_SEPARATOR,
                T_NULLABLE,
                T_SELF,
                T_STRING,
                T_PARENT,
                T_WHITESPACE,
            ],
            $parenthesisCloser + 1,
            $eol,
            true
        )) {
            $error = 'Return type declaration contains invalid token %s';
            $data = [$tokens[$invalid]['type']];
            $phpcsFile->addError($error, $invalid, 'InvalidToken', $data);

            return;
        }

        $nullable = $phpcsFile->findNext(T_NULLABLE, $colon + 1, $eol);

        $this->checkSpacesBeforeColon($phpcsFile, $colon);
        $this->checkSpacesAfterColon($phpcsFile, $colon);

        $first = $phpcsFile->findNext(Tokens::$emptyTokens, ($nullable ?: $colon) + 1, null, true);
        $last = $phpcsFile->findPrevious(Tokens::$emptyTokens, $eol - 1, null, true);

        if ($space = $phpcsFile->findNext(T_WHITESPACE, $first + 1, $last)) {
            $error = 'Return type declaration contains unexpected white characters';
            $fix = $phpcsFile->addFixableError($error, $space, 'Spaces');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($space, '');
            }

            return;
        }

        $returnType = trim($phpcsFile->getTokensAsString($first, $last - $first + 1));

        if (in_array(strtolower($returnType), $this->simpleReturnTypes, true)
            && ! in_array($returnType, $this->simpleReturnTypes, true)
        ) {
            $error = 'Simple return type must be lowercase. Found "%s", expected "%s"';
            $data = [
                $returnType,
                strtolower($returnType),
            ];
            $fix = $phpcsFile->addFixableError($error, $first, 'LowerCaseSimpleType', $data);

            if ($fix) {
                $phpcsFile->fixer->replaceToken($first, strtolower($returnType));
            }
        }
    }

    /**
     * Check if token before colon match configured number of spaces.
     */
    private function checkSpacesBeforeColon(File $phpcsFile, int $colon) : void
    {
        $tokens = $phpcsFile->getTokens();

        // The whitespace before colon is not expected and it is not present.
        if ($this->spacesBeforeColon === 0
            && $tokens[$colon - 1]['code'] !== T_WHITESPACE
        ) {
            return;
        }

        $expected = str_repeat(' ', $this->spacesBeforeColon);

        // Previous token contains expected number of spaces,
        // and before whitespace there is close parenthesis token.
        if ($this->spacesBeforeColon > 0
            && $tokens[$colon - 1]['content'] === $expected
            && $tokens[$colon - 2]['code'] === T_CLOSE_PARENTHESIS
        ) {
            return;
        }

        $error = 'There must be exactly %d space(s) between the closing parenthesis and the colon'
            . ' when declaring a return type for a function';
        $data = [$this->spacesBeforeColon];
        $fix = $phpcsFile->addFixableError($error, $colon - 1, 'SpacesBeforeColon', $data);

        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            if ($tokens[$colon - 1]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($colon - 1, $expected);
                if (isset($tokens[$colon - 2]) && $tokens[$colon - 2]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($colon - 2, '');
                }
            } else {
                $phpcsFile->fixer->addContentBefore($colon, $expected);
            }
            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * Check if token after colon match configured number of spaces.
     */
    private function checkSpacesAfterColon(File $phpcsFile, int $colon) : void
    {
        $tokens = $phpcsFile->getTokens();

        // The whitespace after colon is not expected and it is not present.
        if ($this->spacesAfterColon === 0
            && $tokens[$colon + 1]['code'] !== T_WHITESPACE
        ) {
            return;
        }

        $expected = str_repeat(' ', $this->spacesAfterColon);

        // Next token contains expected number of spaces.
        if ($this->spacesAfterColon > 0
            && $tokens[$colon + 1]['content'] === $expected
        ) {
            return;
        }

        $error = 'There must be exactly %d space(s) between the colon and return type'
            . ' when declaring a return type for a function';
        $data = [$this->spacesAfterColon];
        $fix = $phpcsFile->addFixableError($error, $colon, 'SpacesAfterColon', $data);

        if ($fix) {
            if ($tokens[$colon + 1]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($colon + 1, $expected);
            } else {
                $phpcsFile->fixer->addContent($colon, $expected);
            }
        }
    }
}
