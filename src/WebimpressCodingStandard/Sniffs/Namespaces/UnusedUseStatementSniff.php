<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use WebimpressCodingStandard\CodingStandard;

use function in_array;
use function preg_match;
use function preg_quote;
use function strcasecmp;
use function strrpos;
use function strtolower;
use function substr;
use function trim;

use const T_AS;
use const T_BITWISE_OR;
use const T_CATCH;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_USE_GROUP;
use const T_COLON;
use const T_COMMA;
use const T_CONST;
use const T_CURLY_OPEN;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOUBLE_COLON;
use const T_ELLIPSIS;
use const T_EXTENDS;
use const T_FUNCTION;
use const T_IMPLEMENTS;
use const T_INSTANCEOF;
use const T_INSTEADOF;
use const T_NAMESPACE;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_OBJECT_OPERATOR;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_USE_GROUP;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;
use const T_VARIABLE;
use const T_WHITESPACE;

/**
 * Below class is mixture of:
 *
 * @see http://jdon.at/1h0wb
 * @see https://github.com/squizlabs/PHP_CodeSniffer/pull/1106
 *     - added checks in annotations
 *     - added checks in return type (PHP 7.0+)
 *     - remove unused use statements in files without namespace
 *     - support for grouped use declarations
 */
class UnusedUseStatementSniff implements Sniff
{
    /**
     * @var int[]
     */
    private $checkInTokens = [
        T_STRING,
        T_DOC_COMMENT_STRING,
        T_DOC_COMMENT_TAG,
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_USE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Only check use statements in the global scope.
        if (! CodingStandard::isGlobalUse($phpcsFile, $stackPtr)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $semiColon = $phpcsFile->findEndOfStatement($stackPtr);
        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $semiColon - 1, null, true);

        if ($tokens[$prev]['code'] === T_CLOSE_USE_GROUP) {
            $to = $prev;
            $from = $phpcsFile->findPrevious(T_OPEN_USE_GROUP, $prev - 1);

            // Empty group is invalid syntax
            if ($phpcsFile->findNext(Tokens::$emptyTokens, $from + 1, null, true) === $to) {
                $error = 'Empty use group';

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'EmptyUseGroup');
                if ($fix) {
                    $this->removeUse($phpcsFile, $stackPtr, $semiColon);
                }

                return;
            }

            $comma = $phpcsFile->findNext(T_COMMA, $from + 1, $to);
            if ($comma === false
                || $phpcsFile->findNext(Tokens::$emptyTokens, $comma + 1, $to, true) === false
            ) {
                $error = 'Redundant use group for one declaration';

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'RedundantUseGroup');
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($from, '');
                    $i = $from + 1;
                    while ($tokens[$i]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($i, '');
                        ++$i;
                    }

                    if ($comma !== false) {
                        $phpcsFile->fixer->replaceToken($comma, '');
                    }

                    $phpcsFile->fixer->replaceToken($to, '');
                    $i = $to - 1;
                    while ($tokens[$i]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($i, '');
                        --$i;
                    }
                    $phpcsFile->fixer->endChangeset();
                }

                return;
            }

            $skip = Tokens::$emptyTokens + [T_COMMA => T_COMMA];

            while ($classPtr = $phpcsFile->findPrevious($skip, $to - 1, $from + 1, true)) {
                $to = $phpcsFile->findPrevious(T_COMMA, $classPtr - 1, $from + 1);

                if (! $this->isClassUsed($phpcsFile, $stackPtr, $classPtr)) {
                    $error = 'Unused use statement "%s"';
                    $data = [$tokens[$classPtr]['content']];

                    $fix = $phpcsFile->addFixableError($error, $classPtr, 'UnusedUseInGroup', $data);
                    if ($fix) {
                        $first = $to === false ? $from + 1 : $to;
                        $last = $classPtr;
                        if ($to === false) {
                            $next = $phpcsFile->findNext(Tokens::$emptyTokens, $classPtr + 1, null, true);
                            if ($tokens[$next]['code'] === T_COMMA) {
                                $last = $next;
                            }
                        }

                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $first; $i <= $last; ++$i) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                        $phpcsFile->fixer->endChangeset();
                    }
                }

                if ($to === false) {
                    break;
                }
            }

            return;
        }

        do {
            $classPtr = $phpcsFile->findPrevious(Tokens::$emptyTokens, $semiColon - 1, null, true);
            if (! $this->isClassUsed($phpcsFile, $stackPtr, $classPtr)) {
                $warning = 'Unused use statement "%s"';
                $data = [$tokens[$classPtr]['content']];
                $fix = $phpcsFile->addFixableError($warning, $stackPtr, 'UnusedUse', $data);

                if ($fix) {
                    $prev = $phpcsFile->findPrevious(
                        Tokens::$emptyTokens + [
                            T_STRING => T_STRING,
                            T_NS_SEPARATOR => T_NS_SEPARATOR,
                            T_AS => T_AS,
                        ],
                        $classPtr,
                        null,
                        true
                    );

                    $to = $semiColon;
                    if ($tokens[$prev]['code'] === T_COMMA) {
                        $from = $prev;
                        $to = $classPtr;
                    } elseif ($tokens[$semiColon]['code'] === T_SEMICOLON) {
                        $from = $stackPtr;
                    } else {
                        $from = $phpcsFile->findNext(Tokens::$emptyTokens, $prev + 1, null, true);
                        if ($tokens[$from]['code'] === T_STRING
                            && in_array(strtolower($tokens[$from]['content']), ['const', 'function'], true)
                        ) {
                            $from = $phpcsFile->findNext(Tokens::$emptyTokens, $from + 1, null, true);
                        }
                    }

                    $this->removeUse($phpcsFile, $from, $to);
                }
            }

            if ($tokens[$semiColon]['code'] === T_SEMICOLON) {
                break;
            }
        } while ($semiColon = $phpcsFile->findEndOfStatement($semiColon + 1));
    }

    private function removeUse(File $phpcsFile, int $from, int $to) : void
    {
        $tokens = $phpcsFile->getTokens();

        $phpcsFile->fixer->beginChangeset();

        // Remote whitespaces before in the same line
        if ($tokens[$from - 1]['code'] === T_WHITESPACE
            && $tokens[$from - 1]['line'] === $tokens[$from]['line']
            && $tokens[$from - 2]['line'] !== $tokens[$from]['line']
        ) {
            $phpcsFile->fixer->replaceToken($from - 1, '');
        }

        for ($i = $from; $i <= $to; ++$i) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        // Also remove whitespace after the semicolon (new lines).
        if (isset($tokens[$to + 1]) && $tokens[$to + 1]['code'] === T_WHITESPACE) {
            $phpcsFile->fixer->replaceToken($to + 1, '');
        }
        $phpcsFile->fixer->endChangeset();
    }

    private function isClassUsed(File $phpcsFile, int $usePtr, int $classPtr) : bool
    {
        $tokens = $phpcsFile->getTokens();

        // Search where the class name is used. PHP treats class names case
        // insensitive, that's why we cannot search for the exact class name string
        // and need to iterate over all T_STRING tokens in the file.
        $classUsed = $phpcsFile->findNext($this->checkInTokens, $classPtr + 1);
        $className = $tokens[$classPtr]['content'];

        // Check if the referenced class is in the same namespace as the current
        // file. If it is then the use statement is not necessary.
        $namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $usePtr);

        $namespaceEnd = $namespacePtr !== false && isset($tokens[$namespacePtr]['scope_closer'])
            ? $tokens[$namespacePtr]['scope_closer']
            : null;

        $type = 'class';
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $usePtr + 1, null, true);
        if ($tokens[$next]['code'] === T_STRING
            && in_array(strtolower($tokens[$next]['content']), ['const', 'function'], true)
        ) {
            $type = strtolower($tokens[$next]['content']);
        }

        $searchName = $type === 'const' ? $className : strtolower($className);

        $prev = $phpcsFile->findPrevious(
            Tokens::$emptyTokens + [
                T_STRING => T_STRING,
                T_NS_SEPARATOR => T_NS_SEPARATOR,
            ],
            $classPtr - 1,
            null,
            $usePtr
        );

        // Only if alias is not used.
        if ($tokens[$prev]['code'] !== T_AS) {
            $isGroup = $tokens[$prev]['code'] === T_OPEN_USE_GROUP
                || $phpcsFile->findPrevious(T_OPEN_USE_GROUP, $prev, $usePtr) !== false;

            $useNamespace = '';
            if ($isGroup || $tokens[$prev]['code'] !== T_COMMA) {
                $useNamespacePtr = $type === 'class' ? $next : $next + 1;
                $useNamespace = $this->getNamespace(
                    $phpcsFile,
                    $useNamespacePtr,
                    [T_OPEN_USE_GROUP, T_COMMA, T_AS, T_SEMICOLON]
                );

                if ($isGroup) {
                    $useNamespace .= '\\';
                }
            }

            if ($tokens[$prev]['code'] === T_COMMA || $tokens[$prev]['code'] === T_OPEN_USE_GROUP) {
                $useNamespace .= $this->getNamespace(
                    $phpcsFile,
                    $prev + 1,
                    [T_CLOSE_USE_GROUP, T_COMMA, T_AS, T_SEMICOLON]
                );
            }

            $useNamespace = substr($useNamespace, 0, strrpos($useNamespace, '\\') ?: 0);

            if ($namespacePtr !== false) {
                $namespace = $this->getNamespace($phpcsFile, $namespacePtr + 1, [T_CURLY_OPEN, T_SEMICOLON]);

                if (strcasecmp($namespace, $useNamespace) === 0) {
                    $classUsed = false;
                }
            } elseif ($namespacePtr === false && $useNamespace === '') {
                $classUsed = false;
            }
        }

        $emptyTokens = Tokens::$emptyTokens;
        unset($emptyTokens[T_DOC_COMMENT_TAG]);

        while ($classUsed !== false) {
            $isStringToken = $tokens[$classUsed]['code'] === T_STRING;

            $match = null;

            if (($isStringToken
                    && (($type !== 'const' && strtolower($tokens[$classUsed]['content']) === $searchName)
                        || ($type === 'const' && $tokens[$classUsed]['content'] === $searchName)))
                || ($type === 'class'
                    && (($tokens[$classUsed]['code'] === T_DOC_COMMENT_STRING
                            && preg_match(
                                '/(\s|\||\(|^)' . preg_quote($searchName, '/') . '(\s|\||\\\\|$|\[\])/i',
                                $tokens[$classUsed]['content']
                            ))
                        || ($tokens[$classUsed]['code'] === T_DOC_COMMENT_TAG
                            && preg_match(
                                '/@' . preg_quote($searchName, '/') . '(\(|\\\\|$)/i',
                                $tokens[$classUsed]['content']
                            ))
                        || (! $isStringToken
                            && ! preg_match(
                                '/"[^"]*' . preg_quote($searchName, '/') . '\b[^"]*"/i',
                                $tokens[$classUsed]['content']
                            )
                            && preg_match(
                                '/(?<!")@' . preg_quote($searchName, '/') . '\b/i',
                                $tokens[$classUsed]['content'],
                                $match
                            ))))
            ) {
                $beforeUsage = $phpcsFile->findPrevious(
                    $isStringToken ? Tokens::$emptyTokens : $emptyTokens,
                    $classUsed - 1,
                    null,
                    true
                );

                if ($isStringToken) {
                    if ($this->determineType($phpcsFile, $beforeUsage, $classUsed) === $type) {
                        return true;
                    }
                } elseif ($tokens[$classUsed]['code'] === T_DOC_COMMENT_STRING) {
                    if ($tokens[$beforeUsage]['code'] === T_DOC_COMMENT_TAG
                        && in_array($tokens[$beforeUsage]['content'], CodingStandard::TAG_WITH_TYPE, true)
                    ) {
                        return true;
                    }

                    if ($match) {
                        return true;
                    }
                } else {
                    return true;
                }
            }

            $classUsed = $phpcsFile->findNext($this->checkInTokens, $classUsed + 1, $namespaceEnd);
        }

        return false;
    }

    private function determineType(File $phpcsFile, int $beforePtr, int $ptr) : ?string
    {
        $tokens = $phpcsFile->getTokens();

        $beforeCode = $tokens[$beforePtr]['code'];

        if (in_array($beforeCode, [
            T_NS_SEPARATOR,
            T_OBJECT_OPERATOR,
            T_DOUBLE_COLON,
            T_FUNCTION,
            T_CONST,
            T_AS,
            T_INSTEADOF,
        ], true)) {
            return null;
        }

        if (in_array($beforeCode, [
            T_NEW,
            T_NULLABLE,
            T_EXTENDS,
            T_IMPLEMENTS,
            T_INSTANCEOF,
        ], true)) {
            return 'class';
        }

        if ($beforeCode === T_COMMA) {
            $prev = $phpcsFile->findPrevious(
                Tokens::$emptyTokens + [
                    T_STRING => T_STRING,
                    T_NS_SEPARATOR => T_NS_SEPARATOR,
                    T_COMMA => T_COMMA,
                ],
                $beforePtr - 1,
                null,
                true
            );

            if ($tokens[$prev]['code'] === T_IMPLEMENTS || $tokens[$prev]['code'] === T_EXTENDS) {
                return 'class';
            }

            if ($tokens[$prev]['code'] === T_INSTEADOF) {
                return null;
            }

            if ($tokens[$prev]['code'] === T_USE) {
                $beforeCode = T_USE;
            }
        }

        // Trait usage
        if ($beforeCode === T_USE) {
            if (CodingStandard::isTraitUse($phpcsFile, $beforePtr)) {
                return 'class';
            }

            return null;
        }

        $afterPtr = $phpcsFile->findNext(Tokens::$emptyTokens, $ptr + 1, null, true);
        $afterCode = $tokens[$afterPtr]['code'];

        if ($afterCode === T_AS) {
            return null;
        }

        if ($afterCode === T_OPEN_PARENTHESIS) {
            return 'function';
        }

        if (in_array($afterCode, [
            T_DOUBLE_COLON,
            T_VARIABLE,
            T_ELLIPSIS,
            T_NS_SEPARATOR,
            T_OPEN_CURLY_BRACKET,
        ], true)) {
            return 'class';
        }

        if ($beforeCode === T_COLON) {
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $beforePtr - 1, null, true);
            if ($prev !== false
                && $tokens[$prev]['code'] === T_CLOSE_PARENTHESIS
                && isset($tokens[$prev]['parenthesis_owner'])
                && $tokens[$tokens[$prev]['parenthesis_owner']]['code'] === T_FUNCTION
            ) {
                return 'class';
            }
        }

        if ($afterCode === T_BITWISE_OR) {
            $prev = $phpcsFile->findPrevious(
                Tokens::$emptyTokens + [
                    T_BITWISE_OR => T_BITWISE_OR,
                    T_STRING => T_STRING,
                    T_NS_SEPARATOR => T_NS_SEPARATOR,
                    T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS,
                ],
                $afterPtr,
                null,
                true
            );

            if ($tokens[$prev]['code'] === T_CATCH) {
                return 'class';
            }
        }

        return 'const';
    }

    private function getNamespace(File $phpcsFile, int $ptr, array $stop) : string
    {
        $tokens = $phpcsFile->getTokens();

        $result = '';
        while (! in_array($tokens[$ptr]['code'], $stop, true)) {
            if (in_array($tokens[$ptr]['code'], [T_STRING, T_NS_SEPARATOR], true)) {
                $result .= $tokens[$ptr]['content'];
            }

            ++$ptr;
        }

        return trim(trim($result), '\\');
    }
}
