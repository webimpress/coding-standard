<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function constant;
use function count;
use function in_array;
use function str_repeat;
use function strlen;
use function strtoupper;
use function trim;
use function ucfirst;

use const T_ANON_CLASS;
use const T_COMMA;
use const T_IMPLEMENTS;
use const T_NS_SEPARATOR;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_WHITESPACE;

class AnonymousClassDeclarationSniff implements Sniff
{
    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    public $indent = 4;

    /**
     * @var bool
     */
    public $requireParenthesis = true;

    /**
     * @var int
     */
    public $spacesBeforeBracket = 0;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_ANON_CLASS];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->spacesBeforeBracket = (int) $this->spacesBeforeBracket;

        $tokens = $phpcsFile->getTokens();
        $scopeOpener = $tokens[$stackPtr]['scope_opener'];

        if ($opener = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr + 1, $scopeOpener)) {
            $content = $phpcsFile->getTokensAsString($stackPtr + 1, $opener - $stackPtr - 1);
            $expected = str_repeat(' ', $this->spacesBeforeBracket);

            if ($content !== $expected) {
                $error = 'Expected %d space(s) before anonymous class parenthesis';
                $data = [$this->spacesBeforeBracket];

                if (trim($content) === '') {
                    $fix = $phpcsFile->addFixableError($error, $opener, 'SpacesBeforeBracket', $data);

                    if ($fix) {
                        if ($opener === $stackPtr + 1) {
                            $phpcsFile->fixer->addContent($stackPtr, ' ');
                        } else {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($stackPtr + 1, $expected);
                            for ($i = $stackPtr + 2; $i < $opener; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }
                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                } else {
                    $error = 'Unexpected content before anonymous class parenthesis';
                    $phpcsFile->addError($error, $opener - 1, 'UnexpectedContentBeforeBracket');
                }
            }

            $closer = $tokens[$opener]['parenthesis_closer'];
            $nonEmptyContent = $phpcsFile->findNext(Tokens::$emptyTokens, $opener + 1, $closer, true);

            if (! $nonEmptyContent && ! $this->requireParenthesis) {
                $error = 'Parenthesis are redundant with anonymous class';
                $fix = $phpcsFile->addFixableError($error, $opener, 'ParenthesisRedundant');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($opener, '');
                    $phpcsFile->fixer->replaceToken($closer, '');
                    $phpcsFile->fixer->endChangeset();
                }
            } elseif ($tokens[$closer]['line'] > $tokens[$opener]['line']) {
                // Closer should be in the same line
                if (! $nonEmptyContent) {
                    $error = 'Closing bracket should be in the same line as opening';
                    $fix = $phpcsFile->addFixableError($error, $closer, 'ClosingBracketAfterOpening');

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->addContent($opener, ')');
                        $phpcsFile->fixer->replaceToken($closer, '');
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    $this->processBracket($phpcsFile, $opener);
                }
            }
        } elseif ($this->requireParenthesis) {
            $error = 'Parenthesis must be used with anonymous class';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ParenthesisRequired');

            if ($fix) {
                $phpcsFile->fixer->addContent(
                    $stackPtr,
                    str_repeat(' ', $this->spacesBeforeBracket) . '()'
                );
            }
        }

        $classIndent = 0;
        for ($i = $stackPtr - 1; $i > 0; $i--) {
            if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                continue;
            }

            // We changed lines.
            if ($tokens[$i + 1]['code'] === T_WHITESPACE) {
                $classIndent = $tokens[$i + 1]['length'];
            }

            break;
        }

        $this->processExtendsAndImplements($phpcsFile, $classIndent, $opener ? $closer : $stackPtr, $scopeOpener);
    }

    private function processBracket(File $phpcsFile, int $openBracket) : void
    {
        $tokens = $phpcsFile->getTokens();
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

        // The open bracket should be the last thing on the line.
        if ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']) {
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, $openBracket + 1, null, true);
            if ($tokens[$next]['line'] !== $tokens[$openBracket]['line'] + 1) {
                $error = 'The first parameter of a multi-line anonymous function declaration'
                    . ' must be on the line after the opening bracket';
                $fix = $phpcsFile->addFixableError($error, $next, 'FirstParamSpacing');
                if ($fix) {
                    $phpcsFile->fixer->addNewline($openBracket);
                }
            }
        }

        // Each line between the brackets should contain a single parameter.
        $lastComma = null;
        for ($i = $openBracket + 1; $i < $closeBracket; ++$i) {
            // Skip brackets, like arrays, as they can contain commas.
            if (isset($tokens[$i]['bracket_opener'])) {
                $i = $tokens[$i]['bracket_closer'];
                continue;
            }

            if (isset($tokens[$i]['parenthesis_opener'])) {
                $i = $tokens[$i]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$i]['code'] !== T_COMMA) {
                continue;
            }

            $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i + 1, null, true);
            if ($tokens[$next]['line'] === $tokens[$i]['line']) {
                $error = 'Multi-line anonymous function declarations must define one parameter per line';
                $fix = $phpcsFile->addFixableError($error, $next, 'OneParamPerLine');

                if ($fix) {
                    $phpcsFile->fixer->addNewline($i);
                }
            }
        }
    }

    private function processExtendsAndImplements(
        File $phpcsFile,
        int $classIndent,
        int $stackPtr,
        int $openingBrace
    ) : void {
        $tokens = $phpcsFile->getTokens();

        // Check positions of the extends and implements keywords.
        foreach (['extends', 'implements'] as $keywordType) {
            $keyword = $phpcsFile->findNext(constant('T_' . strtoupper($keywordType)), $stackPtr + 1, $openingBrace);
            if (! $keyword) {
                continue;
            }

            if ($tokens[$keyword]['line'] !== $tokens[$stackPtr]['line']) {
                $error = 'The %s keyword must be on the same line as the anonymous class definition';
                $data = [$keywordType];
                $fix = $phpcsFile->addFixableError($error, $keyword, ucfirst($keywordType) . 'Line', $data);
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $stackPtr + 1; $i < $keyword; ++$i) {
                        if ($tokens[$i]['line'] !== $tokens[$i + 1]['line']) {
                            $phpcsFile->fixer->substrToken($i, 0, -1 * strlen($phpcsFile->eolChar));
                        }
                    }

                    $phpcsFile->fixer->addContentBefore($keyword, ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                // Check the whitespace before. Whitespace after is checked
                // later by looking at the whitespace before the first class name
                // in the list.
                if ($tokens[$keyword - 1]['content'] !== ' ') {
                    $error = 'Expected 1 space before %s keyword';
                    $data = [$keywordType];
                    $fix = $phpcsFile->addFixableError($error, $keyword, 'SpaceBefore' . ucfirst($keywordType), $data);
                    if ($fix) {
                        if ($tokens[$keyword - 1]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken($keyword - 1, ' ');
                        } else {
                            $phpcsFile->fixer->addContentBefore($keyword, ' ');
                        }
                    }
                }
            }
        }

        $implements = $phpcsFile->findNext(T_IMPLEMENTS, $stackPtr + 1, $openingBrace);
        $multiLineImplements = false;
        if ($implements !== false) {
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $openingBrace - 1, $implements, true);
            if ($tokens[$prev]['line'] !== $tokens[$implements]['line']) {
                $multiLineImplements = true;
            }
        }

        $classNames = [];
        $nextClass = $stackPtr;
        while ($nextClass = $phpcsFile->findNext([T_STRING, T_IMPLEMENTS], $nextClass + 1, $openingBrace)) {
            $classNames[] = $nextClass;
        }

        $classCount = count($classNames);

        $checkingImplements = false;
        $implementsToken = null;
        foreach ($classNames as $n => $className) {
            if ($tokens[$className]['code'] === T_IMPLEMENTS) {
                $checkingImplements = true;
                $implementsToken = $className;
                continue;
            }

            if ($checkingImplements === true
                && $multiLineImplements === true
                && ($tokens[$className - 1]['code'] !== T_NS_SEPARATOR
                    || $tokens[$className - 2]['code'] !== T_STRING)
            ) {
                $prev = $phpcsFile->findPrevious([T_NS_SEPARATOR, T_WHITESPACE], $className - 1, $implements, true);

                if ($prev === $implementsToken
                    && $tokens[$className]['line'] !== $tokens[$prev]['line'] + 1
                ) {
                    $error = 'The first item in a multi-line implements list'
                        . ' must be on the line following the implements keyword';
                    $fix = $phpcsFile->addFixableError($error, $className, 'FirstInterfaceSameLine');

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $prev + 1; $i < $className; ++$i) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->addNewline($prev);
                        $phpcsFile->fixer->endChangeset();
                    }
                } elseif ($tokens[$prev]['line'] !== $tokens[$className]['line'] - 1) {
                    $error = 'Only one interface may be specified per line in a multi-line implements declaration';
                    $fix = $phpcsFile->addFixableError($error, $className, 'InterfaceSameLine');

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $prev + 1; $i < $className; ++$i) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->addNewline($prev);
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    $prev = $phpcsFile->findPrevious(T_WHITESPACE, $className - 1, $implements);
                    if ($tokens[$prev]['line'] !== $tokens[$className]['line']) {
                        $found = '';
                    } else {
                        $found = $tokens[$prev]['content'];
                    }

                    $expected = str_repeat(' ', $classIndent + $this->indent);
                    if ($found !== $expected) {
                        $error = 'Expected %s spaces before interface name; %s found';
                        $data = [
                            $classIndent + $this->indent,
                            strlen($found),
                        ];
                        $fix = $phpcsFile->addFixableError($error, $className, 'InterfaceWrongIndent', $data);
                        if ($fix) {
                            if ($found === '') {
                                $phpcsFile->fixer->addContent($prev, $expected);
                            } else {
                                $phpcsFile->fixer->replaceToken($prev, $expected);
                            }
                        }
                    }
                }
            } elseif ($tokens[$className - 1]['code'] !== T_NS_SEPARATOR
                || $tokens[$className - 2]['code'] !== T_STRING
            ) {
                if ($tokens[$className - 1]['code'] === T_NS_SEPARATOR) {
                    $prev = $className - 2;
                } else {
                    $prev = $className - 1;
                }

                $last = $phpcsFile->findPrevious(T_WHITESPACE, $prev, null, true);
                $content = $phpcsFile->getTokensAsString($last + 1, $prev - $last);
                if ($content !== ' ') {
                    $found = $tokens[$prev]['code'] === T_WHITESPACE
                        ? $tokens[$prev]['length']
                        : 0;

                    $error = 'Expected 1 space before "%s"; %s found';
                    $data = [
                        $tokens[$className]['content'],
                        $found,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $className, 'SpaceBeforeName', $data);
                    if ($fix) {
                        if ($tokens[$prev]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($prev, ' ');
                            while ($tokens[--$prev]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken($prev, '');
                            }
                            $phpcsFile->fixer->endChangeset();
                        } else {
                            $phpcsFile->fixer->addContent($prev, ' ');
                        }
                    }
                }
            }

            if ($checkingImplements === true
                && $n !== $classCount - 1
                && $tokens[$className + 1]['code'] !== T_NS_SEPARATOR
                && $tokens[$className + 1]['code'] !== T_COMMA
                && $tokens[$className + 2]['code'] !== T_IMPLEMENTS
            ) {
                $error = 'Expected 0 spaces between "%s" and comma; %s found';
                $data = [
                    $tokens[$className]['content'],
                    $tokens[$className + 1]['length'],
                ];

                $fix = $phpcsFile->addFixableError($error, $className, 'SpaceBeforeComma', $data);
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($className + 1, '');
                }
            }
        }

        $last = $phpcsFile->findPrevious(Tokens::$emptyTokens, $openingBrace - 1, null, true);
        if ($tokens[$stackPtr]['line'] === $tokens[$last]['line']) {
            // Open brace in the same line as anonymous class declaration
            if ($tokens[$last]['line'] < $tokens[$openingBrace]['line']) {
                $error = 'Opening brace of the anonymous class must be in the same line as declaration';
                $fix = $phpcsFile->addFixableError($error, $openingBrace, 'OpenBraceWrongLine');

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $i = $openingBrace;
                    while ($tokens[--$i]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->replaceToken($openingBrace, '');
                    $phpcsFile->fixer->addContent($last, ' {');
                    $phpcsFile->fixer->endChangeset();
                }
            } elseif ($tokens[$openingBrace - 1]['content'] !== ' ') {
                $error = 'There must be exactly one space before opening brace';
                $fix = $phpcsFile->addFixableError($error, $openingBrace, 'SpaceBeforeOpenBrace');

                if ($fix) {
                    if ($tokens[$openingBrace - 1]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($openingBrace - 1, ' ');
                    } else {
                        $phpcsFile->fixer->addContentBefore($openingBrace, ' ');
                    }
                }
            }
        } elseif ($tokens[$last]['line'] + 1 === $tokens[$openingBrace]['line']) {
            if ($tokens[$openingBrace]['column'] !== $classIndent + 1
                || ($classIndent > 0
                    && $tokens[$openingBrace - 1]['content'] !== str_repeat(' ', $classIndent))
            ) {
                $error = 'Invalid indent of the anonymous class opening brace; expected %d spaces';
                $data = [$classIndent];

                $fix = $phpcsFile->addFixableError($error, $openingBrace, 'OpenBraceIndent', $data);
                if ($fix) {
                    $firstInLine = $last;
                    while ($tokens[$firstInLine]['line'] < $tokens[$openingBrace]['line']) {
                        ++$firstInLine;
                    }

                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $firstInLine; $i <= $openingBrace; ++$i) {
                        if (in_array($tokens[$i]['code'], [T_WHITESPACE, T_OPEN_CURLY_BRACKET], true)) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                    }
                    $phpcsFile->fixer->addContentBefore($firstInLine, str_repeat(' ', $classIndent) . '{');
                    $phpcsFile->fixer->endChangeset();
                }
            }
        } else {
            $error = 'Opening brace of the anonymous class must be in the next line after multiline declaration';
            $fix = $phpcsFile->addFixableError($error, $openingBrace, 'OpenBraceWrongLine');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $i = $openingBrace;
                while ($tokens[--$i]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->replaceToken($openingBrace, '');
                $phpcsFile->fixer->addContent($last, $phpcsFile->eolChar . str_repeat(' ', $classIndent) . '{');
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
