<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Helper;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use WebimpressCodingStandard\Sniffs\Namespaces\UnusedUseStatementSniff;

use function in_array;
use function strrchr;
use function strtolower;
use function substr;
use function trim;

use const T_AS;
use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_STRING;
use const T_USE;
use const T_WHITESPACE;

/**
 * @internal
 */
trait NamespacesTrait
{
    private function getNamespace(File $phpcsFile, int $stackPtr) : string
    {
        if ($nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1)) {
            $nsEnd = $phpcsFile->findNext([T_NS_SEPARATOR, T_STRING, T_WHITESPACE], $nsStart + 1, null, true);
            return trim($phpcsFile->getTokensAsString($nsStart + 1, $nsEnd - $nsStart - 1));
        }

        return '';
    }

    /**
     * @return array Array of imported classes {
     *     @var array $_ Key is lowercase class alias name {
     *         @var string $alias Original class alias name
     *         @var string $class FQCN
     *     }
     * }
     */
    private function getGlobalUses(File $phpcsFile, int $stackPtr = 0) : array
    {
        $first = 0;
        $last = $phpcsFile->numTokens;

        $tokens = $phpcsFile->getTokens();

        $nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($nsStart && isset($tokens[$nsStart]['scope_opener'])) {
            $first = $tokens[$nsStart]['scope_opener'];
            $last = $tokens[$nsStart]['scope_closer'];
        }

        $imports = [];

        $use = $first;
        while ($use = $phpcsFile->findNext(T_USE, $use + 1, $last)) {
            if (! empty($tokens[$use]['conditions'])) {
                continue;
            }

            if (isset($phpcsFile->getMetrics()[UnusedUseStatementSniff::class]['values'][$use])) {
                continue;
            }

            $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, $use + 1, null, true);

            if ($tokens[$nextToken]['code'] === T_STRING
                && in_array(strtolower($tokens[$nextToken]['content']), ['const', 'function'], true)
            ) {
                continue;
            }

            $end = $phpcsFile->findNext(
                [T_NS_SEPARATOR, T_STRING],
                $nextToken + 1,
                null,
                true
            );

            $class = trim($phpcsFile->getTokensAsString($nextToken, $end - $nextToken));

            $endOfStatement = $phpcsFile->findEndOfStatement($use);
            if ($aliasStart = $phpcsFile->findNext([T_WHITESPACE, T_AS], $end + 1, $endOfStatement, true)) {
                $alias = trim($phpcsFile->getTokensAsString($aliasStart, $endOfStatement - $aliasStart));
            } else {
                if (strrchr($class, '\\') !== false) {
                    $alias = substr(strrchr($class, '\\'), 1);
                } else {
                    $alias = $class;
                }
            }

            $imports[strtolower($alias)] = ['alias' => $alias, 'class' => $class];
        }

        return $imports;
    }
}
