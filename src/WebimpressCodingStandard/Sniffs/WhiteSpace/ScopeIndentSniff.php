<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function array_diff;
use function array_pop;
use function ceil;
use function end;
use function in_array;
use function ltrim;
use function max;
use function min;
use function preg_match;
use function str_repeat;
use function strlen;
use function strpos;
use function substr;

use const T_ANON_CLASS;
use const T_ARRAY;
use const T_BREAK;
use const T_CATCH;
use const T_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_CLOSE_TAG;
use const T_CLOSURE;
use const T_COLON;
use const T_COMMA;
use const T_COMMENT;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_CONTINUE;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOUBLE_ARROW;
use const T_DOUBLE_QUOTED_STRING;
use const T_ECHO;
use const T_ELSEIF;
use const T_EXIT;
use const T_FN;
use const T_FOR;
use const T_FOREACH;
use const T_FUNCTION;
use const T_GOTO_LABEL;
use const T_IF;
use const T_INLINE_ELSE;
use const T_INLINE_HTML;
use const T_INLINE_THEN;
use const T_INTERFACE;
use const T_OBJECT_OPERATOR;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_OPEN_TAG;
use const T_RETURN;
use const T_SELF;
use const T_SEMICOLON;
use const T_START_HEREDOC;
use const T_START_NOWDOC;
use const T_STATIC;
use const T_STRING_CONCAT;
use const T_SWITCH;
use const T_THROW;
use const T_TRAIT;
use const T_USE;
use const T_VARIABLE;
use const T_WHILE;
use const T_WHITESPACE;
use const T_YIELD;
use const T_YIELD_FROM;

class ScopeIndentSniff implements Sniff
{
    /**
     * @var int
     */
    public $indent = 4;

    /**
     * @var bool
     */
    public $alignObjectOperators = true;

    /**
     * Ignore alignment of array arrows which are at the beginning of new line.
     * If set to true, other sniff should check alignment of these arrows
     * - for example WebimpressCodingStandard.Arrays.DoubleArrow
     *
     * @var bool
     */
    public $ignoreNewLineArrayArrow = false;

    /**
     * @var int[]
     */
    private $controlStructures = [
        T_IF => T_IF,
        T_ELSEIF => T_ELSEIF,
        T_SWITCH => T_SWITCH,
        T_WHILE => T_WHILE,
        T_FOR => T_FOR,
        T_FOREACH => T_FOREACH,
        T_CATCH => T_CATCH,
    ];

    /**
     * @var int[]
     */
    private $caseEndToken = [
        T_BREAK,
        T_CONTINUE,
        T_RETURN,
        T_THROW,
        T_EXIT,
    ];

    /**
     * @var int[]
     */
    private $endToken = [
        T_COLON => T_COLON,
        T_SEMICOLON => T_SEMICOLON,
        T_COMMA => T_COMMA,
        T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS,
        T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
        T_CLOSE_SHORT_ARRAY => T_CLOSE_SHORT_ARRAY,
        T_CLOSE_SQUARE_BRACKET => T_CLOSE_SQUARE_BRACKET,
    ];

    /**
     * @var int[]
     */
    private $endTokenOperator;

    /**
     * @var int[]
     */
    private $breakToken;

    /**
     * @var int[]
     */
    private $functionToken;

    /**
     * @var int[]
     */
    private $extras = [];

    public function __construct()
    {
        $this->endTokenOperator = Tokens::$operators
            + Tokens::$booleanOperators
            + Tokens::$comparisonTokens
            + [
                T_INLINE_ELSE => T_INLINE_ELSE,
                T_INLINE_THEN => T_INLINE_THEN,
                T_STRING_CONCAT => T_STRING_CONCAT,
            ];

        $this->breakToken = $this->endTokenOperator
            + Tokens::$assignmentTokens
            + [
                T_COLON => T_COLON,
                T_SEMICOLON => T_SEMICOLON,
                T_COMMA => T_COMMA,
                T_GOTO_LABEL => T_GOTO_LABEL,
                T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS,
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_OPEN_SHORT_ARRAY => T_OPEN_SHORT_ARRAY,
                T_OPEN_SQUARE_BRACKET => T_OPEN_SQUARE_BRACKET,
                T_ARRAY => T_ARRAY,
                T_INLINE_ELSE => T_INLINE_ELSE,
                T_INLINE_THEN => T_INLINE_THEN,
                T_STRING_CONCAT => T_STRING_CONCAT,
                T_RETURN => T_RETURN,
                T_THROW => T_THROW,
                T_YIELD => T_YIELD,
                T_YIELD_FROM => T_YIELD_FROM,
                T_ECHO => T_ECHO,
                T_EXIT => T_EXIT,
            ];

        $this->functionToken = Tokens::$functionNameTokens
            + $this->controlStructures
            + [
                T_SELF => T_SELF,
                T_STATIC => T_STATIC,
                T_VARIABLE => T_VARIABLE,
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS,
                T_USE => T_USE,
                T_CLOSURE => T_CLOSURE,
                T_FN => T_FN,
                T_ARRAY => T_ARRAY,
                T_ANON_CLASS => T_ANON_CLASS,
            ];
    }

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_TAG];
    }

    /**
     * @param int $stackPtr
     * @return null|int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $this->extras = [];
        $extraIndent = 0;
        $phpIndents = [];
        $previousIndent = null;

        for ($i = $stackPtr + 1; $i < $phpcsFile->numTokens; ++$i) {
            if (in_array($tokens[$i]['code'], Tokens::$booleanOperators, true)) {
                $next = $phpcsFile->findNext(
                    Tokens::$emptyTokens + [T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS],
                    $i + 1,
                    null,
                    true
                );

                if ($tokens[$next]['line'] > $tokens[$i]['line']) {
                    $error = 'Boolean operator found at the end of the line';
                    $fix = $phpcsFile->addFixableError($error, $i, 'BooleanOperatorAtTheEnd');

                    if ($fix) {
                        $lastNonEmpty = $i;
                        while ($tokens[$lastNonEmpty + 1]['line'] === $tokens[$i]['line']) {
                            ++$lastNonEmpty;
                        }
                        while (in_array($tokens[$lastNonEmpty]['code'], Tokens::$emptyTokens, true)) {
                            --$lastNonEmpty;
                        }

                        $string = $phpcsFile->getTokensAsString($i, $lastNonEmpty - $i + 1);
                        if (substr($string, -1) !== '(') {
                            $string .= ' ';
                        }

                        while ($bracket = $phpcsFile->findPrevious(T_OPEN_PARENTHESIS, $next - 1, $lastNonEmpty + 1)) {
                            $next = $bracket;
                        }

                        $phpcsFile->fixer->beginChangeset();
                        $j = $i - 1;
                        while ($tokens[$j]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken($j, '');
                            --$j;
                        }
                        for ($j = $i; $j <= $lastNonEmpty; ++$j) {
                            $phpcsFile->fixer->replaceToken($j, '');
                        }
                        $phpcsFile->fixer->addContentBefore($next, $string);
                        $phpcsFile->fixer->endChangeset();
                    }

                    continue;
                }
            }

            if ($tokens[$i]['code'] === T_INLINE_HTML) {
                $depth = $tokens[$i]['level'];
                $expectedIndent = $depth * $this->indent + $extraIndent;

                if ($tokens[$i]['column'] === 1) {
                    $spaces = str_repeat(' ', $expectedIndent);
                    if ($spaces && strpos($tokens[$i]['content'], $spaces) !== 0) {
                        $error = 'Expected at least %s spaces';
                        $data = [$expectedIndent];
                        $fix = $phpcsFile->addFixableError($error, $i, 'HtmlIndent', $data);

                        if ($fix) {
                            $phpcsFile->fixer->replaceToken($i, $spaces . ltrim($tokens[$i]['content']));
                        }
                    }
                }

                continue;
            }

            if (($tokens[$i]['code'] === T_CONSTANT_ENCAPSED_STRING
                    || $tokens[$i]['code'] === T_DOUBLE_QUOTED_STRING)
                && $tokens[$i - 1]['code'] === $tokens[$i]['code']
            ) {
                continue;
            }

            // Skip class/interface/trait declarations, handled by ClassDeclarationSniff
            if (in_array($tokens[$i]['code'], [T_CLASS, T_INTERFACE, T_TRAIT], true)) {
                $i = $tokens[$i]['scope_opener'];
                continue;
            }

            if ($tokens[$i]['code'] === T_OPEN_TAG) {
                $depth = $tokens[$i]['level'];

                $extraIndent = max($tokens[$i]['column'] - 1 - ($depth * $this->indent), 0);
                $extraIndent = (int) (ceil($extraIndent / $this->indent) * $this->indent);

                $phpIndents[] = $extraIndent;

                $expectedIndent = $depth * $this->indent;
                if ($tokens[$i]['column'] === 1 && $expectedIndent > 0) {
                    if (isset($tokens[$i + 1])
                        && $tokens[$i + 1]['level'] < $depth
                        && $tokens[$i + 1]['line'] === $tokens[$i]['line']
                    ) {
                        $expectedIndent -= $this->indent;
                    }

                    if ($expectedIndent > 0) {
                        $error = 'Expected %d spaces before PHP open tag';
                        $data = [$expectedIndent];
                        $fix = $phpcsFile->addFixableError($error, $i, 'OpenTagIndent', $data);

                        if ($fix) {
                            $phpcsFile->fixer->addContentBefore($i, str_repeat(' ', $expectedIndent));
                        }
                    }
                }

                continue;
            }

            if ($tokens[$i]['code'] === T_CLOSE_TAG) {
                array_pop($phpIndents);
                $extraIndent = end($phpIndents) ?? 0;
            }

            // skip doc block comment
            if ($tokens[$i]['code'] === T_DOC_COMMENT_OPEN_TAG) {
                $i = $tokens[$i]['comment_closer'];
                continue;
            }

            // skip heredoc/nowdoc
            if ($tokens[$i]['code'] === T_START_HEREDOC
                || $tokens[$i]['code'] === T_START_NOWDOC
            ) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            if (isset($this->extras[$i])) {
                $extraIndent -= $this->extras[$i];
                unset($this->extras[$i]);
            }

            // Skip anonymous class declaration, handled by AnonymousClassDeclarationSniff
            // Anonymous class without parenthesis
            if ($tokens[$i]['code'] === T_ANON_CLASS) {
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i + 1, null, true);
                if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
                    $i = $tokens[$i]['scope_opener'];
                    continue;
                }
            }

            // Closing parenthesis belongs to anonymous class, skip to scope opener
            if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                $before = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens,
                    $tokens[$i]['parenthesis_opener'] - 1,
                    null,
                    true
                );

                if ($tokens[$before]['code'] === T_ANON_CLASS) {
                    $i = $tokens[$before]['scope_opener'];
                    continue;
                }
            }

            // check if closing parenthesis is in the same line as control structure
            if ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET
                && isset($tokens[$i]['scope_condition'])
                && ($scopeCondition = $tokens[$tokens[$i]['scope_condition']])
                && ! in_array($scopeCondition['code'], [T_FUNCTION, T_CLOSURE], true)
                && ($parenthesis = $phpcsFile->findPrevious(Tokens::$emptyTokens, $i - 1, null, true))
                && $tokens[$parenthesis]['code'] === T_CLOSE_PARENTHESIS
                && $tokens[$parenthesis]['line'] > $scopeCondition['line']
            ) {
                $prev = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens + [T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS],
                    $parenthesis - 1,
                    null,
                    true
                );
                if ($scopeCondition['line'] === $tokens[$prev]['line']) {
                    $error = 'Closing parenthesis must be in the same line as control structure';
                    $fix = $phpcsFile->addFixableError($error, $parenthesis, 'UnnecessaryLineBreak');

                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($j = $prev + 1; $j < $parenthesis; ++$j) {
                            if ($tokens[$j]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken($j, '');
                            }
                        }
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }

            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS
                && $tokens[$i]['line'] < $tokens[$tokens[$i]['parenthesis_closer']]['line']
            ) {
                $next = $phpcsFile->findNext(
                    Tokens::$emptyTokens
                    + [
                        T_OPEN_SHORT_ARRAY => T_OPEN_SHORT_ARRAY,
                        T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                        T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS,
                    ],
                    $i + 1,
                    null,
                    true
                );

                $prev = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens
                    + [
                        T_CLOSE_SHORT_ARRAY => T_CLOSE_SHORT_ARRAY,
                        T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                        T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS,
                    ],
                    $tokens[$i]['parenthesis_closer'] - 1,
                    null,
                    true
                );

                $owner = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens,
                    $tokens[$i]['parenthesis_opener'] - 1,
                    null,
                    true
                );

                $ownerTokens = array_diff($this->functionToken, $this->controlStructures);

                if ($tokens[$next]['line'] === $tokens[$i]['line']
                    && in_array($tokens[$owner]['code'], $ownerTokens, true)
                    && $this->hasContainNewLine(
                        $phpcsFile,
                        $tokens[$i]['parenthesis_opener'],
                        $prev
                    )
                ) {
                    $error = 'Content must be in new line after opening parenthesis';
                    $fix = $phpcsFile->addFixableError($error, $i, 'OpeningParenthesis');

                    if ($fix) {
                        $phpcsFile->fixer->addNewline($i);
                    }
                }
            }

            // closing parenthesis in next line when multi-line control structure
            if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS
                && $tokens[$i]['line'] > $tokens[$tokens[$i]['parenthesis_opener']]['line']
            ) {
                $prev = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens
                    + [
                        T_CLOSE_SHORT_ARRAY => T_CLOSE_SHORT_ARRAY,
                        T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                        T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS,
                    ],
                    $i - 1,
                    null,
                    true
                );

                $owner = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens,
                    $tokens[$i]['parenthesis_opener'] - 1,
                    null,
                    true
                );

                if ($tokens[$prev]['line'] === $tokens[$i]['line']
                    && in_array($tokens[$owner]['code'], $this->functionToken, true)
                    && $this->hasContainNewLine(
                        $phpcsFile,
                        $tokens[$i]['parenthesis_opener'],
                        $tokens[$i]['parenthesis_closer']
                    )
                ) {
                    $error = 'Closing parenthesis must be in the next line';
                    $fix = $phpcsFile->addFixableError($error, $i, 'ClosingParenthesis');

                    if ($fix) {
                        $phpcsFile->fixer->addNewlineBefore($i);
                    }
                }

                if (isset($tokens[$owner]['scope_condition'])) {
                    $scopeCondition = $tokens[$owner];
                    $prev = $i;
                    while (($prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $prev - 1, null, true))
                        && $tokens[$prev]['code'] === T_CLOSE_PARENTHESIS
                        && $tokens[$prev]['line'] > $scopeCondition['line']
                        && $tokens[$tokens[$prev]['parenthesis_opener']]['line'] === $scopeCondition['line']
                        && ! $phpcsFile->findFirstOnLine(
                            Tokens::$emptyTokens + [T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS],
                            $prev,
                            true
                        )
                    ) {
                        if ($tokens[$prev]['line'] <= $tokens[$i]['line'] - 1) {
                            $error = 'Invalid closing parenthesis position';
                            $fix = $phpcsFile->addFixableError($error, $prev, 'InvalidClosingParenthesisPosition');

                            if ($fix) {
                                $phpcsFile->fixer->beginChangeset();
                                for ($j = $prev + 1; $j < $i; ++$j) {
                                    if ($tokens[$j]['code'] === T_WHITESPACE) {
                                        $phpcsFile->fixer->replaceToken($j, '');
                                    }
                                }
                                $phpcsFile->fixer->endChangeset();
                            }
                        } elseif ($tokens[$prev + 1]['code'] === T_WHITESPACE) {
                            $error = 'Unexpected whitespace before closing parenthesis';
                            $fix = $phpcsFile->addFixableError(
                                $error,
                                $prev + 1,
                                'UnexpectedSpacesBeforeClosingParenthesis'
                            );

                            if ($fix) {
                                $phpcsFile->fixer->replaceToken($prev + 1, '');
                            }
                        }
                    }
                }
            }

            if ($tokens[$i]['code'] === T_OBJECT_OPERATOR) {
                $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $i - 1, null, true);

                if ($tokens[$prev]['line'] === $tokens[$i]['line']
                    && ($prevObjectOperator = $this->hasPrevObjectOperator($phpcsFile, $i))
                    && $tokens[$prevObjectOperator]['line'] < $tokens[$i]['line']
                ) {
                    // add line break before
                    $error = 'Object operator must be in new line';
                    $fix = $phpcsFile->addFixableError($error, $i, 'ObjectOperator');

                    if ($fix) {
                        $phpcsFile->fixer->addNewlineBefore($i);
                    }
                }

                $prev = $phpcsFile->findPrevious(T_WHITESPACE, $i - 1, null, true);
                if ($tokens[$prev]['line'] === $tokens[$i]['line']
                    && ! ($fp = $this->findPrevious($phpcsFile, $i, [T_OBJECT_OPERATOR]))
                ) {
                    $endOfStatement = min($this->findEndOfStatement($phpcsFile, $i), $this->findNext($phpcsFile, $i));
                    $newLine = $this->hasContainNewLine($phpcsFile, $i, $endOfStatement);

                    if ($newLine) {
                        $ei = $previousIndent + $this->indent - $tokens[$i]['level'] * $this->indent - $extraIndent;

                        $fn = $this->findNext($phpcsFile, $i, $this->endTokenOperator);
                        if (! in_array($tokens[$fn]['code'], $this->endToken, true)) {
                            $ei = $this->indent;
                            $fn = $phpcsFile->findPrevious(Tokens::$emptyTokens, $fn - 1, null, true);
                        }

                        if ($this->alignObjectOperators) {
                            $useToken = $i;
                            while (($fno = $this->findNext($phpcsFile, $useToken, [T_OBJECT_OPERATOR]))
                                && $tokens[$fno]['code'] === T_OBJECT_OPERATOR
                                && $tokens[$fno]['line'] === $tokens[$i]['line']
                            ) {
                                $useToken = $fno;
                            }

                            $expectedIndent = $tokens[$useToken]['level'] * $this->indent;
                            $ei = $tokens[$useToken]['column'] - 1 - $expectedIndent - $extraIndent;
                        }

                        if ($ei) {
                            $this->extras($fn, $ei);
                            $extraIndent += $ei;
                        }
                    }
                }
            }

            if (in_array(
                $tokens[$i]['code'],
                Tokens::$assignmentTokens
                + Tokens::$includeTokens
                + [
                    T_THROW => T_THROW,
                    T_RETURN => T_RETURN,
                    T_ECHO => T_ECHO,
                    T_YIELD => T_YIELD,
                    T_YIELD_FROM => T_YIELD_FROM,
                ],
                true
            )) {
                $endOfStatement = $this->findEndOfStatement($phpcsFile, $i);
                $newLine = $this->hasContainNewLine($phpcsFile, $i, $endOfStatement);

                if ($newLine) {
                    $this->extras($endOfStatement, $this->indent);
                    $extraIndent += $this->indent;
                }
            }

            if ($tokens[$i]['column'] === 1
                && ($next = $phpcsFile->findNext(T_WHITESPACE, $i, null, true))
                && $tokens[$next]['line'] === $tokens[$i]['line']
            ) {
                $depth = $tokens[$next]['level'];

                $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $i - 1, null, true);

                $expectedIndent = $depth * $this->indent;
                if (in_array($tokens[$next]['code'], $this->caseEndToken, true)
                    && isset($tokens[$next]['scope_closer'])
                    && $tokens[$next]['scope_closer'] === $next
                ) {
                    $endOfStatement = $this->findEndOfStatement($phpcsFile, $next);
                    $this->extras($endOfStatement, $this->indent);

                    $extraIndent += $this->indent;
                } elseif ($tokens[$next]['code'] === T_CLOSE_PARENTHESIS) {
                    if (isset($this->extras[$next])) {
                        $extraIndent -= $this->extras[$next];
                        unset($this->extras[$next]);
                    }

                    $opener = $tokens[$next]['parenthesis_opener'];
                    $owner = $phpcsFile->findPrevious(Tokens::$emptyTokens, $opener - 1, null, true);

                    // if it is not a function call
                    // and not a control structure
                    if (! in_array($tokens[$owner]['code'], $this->functionToken, true)) {
                        $error = 'Closing parenthesis must be in the previous line';
                        $fix = $phpcsFile->addFixableError($error, $next, 'ClosingParenthesis');

                        if ($fix) {
                            $semicolon = $phpcsFile->findNext(Tokens::$emptyTokens, $next + 1, null, true);

                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($next, '');
                            $phpcsFile->fixer->addContent($prev, $tokens[$next]['content']);
                            if ($tokens[$semicolon]['code'] === T_SEMICOLON) {
                                $phpcsFile->fixer->addContent($prev, ';');
                                $phpcsFile->fixer->replaceToken($semicolon, '');
                                $j = $semicolon + 1;
                            } else {
                                $j = $next + 1;
                            }
                            while ($tokens[$j]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken($j, '');
                                ++$j;

                                if ($tokens[$j]['line'] > $tokens[$next]['line']) {
                                    break;
                                }
                            }
                            $phpcsFile->fixer->endChangeset();
                        }

                        continue;
                    }
                } elseif ($tokens[$next]['code'] === T_CLOSE_SHORT_ARRAY) {
                    if (isset($this->extras[$next])) {
                        $extraIndent -= $this->extras[$next];
                        unset($this->extras[$next]);
                    }
                } elseif ($tokens[$next]['code'] === T_OBJECT_OPERATOR) {
                    $fp = $this->findPrevious(
                        $phpcsFile,
                        $next,
                        $this->breakToken + [T_OBJECT_OPERATOR => T_OBJECT_OPERATOR]
                    );
                    if ($tokens[$fp]['code'] !== T_OBJECT_OPERATOR) {
                        if (in_array($tokens[$fp]['code'], [
                            T_ARRAY,
                            T_OPEN_PARENTHESIS,
                            T_OPEN_SQUARE_BRACKET,
                            T_OPEN_SHORT_ARRAY,
                            T_OPEN_CURLY_BRACKET,
                        ], true)) {
                            $ei = $this->indent;
                        } else {
                            $nx = $phpcsFile->findNext(Tokens::$emptyTokens, $fp + 1, null, true);
                            while ($tokens[$nx - 1]['line'] === $tokens[$nx]['line']) {
                                --$nx;
                            }

                            $indent = $tokens[$nx]['code'] === T_WHITESPACE
                                ? $tokens[$nx]['length']
                                : 0;

                            $ei = $indent - $expectedIndent - $extraIndent + $this->indent;
                        }

                        if ($ei > 0) {
                            $fn = $this->findNext($phpcsFile, $i);
                            $this->extras($fn, $ei);
                            $extraIndent += $ei;
                        }
                    }
                } elseif ($tokens[$next]['code'] === T_INLINE_THEN) {
                    $expectedIndent = $previousIndent - $extraIndent + $this->indent;
                } elseif ($tokens[$next]['code'] === T_INLINE_ELSE) {
                    $count = 0;
                    $t = $i;
                    while ($t = $phpcsFile->findPrevious([T_INLINE_THEN, T_INLINE_ELSE], $t - 1)) {
                        if ($tokens[$t]['code'] === T_INLINE_ELSE) {
                            ++$count;
                        } else {
                            --$count;

                            if ($count < 0) {
                                break;
                            }
                        }
                    }

                    $first = $phpcsFile->findFirstOnLine([], $t, true);
                    if ($tokens[$first]['code'] !== T_WHITESPACE) {
                        $expectedIndent = $this->indent;
                    } else {
                        $expectedIndent = strlen($tokens[$first]['content']) - $extraIndent;

                        $firstNonEmpty = $phpcsFile->findFirstOnLine(Tokens::$emptyTokens, $t, true);
                        if ($t !== $firstNonEmpty) {
                            $expectedIndent += $this->indent;
                        }
                    }
                } elseif ($this->ignoreNewLineArrayArrow && $tokens[$next]['code'] === T_DOUBLE_ARROW) {
                    continue;
                }

                $expectedIndent += $extraIndent;
                $previousIndent = $expectedIndent;

                if ($tokens[$i]['code'] === T_WHITESPACE
                    && strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false
                    && strlen($tokens[$i]['content']) !== $expectedIndent
                ) {
                    $error = 'Invalid indent. Expected %d spaces, found %d';
                    $data = [
                        $expectedIndent,
                        strlen($tokens[$i]['content']),
                    ];
                    $fix = $phpcsFile->addFixableError($error, $i, 'InvalidIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($i, str_repeat(' ', max($expectedIndent, 0)));
                    }
                } elseif ($tokens[$i]['code'] === T_COMMENT
                    && preg_match('/^(\s*)\*/', $tokens[$i]['content'], $match)
                ) {
                    if (strlen($match[1]) !== $expectedIndent + 1) {
                        $error = 'Invalid comment indent. Expected %d spaces, found %d';
                        $data = [
                            $expectedIndent + 1,
                            strlen($match[1]),
                        ];
                        $fix = $phpcsFile->addFixableError($error, $i, 'CommentIndent', $data);

                        if ($fix) {
                            $phpcsFile->fixer->replaceToken(
                                $i,
                                str_repeat(' ', max($expectedIndent, 0) + 1) . ltrim($tokens[$i]['content'])
                            );
                        }
                    }
                } elseif ($tokens[$i]['code'] !== T_WHITESPACE
                    && $expectedIndent
                    && ($tokens[$i]['code'] !== T_COMMENT
                        || preg_match('/^\s*(\/\/|#|\/\*)/', $tokens[$i]['content']))
                ) {
                    $error = 'Missing indent. Expected %d spaces';
                    $data = [$expectedIndent];
                    $fix = $phpcsFile->addFixableError($error, $i, 'MissingIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->addContentBefore($i, str_repeat(' ', max($expectedIndent, 0)));
                    }
                }
            }

            // count extra indent
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS
                || $tokens[$i]['code'] === T_OPEN_SHORT_ARRAY
                || ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET
                    && isset($tokens[$i]['scope_closer']))
            ) {
                switch ($tokens[$i]['code']) {
                    case T_OPEN_PARENTHESIS:
                        $key = 'parenthesis_closer';
                        break;
                    case T_OPEN_SHORT_ARRAY:
                        $key = 'bracket_closer';
                        break;
                    default:
                        $key = 'scope_closer';
                        break;
                }
                $xEnd = $tokens[$i][$key];

                // no extra indent if there is no new line between open and close brackets
                if (! $this->hasContainNewLine($phpcsFile, $i, $xEnd)) {
                    continue;
                }

                // If open parenthesis belongs to control structure
                if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS
                    && isset($tokens[$i]['parenthesis_owner'])
                    && in_array($tokens[$tokens[$i]['parenthesis_owner']]['code'], $this->controlStructures, true)
                ) {
                    // search for first non-empty token in line,
                    // where is the closing parenthesis of the control structure
                    $firstOnLine = $phpcsFile->findFirstOnLine(Tokens::$emptyTokens, $xEnd, true);

                    $extraIndent += $this->indent;
                    $this->extras($firstOnLine, $this->indent);

                    $controlStructure[$tokens[$i]['line']] = $tokens[$i]['parenthesis_closer'];

                    continue;
                }

                $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $i, true);

                $firstInNextLine = $i;
                while ($tokens[$firstInNextLine]['line'] === $tokens[$i]['line']
                    || $tokens[$firstInNextLine]['code'] === T_WHITESPACE
                ) {
                    ++$firstInNextLine;
                }

                $ei1 = 0;
                if ($tokens[$first]['level'] === $tokens[$firstInNextLine]['level']
                    && $tokens[$firstInNextLine]['code'] !== T_CLOSE_CURLY_BRACKET
                ) {
                    $ei1 = $this->indent;
                    $this->extras($xEnd, $ei1);
                }

                $ei2 = 0;
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i + 1, null, true);
                if ($tokens[$next]['line'] > $tokens[$i]['line']) {
                    // current line indent
                    $whitespace = $phpcsFile->findFirstOnLine([], $i, true);
                    if ($tokens[$whitespace]['code'] === T_WHITESPACE) {
                        $sum = strlen($tokens[$whitespace]['content'])
                            - $tokens[$first]['level'] * $this->indent
                            - $extraIndent;

                        if ($sum > 0) {
                            $ei2 = $sum;
                            $this->extras($xEnd + 1, $ei2);
                        }
                    }
                }

                $extraIndent += $ei1 + $ei2;
            }
        }

        return $phpcsFile->numTokens + 1;
    }

    private function extras(int $index, int $value) : void
    {
        if (isset($this->extras[$index])) {
            $this->extras[$index] += $value;
        } else {
            $this->extras[$index] = $value;
        }
    }

    private function findPrevious(File $phpcsFile, int $ptr, array $search) : ?int
    {
        $tokens = $phpcsFile->getTokens();

        while (--$ptr) {
            if ($tokens[$ptr]['code'] === T_CLOSE_PARENTHESIS) {
                $ptr = $tokens[$ptr]['parenthesis_opener'];
            } elseif ($tokens[$ptr]['code'] === T_CLOSE_CURLY_BRACKET
                || $tokens[$ptr]['code'] === T_CLOSE_SHORT_ARRAY
                || $tokens[$ptr]['code'] === T_CLOSE_SQUARE_BRACKET
            ) {
                $ptr = $tokens[$ptr]['bracket_opener'];
            } elseif (in_array($tokens[$ptr]['code'], $search, true)) {
                return $ptr;
            } elseif (in_array($tokens[$ptr]['code'], $this->breakToken, true)) {
                break;
            }
        }

        return null;
    }

    /**
     * Find next token in object chain calls.
     */
    private function findNext(File $phpcsFile, int $ptr, array $search = []) : ?int
    {
        $tokens = $phpcsFile->getTokens();

        while (++$ptr) {
            if ($tokens[$ptr]['code'] === T_OPEN_PARENTHESIS) {
                $ptr = $tokens[$ptr]['parenthesis_closer'];
            } elseif ($tokens[$ptr]['code'] === T_OPEN_CURLY_BRACKET
                || $tokens[$ptr]['code'] === T_OPEN_SQUARE_BRACKET
                || $tokens[$ptr]['code'] === T_OPEN_SHORT_ARRAY
            ) {
                $ptr = $tokens[$ptr]['bracket_closer'];
            } elseif (in_array($tokens[$ptr]['code'], $search, true)) {
                return $ptr;
            } elseif (in_array($tokens[$ptr]['code'], $this->endTokenOperator, true)) {
                return $phpcsFile->findPrevious(Tokens::$emptyTokens, $ptr - 1, null, true);
            } elseif (in_array($tokens[$ptr]['code'], $this->endToken, true)) {
                return $ptr;
            }
        }

        return null;
    }

    /**
     * Overrides File::findEndOfStatement as temporary solution until
     * https://github.com/squizlabs/PHP_CodeSniffer/issues/2748
     * is fixed.
     */
    private function findEndOfStatement(File $phpcsFile, int $ptr) : int
    {
        $closingBracket = [
            T_CLOSE_PARENTHESIS,
            T_CLOSE_SQUARE_BRACKET,
            T_CLOSE_CURLY_BRACKET,
            T_CLOSE_SHORT_ARRAY,
        ];

        $tokens = $phpcsFile->getTokens();
        $lastToken = $phpcsFile->numTokens;

        if ($tokens[$ptr]['code'] === T_DOUBLE_ARROW && $ptr < $lastToken) {
            ++$ptr;
        }

        while ($ptr < $lastToken) {
            if ($tokens[$ptr]['code'] === T_OPEN_PARENTHESIS) {
                $ptr = $tokens[$ptr]['parenthesis_closer'] + 1;
                continue;
            }

            if ($tokens[$ptr]['code'] === T_OPEN_CURLY_BRACKET
                || $tokens[$ptr]['code'] === T_OPEN_SQUARE_BRACKET
                || $tokens[$ptr]['code'] === T_OPEN_SHORT_ARRAY
            ) {
                $ptr = $tokens[$ptr]['bracket_closer'] + 1;
                continue;
            }

            if (isset($tokens[$ptr]['scope_closer']) && $ptr < $tokens[$ptr]['scope_closer']) {
                $ptr = $tokens[$ptr]['scope_closer'];
                if (in_array($tokens[$ptr]['code'], $closingBracket, true)) {
                    ++$ptr;
                }
            } elseif (isset($tokens[$ptr]['parenthesis_closer']) && $ptr < $tokens[$ptr]['parenthesis_closer']) {
                $ptr = $tokens[$ptr]['parenthesis_closer'];
                if (in_array($tokens[$ptr]['code'], $closingBracket, true)) {
                    ++$ptr;
                }
            }

            if ($tokens[$ptr]['code'] === T_COMMA
                || $tokens[$ptr]['code'] === T_SEMICOLON
                || $tokens[$ptr]['code'] === T_DOUBLE_ARROW
            ) {
                return $ptr;
            }

            if (in_array($tokens[$ptr]['code'], $closingBracket, true)) {
                return $phpcsFile->findPrevious(Tokens::$emptyTokens, $ptr - 1, null, true);
            }

            ++$ptr;
        }

        return $lastToken;
    }

    /**
     * Checks if there is another object operator
     * before $ptr token.
     */
    private function hasPrevObjectOperator(File $phpcsFile, int $ptr) : ?int
    {
        $tokens = $phpcsFile->getTokens();

        while (--$ptr) {
            if ($tokens[$ptr]['code'] === T_CLOSE_PARENTHESIS) {
                $ptr = $tokens[$ptr]['parenthesis_opener'];
            } elseif ($tokens[$ptr]['code'] === T_CLOSE_CURLY_BRACKET) {
                $ptr = $tokens[$ptr]['bracket_opener'];
            } elseif ($tokens[$ptr]['code'] === T_OBJECT_OPERATOR) {
                return $ptr;
            } elseif (in_array($tokens[$ptr]['code'], $this->breakToken, true)) {
                break;
            }
        }

        return null;
    }

    /**
     * Checks if between $fromPtr and $toPtr is any new line
     * excluding scopes (arrays, closures, multiline function calls).
     */
    private function hasContainNewLine(File $phpcsFile, int $fromPtr, int $toPtr) : bool
    {
        $tokens = $phpcsFile->getTokens();

        for ($j = $fromPtr + 1; $j < $toPtr; ++$j) {
            switch ($tokens[$j]['code']) {
                case T_OPEN_PARENTHESIS:
                    $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $j - 1, null, true);
                    if (! in_array($tokens[$prev]['code'], $this->functionToken, true)) {
                        continue 2;
                    }
                    // no break
                case T_ARRAY:
                    $j = $tokens[$j]['parenthesis_closer'];
                    continue 2;
                case T_ANON_CLASS:
                case T_OPEN_CURLY_BRACKET:
                    if (isset($tokens[$j]['scope_closer'])) {
                        $j = $tokens[$j]['scope_closer'];
                    }
                    continue 2;
                case T_OPEN_SHORT_ARRAY:
                case T_OPEN_SQUARE_BRACKET:
                    $j = $tokens[$j]['bracket_closer'];
                    continue 2;
                case T_WHITESPACE:
                    if (strpos($tokens[$j]['content'], $phpcsFile->eolChar) !== false) {
                        return true;
                    }
            }
        }

        return false;
    }
}
