<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use WebimpressCodingStandard\CodingStandard;

use function array_filter;
use function implode;
use function in_array;
use function max;
use function preg_match;
use function preg_replace;
use function preg_split;
use function round;
use function str_repeat;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function trim;

use const T_COLON;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_NAMESPACE;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_TAG;
use const T_USE;
use const T_WHITESPACE;

class DocCommentSniff implements Sniff
{
    /**
     * @var int
     */
    public $indent = 4;

    /**
     * @var string[]
     */
    public $tagWithType = CodingStandard::TAG_WITH_TYPE;

    /**
     * @var string[]
     */
    public $tagsWithContent = [
        '@see',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $commentStart = $stackPtr;
        $commentEnd = $tokens[$stackPtr]['comment_closer'];

        if ($this->checkIfEmpty($phpcsFile, $commentStart, $commentEnd)) {
            return;
        }

        $this->checkTags($phpcsFile, $commentStart, $commentEnd);
        $this->checkBeforeOpen($phpcsFile, $commentStart);
        $this->checkAfterClose($phpcsFile, $commentStart, $commentEnd);
        $this->checkCommentIndents($phpcsFile, $commentStart, $commentEnd);
        $this->checkTagsSpaces($phpcsFile, $commentStart, $commentEnd);
        $this->checkInheritDoc($phpcsFile, $commentStart, $commentEnd);

        // Doc block comment in one line.
        if ($tokens[$commentStart]['line'] === $tokens[$commentEnd]['line']) {
            $this->checkSpacesInOneLineComment($phpcsFile, $commentStart, $commentEnd);

            return;
        }

        $this->checkAfterOpen($phpcsFile, $commentStart);
        $this->checkBeforeClose($phpcsFile, $commentEnd);

        $this->checkSpacesAfterStar($phpcsFile, $commentStart, $commentEnd);
        $this->checkBlankLinesInComment($phpcsFile, $commentStart, $commentEnd);
        $this->checkStarInEveryLine($phpcsFile, $commentStart, $commentEnd);

        $this->checkBlankLineBeforeTags($phpcsFile, $commentStart);
    }

    /**
     * Checks if doc comment is empty.
     */
    private function checkIfEmpty(File $phpcsFile, int $commentStart, int $commentEnd) : bool
    {
        $tokens = $phpcsFile->getTokens();

        $empty = [
            T_DOC_COMMENT_WHITESPACE,
            T_DOC_COMMENT_STAR,
        ];

        $next = $commentStart;
        while ($next = $phpcsFile->findNext($empty, $next + 1, $commentEnd, true)) {
            if ($tokens[$next]['code'] === T_DOC_COMMENT_STRING
                && preg_match('/^[*\s]+$/', $tokens[$next]['content'])
            ) {
                continue;
            }

            return false;
        }

        $error = 'Doc comment is empty';
        $fix = $phpcsFile->addFixableError($error, $commentStart, 'Empty');

        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = $commentStart; $i <= $commentEnd; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
            if ($tokens[$commentStart - 1]['code'] === T_WHITESPACE
                && strpos($tokens[$commentStart - 1]['content'], $phpcsFile->eolChar) === false
            ) {
                $phpcsFile->fixer->replaceToken($commentStart - 1, '');
                if ($tokens[$commentStart - 2]['code'] === T_WHITESPACE
                    && strpos($tokens[$commentStart - 2]['content'], $phpcsFile->eolChar) !== false
                    && $tokens[$commentEnd + 1]['code'] === T_WHITESPACE
                    && strpos($tokens[$commentEnd + 1]['content'], $phpcsFile->eolChar) !== false
                ) {
                    $phpcsFile->fixer->replaceToken($commentStart - 2, '');
                }
            } elseif ($tokens[$commentStart - 1]['code'] === T_WHITESPACE
                && strpos($tokens[$commentStart - 1]['content'], $phpcsFile->eolChar) !== false
                && $tokens[$commentEnd + 1]['code'] === T_WHITESPACE
                && strpos($tokens[$commentEnd + 1]['content'], $phpcsFile->eolChar) !== false
            ) {
                $phpcsFile->fixer->replaceToken($commentStart - 1, '');
            } elseif ($tokens[$commentStart - 1]['code'] === T_OPEN_TAG
                && ($next = $phpcsFile->findNext(T_WHITESPACE, $commentEnd + 1, null, true))
                && $tokens[$next]['line'] > $tokens[$commentEnd]['line'] + 1
            ) {
                $phpcsFile->fixer->replaceToken($commentEnd + 1, '');
            }
            $phpcsFile->fixer->endChangeset();
        }

        return true;
    }

    /**
     * Check if there is no additional * with comment open and close tags
     */
    private function checkTags(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$commentStart]['content'] !== '/**') {
            $error = 'Invalid PHPDoc open tag; expected /** but found %s';
            $data = [$tokens[$commentStart]['content']];

            $fix = $phpcsFile->addFixableError($error, $commentStart, 'InvalidOpen', $data);
            if ($fix) {
                $phpcsFile->fixer->replaceToken($commentStart, '/**');
            }
        }

        if ($tokens[$commentEnd]['content'] !== '*/') {
            $error = 'Invalid PHPDoc close tag; expected */ but found %s';
            $data = [$tokens[$commentEnd]['content']];

            $fix = $phpcsFile->addFixableError($error, $commentEnd, 'InvalidClose', $data);
            if ($fix) {
                $phpcsFile->fixer->replaceToken($commentEnd, '*/');
            }
        }
    }

    /**
     * Checks if there is no any other content before doc comment opening tag,
     * and if there is blank line before doc comment (for multiline doc comment).
     */
    private function checkBeforeOpen(File $phpcsFile, int $commentStart) : void
    {
        $tokens = $phpcsFile->getTokens();

        $previous = $phpcsFile->findPrevious(T_WHITESPACE, $commentStart - 1, null, true);
        if ($tokens[$previous]['line'] === $tokens[$commentStart]['line']) {
            $error = 'The open comment tag must be the only content on the line';
            $fix = $phpcsFile->addFixableError($error, $commentStart, 'ContentBeforeOpeningTag');

            if ($fix) {
                $nonEmpty = $phpcsFile->findPrevious(T_WHITESPACE, $commentStart - 1, null, true);
                $phpcsFile->fixer->beginChangeset();
                $prev = $commentStart;
                while ($prev = $phpcsFile->findPrevious(T_WHITESPACE, $prev - 1, $nonEmpty)) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
                $phpcsFile->fixer->replaceToken($nonEmpty, trim($tokens[$nonEmpty]['content']));
                $phpcsFile->fixer->addNewline($commentStart - 1);
                $phpcsFile->fixer->endChangeset();
            }
        } elseif ($tokens[$previous]['line'] === $tokens[$commentStart]['line'] - 1
            && ! in_array($tokens[$previous]['code'], [
                T_OPEN_CURLY_BRACKET,
                T_OPEN_PARENTHESIS,
                T_OPEN_SHORT_ARRAY,
                T_OPEN_TAG,
                T_COLON,
            ], true)
        ) {
            $error = 'Missing blank line before doc comment';
            $fix = $phpcsFile->addFixableError($error, $commentStart, 'MissingBlankLine');

            if ($fix) {
                $phpcsFile->fixer->addNewlineBefore($commentStart);
            }
        }
    }

    /**
     * Checks if there is no any other content after doc comment opening tag (for multiline doc comment).
     */
    private function checkAfterOpen(File $phpcsFile, int $commentStart) : void
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_DOC_COMMENT_WHITESPACE, $commentStart + 1, null, true);
        if ($tokens[$next]['line'] === $tokens[$commentStart]['line']) {
            $error = 'The open comment tag must be the only content on the line';
            $fix = $phpcsFile->addFixableError($error, $commentStart, 'ContentAfterOpeningTag');

            if ($fix) {
                $indentToken = $tokens[$commentStart - 1];
                if ($indentToken['code'] === T_WHITESPACE
                    && $indentToken['line'] === $tokens[$commentStart]['line']
                ) {
                    $indent = strlen($indentToken['content']);
                } else {
                    $indent = 0;
                }

                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewline($commentStart);
                if ($tokens[$commentStart + 1]['code'] === T_DOC_COMMENT_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($commentStart + 1, str_repeat(' ', $indent));
                    if ($tokens[$commentStart + 2]['code'] !== T_DOC_COMMENT_STAR) {
                        $phpcsFile->fixer->addContent($commentStart + 1, '* ');
                    }
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Checks if there is no any other content before doc comment closing tag (for multiline doc comment).
     */
    private function checkBeforeClose(File $phpcsFile, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        $previous = $phpcsFile->findPrevious(T_DOC_COMMENT_WHITESPACE, $commentEnd - 1, null, true);
        if ($tokens[$previous]['line'] === $tokens[$commentEnd]['line']) {
            $error = 'The close comment tag must be the only content on the line';
            $fix = $phpcsFile->addFixableError($error, $commentEnd, 'ContentBeforeClosingTag');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $content = $tokens[$commentEnd - 1]['content'];
                if (trim($content) . ' ' !== $content) {
                    $phpcsFile->fixer->replaceToken($commentEnd - 1, trim($content));
                }
                $phpcsFile->fixer->addNewlineBefore($commentEnd);
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Checks if there is no any other content after doc comment closing tag (for multiline doc comment).
     */
    private function checkAfterClose(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        $allowEmptyLineBefore = [
            T_NAMESPACE,
            T_USE,
            T_DOC_COMMENT_OPEN_TAG,
        ];

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $commentStart - 1, null, true);
        $next = $phpcsFile->findNext(T_WHITESPACE, $commentEnd + 1, null, true);

        if (! $next) {
            $error = 'Doc comment is not allowed at the end of the file';
            $phpcsFile->addError($error, $commentStart, 'DocCommentAtTheEndOfTheFile');
            return;
        }

        if ($tokens[$commentEnd]['line'] === $tokens[$next]['line']) {
            $error = 'The close comment tag must be the only content on the line';
            $fix = $phpcsFile->addFixableError($error, $commentEnd, 'ContentAfterClosingTag');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $newLine = $commentEnd;
                if ($tokens[$commentEnd + 1]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($commentEnd + 1, '');
                    $newLine++;
                }
                $phpcsFile->fixer->addNewline($newLine);
                $phpcsFile->fixer->endChangeset();
            }
        } elseif ($tokens[$prev]['code'] === T_OPEN_TAG) {
            if ($tokens[$next]['line'] === $tokens[$commentEnd]['line'] + 1) {
                $error = 'Missing blank line after file doc comment';
                $fix = $phpcsFile->addFixableError($error, $commentEnd, 'MissingBlankLineAfter');

                if ($fix) {
                    $phpcsFile->fixer->addNewline($commentEnd);
                }
            }
        } elseif ($tokens[$next]['line'] > $tokens[$commentEnd]['line'] + 1
            && ! in_array($tokens[$next]['code'], $allowEmptyLineBefore, true)
        ) {
            $error = 'Additional blank lines found after doc comment';
            $fix = $phpcsFile->addFixableError($error, $commentEnd + 2, 'BlankLinesAfter');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $commentEnd + 1; $i < $next; $i++) {
                    if ($tokens[$i + 1]['line'] === $tokens[$next]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Checks if there is exactly one space after doc comment opening tag,
     * and exactly one space before closing tag (for single line doc comment).
     */
    private function checkSpacesInOneLineComment(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        // Check, if there is exactly one space after opening tag.
        if ($tokens[$commentStart + 1]['code'] === T_DOC_COMMENT_WHITESPACE
            && $tokens[$commentStart + 1]['content'] !== ' '
        ) {
            $error = 'Expected 1 space after opening tag of one line doc block comment';
            $fix = $phpcsFile->addFixableError($error, $commentStart + 1, 'InvalidSpacing');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($commentStart + 1, ' ');
            }
        } elseif ($tokens[$commentStart + 1]['code'] !== T_DOC_COMMENT_WHITESPACE) {
            $error = 'Expected 1 space after opening tag of one line doc block comment';
            $fix = $phpcsFile->addFixableError($error, $commentStart, 'InvalidSpacing');

            if ($fix) {
                $phpcsFile->fixer->addContent($commentStart, ' ');
            }
        }

        // Check, if there is exactly one space before closing tag.
        $content = $tokens[$commentEnd - 1]['content'];
        if (trim($content) . ' ' !== $content) {
            $error = 'Expected 1 space before closing tag of one line doc block comment';
            $fix = $phpcsFile->addFixableError($error, $commentEnd - 1, 'InvalidSpacing');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($commentEnd - 1, trim($content) . ' ');
            }
        }
    }

    /**
     * Checks if there is one space after star in multiline doc comment.
     * More than one space is allowed, unless the line contains tag.
     */
    private function checkSpacesAfterStar(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();
        $firstTag = $tokens[$commentStart]['comment_tags'][0] ?? null;

        $tagSpaces = [];

        $replaces = [];
        $next = $commentStart;
        $search = [T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG];
        while ($next = $phpcsFile->findNext($search, $next + 1, $commentEnd + 1)) {
            if (($tokens[$next + 1]['code'] !== T_DOC_COMMENT_WHITESPACE
                    && $tokens[$next]['code'] === T_DOC_COMMENT_STAR)
                || ($tokens[$next + 1]['code'] === T_DOC_COMMENT_WHITESPACE
                    && strpos($tokens[$next + 1]['content'], $phpcsFile->eolChar) === false)
            ) {
                $openNestingTokens = [];

                $nested = 0;
                if ($firstTag) {
                    $prev = $next;
                    $expectedTokens = [T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG];
                    while ($prev = $phpcsFile->findPrevious($expectedTokens, $prev - 1, $firstTag)) {
                        if (in_array($tokens[$prev]['content'][0], ['}', ')'], true)) {
                            --$nested;
                        }

                        $last = substr(trim($tokens[$prev]['content']), -1);
                        if (in_array($last, ['{', '('], true)) {
                            ++$nested;
                            $openNestingTokens[] = $prev;
                        }
                    }
                }

                $lastInLine = $next + 1;
                while ($tokens[$lastInLine + 1]['line'] === $tokens[$lastInLine]['line']
                    && strpos($tokens[$lastInLine + 1]['content'], $phpcsFile->eolChar) === false
                ) {
                    ++$lastInLine;
                }

                $expectedSpaces = 1 + $this->indent * $nested;
                $expected = str_repeat(' ', $expectedSpaces);

                if ($tokens[$next + 1]['code'] !== T_DOC_COMMENT_WHITESPACE) {
                    $error = 'There must be exactly %d space(s) between star and comment; found 0';
                    $data = [
                        $expectedSpaces,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $next, 'NoSpaceAfterStar', $data);

                    if ($fix) {
                        $phpcsFile->fixer->addContent($next, $expected);
                    }
                } elseif ($tokens[$next + 1]['content'] !== $expected
                    && (strpos($tokens[$next + 2]['content'], '@') === 0
                        || $tokens[$next + 1]['line'] === $tokens[$commentStart]['line'] + 1)
                ) {
                    $error = 'There must be exactly %d space(s) between star and comment';
                    $data = [
                        $expectedSpaces,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $next + 1, 'TooManySpacesAfterStar', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($next + 1, $expected);
                    }
                } elseif ($tokens[$next + 2]['code'] !== T_DOC_COMMENT_TAG
                    && $tokens[$next + 2]['code'] !== T_DOC_COMMENT_CLOSE_TAG
                ) {
                    $prev = $phpcsFile->findPrevious(
                        [
                            T_DOC_COMMENT_WHITESPACE,
                            T_DOC_COMMENT_STRING,
                            T_DOC_COMMENT_STAR,
                        ],
                        $next - 1,
                        null,
                        true
                    );

                    // could be doc string or tag
                    $prev2 = $phpcsFile->findPrevious(
                        [
                            T_DOC_COMMENT_WHITESPACE,
                            T_DOC_COMMENT_STAR,
                        ],
                        $next - 1,
                        $prev,
                        true
                    );

                    $closingBracket = false;
                    if (in_array($tokens[$next + 2]['content'][0], ['}', ')'], true)) {
                        $expectedBracket = $tokens[$next + 2]['content'][0] === '}' ? '{' : '(';

                        $lvl = -1;
                        $token = $next;
                        $expectedTokens = [T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG];
                        while ($token = $phpcsFile->findPrevious($expectedTokens, $token - 1, $firstTag)) {
                            $last = substr(trim($tokens[$token]['content']), -1);
                            if (in_array($last, ['{', '('], true)) {
                                ++$lvl;
                            }

                            if ($lvl === 0) {
                                break;
                            }

                            if (in_array($tokens[$token]['content'][0], ['}', ')'], true)) {
                                --$lvl;
                            }
                        }

                        if (isset($tagSpaces[$token]) && $last === $expectedBracket) {
                            $closingBracket = true;
                            $expectedSpaces = $tagSpaces[$token];
                        } else {
                            $expectedSpaces -= $this->indent;
                        }
                    } elseif (! in_array($prev2, $openNestingTokens, true)
                        && ! in_array($prev, $openNestingTokens, true)
                    ) {
                        $expectedSpaces += $this->indent;
                    }

                    $spaces = strlen(preg_replace('/^( *).*$/', '\\1', $tokens[$next + 1]['content']));
                    if (! isset($replaces[$prev][$spaces])) {
                        $replaces[$prev][$spaces] = $spaces;
                    }

                    if ($tokens[$prev]['code'] === T_DOC_COMMENT_TAG
                        && ($spaces < $expectedSpaces
                            || (($spaces - 1) % $this->indent) !== 0
                            || ($spaces > $expectedSpaces
                                && $tokens[$prev]['line'] === $tokens[$next + 1]['line'] - 1)
                            || ($spaces > $expectedSpaces
                                && $closingBracket))
                    ) {
                        if ($tokens[$prev2]['line'] === $tokens[$next]['line'] - 1) {
                            if (isset($replaces[$prev][$spaces]) && $replaces[$prev][$spaces] !== $spaces) {
                                $expectedSpaces = $replaces[$prev][$spaces];
                            } elseif (! $closingBracket
                                && $tokens[$prev]['line'] !== $tokens[$next + 1]['line'] - 1
                            ) {
                                $expectedSpaces = 1 + (int) max(
                                    round(($spaces - 1) / $this->indent) * $this->indent,
                                    $this->indent
                                );
                            }
                            $replaces[$prev][$spaces] = $expectedSpaces;

                            if ($spaces !== $expectedSpaces) {
                                $error = 'Invalid indent before description; expected %d spaces, found %d';
                                $data = [
                                    $expectedSpaces,
                                    $spaces,
                                ];
                                $fix = $phpcsFile->addFixableError(
                                    $error,
                                    $next + 1,
                                    'InvalidDescriptionIndent',
                                    $data
                                );

                                if ($fix) {
                                    $phpcsFile->fixer->replaceToken($next + 1, str_repeat(' ', $expectedSpaces));
                                }
                            }
                        } else {
                            $error = 'Additional description is not allowed after tag.'
                                . ' Please move description to the top of the PHPDocs'
                                . ' or remove empty line above if it is description for the tag';
                            $phpcsFile->addError($error, $next + 1, 'AdditionalDescription');
                        }
                    } else {
                        $expectedSpaces = $spaces;
                    }
                }

                $tagSpaces[$lastInLine] = $expectedSpaces;
            }
        }
    }

    private function checkStarInEveryLine(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        $firstLine = $tokens[$commentStart]['line'] + 1;
        $lastLine = $tokens[$commentEnd]['line'] - 1;

        $indentToken = $tokens[$commentStart - 1];
        if ($indentToken['code'] === T_WHITESPACE
            && $indentToken['line'] === $tokens[$commentStart]['line']
        ) {
            $indent = strlen($indentToken['content']) + 1;
        } else {
            $indent = 1;
        }

        $currentLine = $firstLine - 1;
        for ($i = $commentStart; $i < $commentEnd; ++$i) {
            $line = $tokens[$i]['line'];
            if ($line > $lastLine) {
                break;
            }

            if ($line === $currentLine + 1) {
                $currentLine = $line;
                if ($tokens[$i]['code'] === T_DOC_COMMENT_STAR) {
                    continue;
                }

                if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE
                    && $tokens[$i + 1]['line'] === $line
                    && $tokens[$i + 1]['code'] === T_DOC_COMMENT_STAR
                ) {
                    continue;
                }

                // blank line in a comment, skipping here
                if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE
                    && $tokens[$i + 1]['line'] > $line
                ) {
                    continue;
                }

                $error = 'Missing star at the beginning doc-block comment line';
                $fix = $phpcsFile->addFixableError($error, $i, 'MissingStar');

                if ($fix) {
                    if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE) {
                        $len = $tokens[$i]['length'];
                        $spaces = max($len - $indent - 1, 1);
                        $content = str_repeat(' ', $indent) . '*' . str_repeat(' ', $spaces);
                        $phpcsFile->fixer->replaceToken($i, $content);
                    } else {
                        $phpcsFile->fixer->addContentBefore($i, str_repeat(' ', $indent) . '* ');
                    }
                }
            }
        }
    }

    /**
     * Doc comment cannot have empty line on the beginning of the comment, at the end of the comment,
     * and there is allowed only one empty line between two comment sections.
     */
    private function checkBlankLinesInComment(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        $empty = [
            T_DOC_COMMENT_WHITESPACE,
            T_DOC_COMMENT_STAR,
        ];

        // Additional blank lines at the beginning of doc block.
        $next = $phpcsFile->findNext($empty, $commentStart + 1, null, true);
        if ($tokens[$next]['line'] > $tokens[$commentStart]['line'] + 1) {
            $error = 'Additional blank lines found at the beginning of doc comment';
            $fix = $phpcsFile->addFixableError($error, $commentStart + 2, 'SpacingBefore');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $commentStart + 1; $i < $next; $i++) {
                    if ($tokens[$i + 1]['line'] === $tokens[$next]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        // Additional blank lines at the and of doc block.
        $previous = $phpcsFile->findPrevious($empty, $commentEnd - 1, null, true);
        if ($tokens[$previous]['line'] < $tokens[$commentEnd]['line'] - 1) {
            $error = 'Additional blank lines found at the end of doc comment';
            $fix = $phpcsFile->addFixableError($error, $previous + 2, 'SpacingAfter');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $previous + 1; $i < $commentEnd; $i++) {
                    if ($tokens[$i + 1]['line'] === $tokens[$commentEnd]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }
        }

        // Check for double blank lines.
        $from = $phpcsFile->findNext($empty, $commentStart + 1, null, true);
        $to = $phpcsFile->findPrevious($empty, $commentEnd - 1, null, true);

        while ($next = $phpcsFile->findNext($empty, $from + 1, $to, true)) {
            if ($tokens[$next]['line'] > $tokens[$from]['line'] + 2) {
                $error = 'More than one blank line between parts of doc block';
                $i = 0;
                while ($token = $phpcsFile->findNext(T_DOC_COMMENT_STAR, $from + 1, $next - 2)) {
                    if ($i++ > 0) {
                        $fix = $phpcsFile->addFixableError($error, $token, 'MultipleBlankLines');

                        if ($fix) {
                            $firstOnLine = $phpcsFile->findFirstOnLine($empty, $token);
                            for ($n = $firstOnLine; $n <= $token + 1; $n++) {
                                $phpcsFile->fixer->replaceToken($n, '');
                            }
                        }
                    }

                    $from = $token;
                }
            }

            $from = $next;
        }

        // Check for blank lines without *
        $from = $commentStart;
        $to = $commentEnd;

        while ($next = $phpcsFile->findNext(T_DOC_COMMENT_WHITESPACE, $from + 1, $to + 1, true)) {
            if ($tokens[$next]['line'] > $tokens[$from]['line'] + 1) {
                $ptr = $from + 1;
                while ($tokens[$ptr]['line'] === $tokens[$from]['line']) {
                    ++$ptr;
                }

                $error = 'Blank line found in PHPDoc comment';
                $fix = $phpcsFile->addFixableError($error, $ptr, 'BlankLine');

                if ($fix) {
                    $phpcsFile->fixer->addContentBefore($ptr, '*');
                }
            }

            $from = $next;
        }
    }

    /**
     * Checks indents of the comment (opening tag, lines with star, closing tag).
     */
    private function checkCommentIndents(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        $allowEmptyLineBefore = [
            T_NAMESPACE,
            T_USE,
        ];

        $next = $phpcsFile->findNext(T_WHITESPACE, $commentEnd + 1, null, true);

        // There is something exactly in the next line.
        if ($next && $tokens[$next]['line'] === $tokens[$commentEnd]['line'] + 1) {
            // Check indent of the next line.
            if ($tokens[$next - 1]['code'] === T_WHITESPACE
                && strpos($tokens[$next - 1]['content'], $phpcsFile->eolChar) === false
            ) {
                $indent = strlen($tokens[$next - 1]['content']);
            } else {
                $indent = 0;
            }
        } elseif (! $next
            || ($tokens[$next]['line'] > $tokens[$commentEnd]['line'] + 1
                && in_array($tokens[$next]['code'], $allowEmptyLineBefore, true))
        ) {
            $indent = 0;
        } else {
            return;
        }

        // The open tag is alone in the line.
        $previous = $phpcsFile->findPrevious(T_WHITESPACE, $commentStart - 1, null, true);
        if ($tokens[$previous]['line'] < $tokens[$commentStart]['line']) {
            // Check if comment starts with the same indent.
            $spaces = $tokens[$commentStart - 1];
            if ($spaces['code'] === T_WHITESPACE
                && strpos($spaces['content'], $phpcsFile->eolChar) === false
                && strlen($spaces['content']) !== $indent
            ) {
                $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                $data = [
                    $indent,
                    strlen($spaces['content']),
                ];
                $fix = $phpcsFile->addFixableError($error, $commentStart, 'InvalidIndent', $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($commentStart - 1, str_repeat(' ', $indent));
                }
            } elseif ($spaces['code'] === T_WHITESPACE
                && strpos($spaces['content'], $phpcsFile->eolChar) !== false
                && $indent > 0
            ) {
                $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                $data = [
                    $indent,
                    0,
                ];
                $fix = $phpcsFile->addFixableError($error, $commentStart, 'InvalidIndent', $data);

                if ($fix) {
                    $phpcsFile->fixer->replaceToken(
                        $commentStart - 1,
                        $phpcsFile->eolChar . str_repeat(' ', $indent)
                    );
                }
            }
        }

        // This is one-line doc comment.
        if ($tokens[$commentStart]['line'] === $tokens[$commentEnd]['line']) {
            return;
        }

        // Rest of the doc comment.
        $from = $commentStart;
        $search = [T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG];
        while ($next = $phpcsFile->findNext($search, $from + 1, $commentEnd + 1)) {
            if ($tokens[$next]['line'] !== $tokens[$from]['line']) {
                $spaces = $tokens[$next - 1];

                if (strpos($spaces['content'], $phpcsFile->eolChar) !== false) {
                    $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                    $data = [
                        $indent + 1,
                        0,
                    ];
                    $fix = $phpcsFile->addFixableError($error, $next, 'InvalidIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($next - 1, $phpcsFile->eolChar . ' ');
                    }
                } elseif ($spaces['code'] === T_DOC_COMMENT_WHITESPACE
                    && strlen($spaces['content']) !== $indent + 1
                ) {
                    $error = 'Invalid doc comment indent. Expected %d spaces; %d found';
                    $data = [
                        $indent + 1,
                        strlen($spaces['content']),
                    ];
                    $fix = $phpcsFile->addFixableError($error, $next, 'InvalidIndent', $data);

                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($next - 1, str_repeat(' ', $indent + 1));
                    }
                }
            }

            $from = $next;
        }
    }

    /**
     * Check if there is one blank line before comment tags.
     */
    private function checkBlankLineBeforeTags(File $phpcsFile, int $commentStart) : void
    {
        $tokens = $phpcsFile->getTokens();

        if (! $tokens[$commentStart]['comment_tags']) {
            return;
        }

        $tag = $tokens[$commentStart]['comment_tags'][0];
        $beforeTag = $phpcsFile->findPrevious(
            [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
            $tag - 1,
            null,
            true
        );

        if ($tokens[$beforeTag]['code'] === T_DOC_COMMENT_STRING
            && $tokens[$beforeTag]['line'] === $tokens[$tag]['line'] - 1
        ) {
            $firstOnLine = $phpcsFile->findFirstOnLine([], $tag, true);

            $error = 'Missing blank line before comment tags';
            $fix = $phpcsFile->addFixableError($error, $firstOnLine, 'MissingBlankLine');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewlineBefore($firstOnLine);
                $phpcsFile->fixer->addContentBefore($firstOnLine, '*');
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    private function checkTagsSpaces(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        // Return when there is no tags in the comment.
        if (empty($tokens[$commentStart]['comment_tags'])) {
            return;
        }

        // Return when comment contains one of the following tags.
        $skipIfContains = ['@copyright', '@license'];
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if (in_array(strtolower($tokens[$tag]['content']), $skipIfContains, true)) {
                return;
            }
        }

        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            // Continue if next token is not a whitespace.
            if ($tokens[$tag + 1]['code'] !== T_DOC_COMMENT_WHITESPACE) {
                continue;
            }

            if (in_array($tokens[$tag]['content'], $this->tagsWithContent, true)) {
                $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
                if (! $string || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                    $error = 'Content missing for %s tag in PHPDoc comment';
                    $data = [$tokens[$tag]['content']];
                    $phpcsFile->addError($error, $tag, 'EmptyTagContent', $data);
                }
            }

            // Continue if next token contains new line.
            if (strpos($tokens[$tag + 1]['content'], $phpcsFile->eolChar) !== false) {
                continue;
            }

            // Continue if after next token the comment ends.
            // It means end of the comment is in the same line as the tag.
            if ($tokens[$tag + 2]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                continue;
            }

            // Check spaces after type for some tags.
            if (in_array(strtolower($tokens[$tag]['content']), $this->tagWithType, true)
                && $tokens[$tag + 2]['code'] === T_DOC_COMMENT_STRING
            ) {
                $this->checkSpacesAfterTag($phpcsFile, $tag);
            }

            // Continue if next token is one space.
            if ($tokens[$tag + 1]['content'] === ' ') {
                continue;
            }

            $error = 'There must be exactly one space after PHPDoc tag';
            $fix = $phpcsFile->addFixableError($error, $tag + 1, 'SpaceAfterTag');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($tag + 1, ' ');
            }
        }
    }

    private function checkSpacesAfterTag(File $phpcsFile, int $tag) : void
    {
        $tokens = $phpcsFile->getTokens();

        $content = $tokens[$tag + 2]['content'];
        $expected = implode(' ', array_filter(preg_split('/\s+/', $content)));

        if ($tokens[$tag + 3]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            // In case when spacing between type and variable is correct.
            // Space before closing comment tag are covered in another case.
            if (trim($content) === $expected) {
                return;
            }

            $expected .= ' ';
        }

        if ($content === $expected) {
            return;
        }

        $error = 'Invalid spacing in comment; expected: "%s", found "%s"';
        $data = [
            $expected,
            $content,
        ];
        $fix = $phpcsFile->addFixableError($error, $tag + 2, 'TagDescriptionSpacing', $data);

        if ($fix) {
            $phpcsFile->fixer->replaceToken($tag + 2, $expected);
        }
    }

    private function checkInheritDoc(File $phpcsFile, int $commentStart, int $commentEnd) : void
    {
        $tokens = $phpcsFile->getTokens();

        $commentContent = $phpcsFile->getTokensAsString($commentStart + 1, $commentEnd - $commentStart - 1);
        if (preg_match('/\*.*\{@inheritDoc\}/i', $commentContent, $m)) {
            $error = 'Tag {@inheritDoc} is not allowed in doc-block comment. Please define explicitly types';
            $phpcsFile->addError($error, $commentStart, 'InheritDoc');
            return;
        }

        if (isset($tokens[$commentStart]['comment_tags'])) {
            foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
                if (strtolower($tokens[$tag]['content']) === '@inheritdoc') {
                    $error = 'Tag @inheritDoc is not allowed in doc-block comment. Please define explicitly types';
                    $phpcsFile->addError($error, $tag, 'InheritDocTag');
                    break;
                }
            }
        }
    }
}
