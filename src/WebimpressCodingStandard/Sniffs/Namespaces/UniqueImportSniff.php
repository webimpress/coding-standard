<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use WebimpressCodingStandard\CodingStandard;

use function explode;
use function in_array;
use function max;
use function strtolower;

use const T_AS;
use const T_COMMA;
use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_OPEN_TAG;
use const T_OPEN_USE_GROUP;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;

class UniqueImportSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_TAG, T_NAMESPACE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr) : int
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_OPEN_TAG) {
            $namespace = $phpcsFile->findNext(T_NAMESPACE, $stackPtr + 1);
            if ($namespace) {
                return $namespace;
            }
        }

        $uses = $this->getUseStatements($phpcsFile, $stackPtr);

        foreach ($uses as $type => $data) {
            foreach ($data as $name => $ptrs) {
                if (isset($ptrs[1])) {
                    $ptr = max($ptrs);
                    if ($type === 'full') {
                        $error = 'The same %s %s is already imported';
                        $data = [explode('::', $name)[0], $tokens[$ptr]['content']];
                        $phpcsFile->addError($error, $ptr, 'DuplicateImport', $data);
                    } else {
                        $error = 'The name %s is already used for another %s';
                        $data = [$tokens[$ptr]['content'], $type];
                        $phpcsFile->addError($error, $ptr, 'DuplicateAlias', $data);
                    }
                }
            }
        }

        return $tokens[$stackPtr]['code'] === T_OPEN_TAG
            ? $phpcsFile->numTokens + 1
            : $stackPtr + 1;
    }

    /**
     * @return string[][]
     */
    private function getUseStatements(File $phpcsFile, int $scopePtr) : array
    {
        $tokens = $phpcsFile->getTokens();

        $uses = [];

        if (isset($tokens[$scopePtr]['scope_opener'])) {
            $start = $tokens[$scopePtr]['scope_opener'];
            $end = $tokens[$scopePtr]['scope_closer'];
        } else {
            $start = $scopePtr;
            $end = null;
        }

        while ($use = $phpcsFile->findNext(T_USE, $start + 1, $end)) {
            if (! CodingStandard::isGlobalUse($phpcsFile, $use)) {
                $start = $use;
                continue;
            }

            $semicolon = $phpcsFile->findNext(T_SEMICOLON, $use + 1);

            $type = 'class';
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, $use + 1, null, true);
            if ($tokens[$next]['code'] === T_STRING
                && in_array(strtolower($tokens[$next]['content']), ['const', 'function'], true)
            ) {
                $type = strtolower($tokens[$next]['content']);

                $use = $next + 1;
            }

            $current = $semicolon;
            while ($namePtr = $phpcsFile->findPrevious(T_STRING, $current, $use)) {
                $key = $tokens[$namePtr]['content'];
                if ($type !== 'const') {
                    $key = strtolower($key);
                }
                $uses[$type][$key][] = $namePtr;

                $lastPtr = $namePtr;
                $as = $phpcsFile->findPrevious(Tokens::$emptyTokens, $namePtr - 1, null, true);
                if ($tokens[$as]['code'] === T_AS) {
                    $lastPtr = $phpcsFile->findPrevious(T_STRING, $as - 1);
                    $key = $tokens[$lastPtr]['content'];
                    if ($type !== 'const') {
                        $key = strtolower($key);
                    }
                }

                $from = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens + [
                        T_STRING => T_STRING,
                        T_NS_SEPARATOR => T_NS_SEPARATOR,
                    ],
                    $lastPtr - 1,
                    $use,
                    true
                ) ?: $use;

                $full = '';
                for ($i = $from + 1; $i < $lastPtr; ++$i) {
                    if ($tokens[$i]['code'] === T_STRING || $tokens[$i]['code'] === T_NS_SEPARATOR) {
                        $full .= $tokens[$i]['content'];
                    }
                }
                $full .= $key;

                $prev = $phpcsFile->findPrevious(T_OPEN_USE_GROUP, $lastPtr - 1, $use);
                if ($prev) {
                    for ($i = $prev - 1; $i > $use; --$i) {
                        if ($tokens[$i]['code'] === T_STRING || $tokens[$i]['code'] === T_NS_SEPARATOR) {
                            $full = $tokens[$i]['content'] . $full;
                        }
                    }
                }

                $uses['full'][$type . '::' . $full][] = $lastPtr;

                $current = $phpcsFile->findPrevious(T_COMMA, $namePtr - 1, $use);
                if ($current === false) {
                    break;
                }
            }

            $start = $semicolon;
        }

        return $uses;
    }
}
