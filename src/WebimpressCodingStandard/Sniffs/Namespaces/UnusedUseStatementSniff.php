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
use const T_CURLY_OPEN;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOUBLE_COLON;
use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_OBJECT_OPERATOR;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;
use const T_WHITESPACE;

/**
 * Below class is mixture of:
 *
 * @see http://jdon.at/1h0wb
 * @see https://github.com/squizlabs/PHP_CodeSniffer/pull/1106
 *     - added checks in annotations
 *     - added checks in return type (PHP 7.0+)
 *     - remove unused use statements in files without namespace
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
        $tokens = $phpcsFile->getTokens();

        // Only check use statements in the global scope.
        if (! CodingStandard::isGlobalUse($phpcsFile, $stackPtr)) {
            return;
        }

        // Seek to the end of the statement and get the string before the semi colon.
        // It works only with one USE keyword per declaration.
        $semiColon = $phpcsFile->findEndOfStatement($stackPtr);
        if ($tokens[$semiColon]['code'] !== T_SEMICOLON) {
            return;
        }

        $classPtr = $phpcsFile->findPrevious(
            Tokens::$emptyTokens,
            $semiColon - 1,
            null,
            true
        );

        // Search where the class name is used. PHP treats class names case
        // insensitive, that's why we cannot search for the exact class name string
        // and need to iterate over all T_STRING tokens in the file.
        $classUsed = $phpcsFile->findNext($this->checkInTokens, $classPtr + 1);
        $className = $tokens[$classPtr]['content'];
        $lowerClassName = strtolower($className);

        // Check if the referenced class is in the same namespace as the current
        // file. If it is then the use statement is not necessary.
        $namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);

        // Check if the use statement does aliasing with the "as" keyword. Aliasing
        // is allowed even in the same namespace.
        $aliasUsed = $phpcsFile->findPrevious(T_AS, $classPtr - 1, $stackPtr);

        if ($aliasUsed === false) {
            $useNamespacePtr = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
            if (in_array(strtolower($tokens[$useNamespacePtr]['content']), ['const', 'function'], true)) {
                ++$useNamespacePtr;
            }
            $useNamespace = $this->getNamespace($phpcsFile, $useNamespacePtr, [T_AS, T_CURLY_OPEN, T_SEMICOLON]);
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

            if (($isStringToken && strtolower($tokens[$classUsed]['content']) === $lowerClassName)
                || ($tokens[$classUsed]['code'] === T_DOC_COMMENT_STRING
                    && preg_match(
                        '/(\s|\||^)' . preg_quote($lowerClassName, '/') . '(\s|\||\\\\|$|\[\])/i',
                        $tokens[$classUsed]['content']
                    ))
                || ($tokens[$classUsed]['code'] === T_DOC_COMMENT_TAG
                    && preg_match(
                        '/@' . preg_quote($lowerClassName, '/') . '(\(|\\\\|$)/i',
                        $tokens[$classUsed]['content']
                    ))
            ) {
                $beforeUsage = $phpcsFile->findPrevious(
                    $isStringToken ? Tokens::$emptyTokens : $emptyTokens,
                    $classUsed - 1,
                    null,
                    true
                );

                if ($isStringToken) {
                    // If a backslash is used before the class name then this is some other
                    // use statement.
                    if ($tokens[$beforeUsage]['code'] !== T_USE
                        && $tokens[$beforeUsage]['code'] !== T_NS_SEPARATOR
                        && $tokens[$beforeUsage]['code'] !== T_OBJECT_OPERATOR
                        && $tokens[$beforeUsage]['code'] !== T_DOUBLE_COLON
                    ) {
                        return;
                    }

                    // Trait use statement within a class.
                    if ($tokens[$beforeUsage]['code'] === T_USE
                        && ! empty($tokens[$beforeUsage]['conditions'])
                    ) {
                        return;
                    }
                } elseif ($tokens[$classUsed]['code'] === T_DOC_COMMENT_STRING) {
                    if ($tokens[$beforeUsage]['code'] === T_DOC_COMMENT_TAG
                        && in_array(
                            $tokens[$beforeUsage]['content'],
                            ['@var', '@param', '@return', '@throws', '@method'],
                            true
                        )
                    ) {
                        return;
                    }
                } else {
                    return;
                }
            }

            $classUsed = $phpcsFile->findNext($this->checkInTokens, $classUsed + 1);
        }

        $warning = 'Unused use statement "%s"';
        $data = [$className];
        $fix = $phpcsFile->addFixableWarning($warning, $stackPtr, 'UnusedUse', $data);

        if ($fix) {
            // Remove the whole use statement line.
            $phpcsFile->fixer->beginChangeset();
            for ($i = $stackPtr; $i <= $semiColon; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            // Also remove whitespace after the semicolon (new lines).
            if (isset($tokens[$i]) && $tokens[$i]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            $phpcsFile->fixer->endChangeset();

            $phpcsFile->recordMetric($stackPtr, __CLASS__, $stackPtr);
        }
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
