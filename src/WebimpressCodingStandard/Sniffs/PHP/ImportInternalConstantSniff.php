<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use WebimpressCodingStandard\CodingStandard;

use function array_walk_recursive;
use function get_defined_constants;
use function in_array;
use function ltrim;
use function sort;
use function sprintf;
use function strtolower;
use function strtoupper;

use const T_AS;
use const T_DOUBLE_COLON;
use const T_FUNCTION;
use const T_NAMESPACE;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;
use const T_WHITESPACE;

class ImportInternalConstantSniff implements Sniff
{
    /**
     * @var string[] Array of constants to exclude from importing.
     */
    public $exclude = [];

    /**
     * @var array Hash map of all php built in constant names.
     */
    private $builtInConstants;

    /**
     * @var array Array of imported constant in current namespace.
     */
    private $importedConstants;

    public function __construct()
    {
        $allConstants = get_defined_constants(true);

        $arr = [];
        array_walk_recursive($allConstants, function ($v, $k) use (&$arr) {
            if (strtolower($k) !== 'user') {
                $arr[$k] = $v;
            }
        });

        $this->builtInConstants = $arr;
    }

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_STRING];
    }

    /**
     * @param int $stackPtr
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $currentNamespacePtr = null;
        $constantsToImport = [];
        $lastUse = null;

        do {
            $namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1) ?: null;

            if ($namespacePtr !== $currentNamespacePtr) {
                if ($currentNamespacePtr) {
                    $this->importConstants($phpcsFile, $currentNamespacePtr, $lastUse, $constantsToImport);
                }

                $currentNamespacePtr = $namespacePtr;
                $constantsToImport = [];
                $lastUse = null;
                $this->importedConstants = $this->getImportedConstants($phpcsFile, $namespacePtr, $lastUse);

                foreach ($this->importedConstants as $const) {
                    $fqn = strtoupper($const['fqn']);

                    if (in_array($fqn, $this->exclude, true)) {
                        $error = 'Constant %s cannot be imported';
                        $data = [$const['fqn']];
                        $fix = $phpcsFile->addFixableError($error, $const['ptr'], 'ExcludeImported', $data);

                        if ($fix) {
                            $phpcsFile->fixer->beginChangeset();
                            for ($i = $const['ptr']; $i <= $const['eos']; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }
                            if ($tokens[$i + 1]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken($i + 1, '');
                            }
                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }
            }

            if ($constantName = $this->processString($phpcsFile, $stackPtr, $namespacePtr ?: null)) {
                $constantsToImport[] = $constantName;
            }
        } while ($stackPtr = $phpcsFile->findNext($this->register(), $stackPtr + 1));

        if ($currentNamespacePtr) {
            $this->importConstants($phpcsFile, $currentNamespacePtr, $lastUse, $constantsToImport);
        }

        return $phpcsFile->numTokens + 1;
    }

    private function processString(File $phpcsFile, int $stackPtr, ?int $namespacePtr) : ?string
    {
        $tokens = $phpcsFile->getTokens();

        $content = strtoupper($tokens[$stackPtr]['content']);
        if ($content !== $tokens[$stackPtr]['content']) {
            return null;
        }

        if (! isset($this->builtInConstants[$content])) {
            return null;
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($next && $tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return null;
        }

        $prev = $phpcsFile->findPrevious(
            Tokens::$emptyTokens + [T_NS_SEPARATOR => T_NS_SEPARATOR],
            $stackPtr - 1,
            null,
            true
        );
        if ($tokens[$prev]['code'] === T_FUNCTION
            || $tokens[$prev]['code'] === T_NEW
            || $tokens[$prev]['code'] === T_STRING
            || $tokens[$prev]['code'] === T_DOUBLE_COLON
            || $tokens[$prev]['code'] === T_OBJECT_OPERATOR
        ) {
            return null;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if ($tokens[$prev]['code'] === T_NS_SEPARATOR) {
            if (! $namespacePtr) {
                $error = 'FQN for PHP internal constant "%s" is not needed here, file does not have defined namespace';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoNamespace', $data);
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
            } elseif (in_array($content, $this->exclude, true)) {
                $error = 'FQN for PHP internal constant "%s" is not allowed here';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ExcludeRedundantFQN', $data);
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
            } elseif (isset($this->importedConstants[$content])) {
                if (strtoupper($this->importedConstants[$content]['fqn']) === $content) {
                    $error = 'FQN for PHP internal constant "%s" is not needed here, constant is already imported';
                    $data = [
                        $content,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'RedundantFQN', $data);
                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($prev, '');
                    }
                }
            } else {
                $error = 'PHP internal constant "%s" must be imported';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ImportFQN', $data);
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($prev, '');
                    $phpcsFile->fixer->endChangeset();

                    return $this->importConstant($content);
                }
            }
        } elseif ($namespacePtr) {
            if (! isset($this->importedConstants[$content])
                && ! in_array($content, $this->exclude, true)
            ) {
                $error = 'PHP internal constant "%s" must be imported';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Import', $data);
                if ($fix) {
                    return $this->importConstant($content);
                }
            }
        }

        return null;
    }

    private function importConstant(string $constantName) : string
    {
        $this->importedConstants[$constantName] = [
            'name' => $constantName,
            'fqn' => $constantName,
        ];

        return $constantName;
    }

    /**
     * @param string[] $constantNames
     */
    private function importConstants(File $phpcsFile, int $namespacePtr, ?int $lastUse, array $constantNames) : void
    {
        if (! $constantNames) {
            return;
        }

        sort($constantNames);

        $phpcsFile->fixer->beginChangeset();

        if ($lastUse) {
            $ptr = $phpcsFile->findEndOfStatement($lastUse);
        } else {
            $tokens = $phpcsFile->getTokens();
            if (isset($tokens[$namespacePtr]['scope_opener'])) {
                $ptr = $tokens[$namespacePtr]['scope_opener'];
            } else {
                $ptr = $phpcsFile->findEndOfStatement($namespacePtr);
                $phpcsFile->fixer->addNewline($ptr);
            }
        }

        $content = '';
        foreach ($constantNames as $constantName) {
            $content .= sprintf('%suse const %s;', $phpcsFile->eolChar, $constantName);
        }

        $phpcsFile->fixer->addContent($ptr, $content);
        if (! $lastUse && isset($tokens[$namespacePtr]['scope_opener'])) {
            $phpcsFile->fixer->addNewline($ptr);
        }

        $phpcsFile->fixer->endChangeset();
    }

    /**
     * @return array Array of imported constants {
     *     @var array $_ Key is lowercase constant name {
     *         @var string $name Original constant name
     *         @var string $fqn Fully qualified constant name without leading slashes
     *         @var int $ptr The position of use declaration
     *         @var int $eos The position of the end of the use statement
     *     }
     * }
     */
    private function getImportedConstants(File $phpcsFile, ?int $namespacePtr, ?int &$lastUse) : array
    {
        $first = 0;
        $last = $phpcsFile->numTokens;

        $tokens = $phpcsFile->getTokens();

        if ($namespacePtr && isset($tokens[$namespacePtr]['scope_opener'])) {
            $first = $tokens[$namespacePtr]['scope_opener'];
            $last = $tokens[$namespacePtr]['scope_closer'];
        }

        $constants = [];

        $use = $first;
        while ($use = $phpcsFile->findNext(T_USE, $use + 1, $last)) {
            if (! CodingStandard::isGlobalUse($phpcsFile, $use)) {
                continue;
            }

            if ($next = $this->isConstUse($phpcsFile, $use)) {
                $start = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], $next + 1);
                $end = $phpcsFile->findPrevious(
                    T_STRING,
                    $phpcsFile->findNext([T_AS, T_SEMICOLON], $start + 1) - 1
                );
                $endOfStatement = $phpcsFile->findEndOfStatement($next);
                $name = $phpcsFile->findPrevious(T_STRING, $endOfStatement - 1);
                $fullName = $phpcsFile->getTokensAsString($start, $end - $start + 1);

                $constants[strtoupper($tokens[$name]['content'])] = [
                    'name' => $tokens[$name]['content'],
                    'fqn' => ltrim($fullName, '\\'),
                    'ptr' => $use,
                    'eos' => $endOfStatement,
                ];
            }

            $lastUse = $use;
        }

        return $constants;
    }

    /**
     * @return false|int
     */
    private function isConstUse(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);

        if ($tokens[$next]['code'] === T_STRING
            && strtolower($tokens[$next]['content']) === 'const'
        ) {
            return $next;
        }

        return false;
    }
}
