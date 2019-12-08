<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Helper;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use WebimpressCodingStandard\CodingStandard;

use function array_flip;
use function array_walk_recursive;
use function get_defined_constants;
use function get_defined_functions;
use function in_array;
use function ltrim;
use function strrchr;
use function strtolower;
use function strtoupper;
use function substr;

use const T_AS;
use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_STRING;
use const T_USE;

/**
 * @internal
 */
trait NamespacesTrait
{
    private function getNamespace(File $phpcsFile, int $stackPtr) : string
    {
        if ($nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1)) {
            return $this->getName($phpcsFile, $nsStart + 1);
        }

        return '';
    }

    /**
     * @return array Array of imported classes {
     *     @var array $_ Key is lowercase alias name for classes and function and upercase for constants {
     *         @var string $name Original alias name
     *         @var string $fqn Fully-Qualified Name
     *         @var int $ptr Pointer position of use statement
     *     }
     * }
     */
    private function getGlobalUses(File $phpcsFile, int $stackPtr = 0, string $type = 'class') : array
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
            if (! CodingStandard::isGlobalUse($phpcsFile, $use)) {
                continue;
            }

            $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, $use + 1, null, true);

            $useType = 'class';
            if ($tokens[$nextToken]['code'] === T_STRING
                && in_array(strtolower($tokens[$nextToken]['content']), ['const', 'function'], true)
            ) {
                $useType = strtolower($tokens[$nextToken]['content']);

                // increase token
                ++$nextToken;
            }

            if ($type !== 'all' && $type !== $useType) {
                continue;
            }

            $name = $this->getName($phpcsFile, $nextToken);

            $endOfStatement = $phpcsFile->findEndOfStatement($use);
            $endOfName = $phpcsFile->findNext(
                Tokens::$emptyTokens + [T_NS_SEPARATOR => T_NS_SEPARATOR, T_STRING => T_STRING],
                $nextToken + 1,
                null,
                true
            );

            $aliasStart = $phpcsFile->findNext(
                Tokens::$emptyTokens + [T_AS => T_AS],
                $endOfName + 1,
                $endOfStatement,
                true
            );

            if ($aliasStart) {
                $alias = $tokens[$aliasStart]['content'];
            } else {
                $alias = $this->getAliasFromName($name);
            }

            $imports[$useType][$useType === 'const' ? strtoupper($alias) : strtolower($alias)] = [
                'name' => $alias,
                'fqn' => $name,
                'ptr' => $use,
            ];
        }

        return $type === 'all' ? $imports : ($imports[$type] ?? []);
    }

    private function getAliasFromName(string $name) : string
    {
        return strrchr($name, '\\') === false
            ? $name
            : substr(strrchr($name, '\\'), 1);
    }

    private function getName(File $phpcsFile, int $stackPtr) : string
    {
        $tokens = $phpcsFile->getTokens();

        $class = '';
        do {
            if (in_array($tokens[$stackPtr]['code'], Tokens::$emptyTokens, true)) {
                continue;
            }

            if (! in_array($tokens[$stackPtr]['code'], [T_NS_SEPARATOR, T_STRING], true)) {
                break;
            }

            $class .= $tokens[$stackPtr]['content'];
        } while (isset($tokens[++$stackPtr]));

        return ltrim($class, '\\');
    }

    private function getBuiltInFunctions() : array
    {
        $allFunctions = get_defined_functions();

        return array_flip($allFunctions['internal']);
    }

    private function getBuiltInConstants() : array
    {
        $allConstants = get_defined_constants(true);

        $arr = [];
        array_walk_recursive($allConstants, static function ($v, $k) use (&$arr) {
            if (strtolower($k) !== 'user') {
                $arr[$k] = $v;
            }
        });

        return $arr;
    }
}
