<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use WebimpressCodingStandard\Helper\NamespacesTrait;

use function in_array;
use function sort;
use function sprintf;
use function strtolower;

use const T_DOUBLE_COLON;
use const T_FUNCTION;
use const T_NAMESPACE;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_WHITESPACE;

class ImportInternalFunctionSniff implements Sniff
{
    use NamespacesTrait;

    /**
     * @var string[] Array of functions to exclude from importing.
     */
    public $exclude = [];

    /**
     * @var array Hash map of all php built in function names.
     */
    private $builtInFunctions;

    /**
     * @var array Array of imported functions in current namespace.
     */
    private $importedFunctions;

    public function __construct()
    {
        $this->builtInFunctions = $this->getBuiltInFunctions();
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
        $functionsToImport = [];

        do {
            $namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1) ?: null;

            if ($namespacePtr !== $currentNamespacePtr) {
                if ($currentNamespacePtr) {
                    $this->importFunctions($phpcsFile, $currentNamespacePtr, $functionsToImport);
                }

                $currentNamespacePtr = $namespacePtr;
                $functionsToImport = [];

                $this->importedFunctions = $this->getGlobalUses($phpcsFile, $namespacePtr ?: 0, 'function');

                foreach ($this->importedFunctions as $func) {
                    $fqn = strtolower($func['fqn']);

                    if (in_array($fqn, $this->exclude, true)) {
                        $error = 'Function %s cannot be imported';
                        $data = [$func['fqn']];
                        $fix = $phpcsFile->addFixableError($error, $func['ptr'], 'ExcludeImported', $data);

                        if ($fix) {
                            $eos = $phpcsFile->findEndOfStatement($func['ptr']);

                            $phpcsFile->fixer->beginChangeset();
                            for ($i = $func['ptr']; $i <= $eos; ++$i) {
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

            if ($functionName = $this->processString($phpcsFile, $stackPtr, $namespacePtr ?: null)) {
                $functionsToImport[] = $functionName;
            }
        } while ($stackPtr = $phpcsFile->findNext($this->register(), $stackPtr + 1));

        if ($currentNamespacePtr) {
            $this->importFunctions($phpcsFile, $currentNamespacePtr, $functionsToImport);
        }

        return $phpcsFile->numTokens + 1;
    }

    private function processString(File $phpcsFile, int $stackPtr, ?int $namespacePtr) : ?string
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure this is a function call.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if (! $next || $tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            return null;
        }

        $content = strtolower($tokens[$stackPtr]['content']);
        if (! isset($this->builtInFunctions[$content])) {
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
                $error = 'FQN for PHP internal function "%s" is not needed here, file does not have defined namespace';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoNamespace', $data);
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
            } elseif (in_array($content, $this->exclude, true)) {
                $error = 'FQN for PHP internal function "%s" is not allowed here';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ExcludeRedundantFQN', $data);
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
            } elseif (isset($this->importedFunctions[$content])) {
                if (strtolower($this->importedFunctions[$content]['fqn']) === $content) {
                    $error = 'FQN for PHP internal function "%s" is not needed here, function is already imported';
                    $data = [
                        $content,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'RedundantFQN', $data);
                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($prev, '');
                    }
                }
            } else {
                $error = 'PHP internal function "%s" must be imported';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ImportFQN', $data);
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($prev, '');
                    $phpcsFile->fixer->endChangeset();

                    return $this->importFunction($content);
                }
            }
        } elseif ($namespacePtr) {
            if (! isset($this->importedFunctions[$content])
                && ! in_array($content, $this->exclude, true)
            ) {
                $error = 'PHP internal function "%s" must be imported';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Import', $data);
                if ($fix) {
                    return $this->importFunction($content);
                }
            }
        }

        return null;
    }

    private function importFunction(string $functionName) : string
    {
        $this->importedFunctions[$functionName] = [
            'name' => $functionName,
            'fqn' => $functionName,
        ];

        return $functionName;
    }

    /**
     * @param string[] $functionNames
     */
    private function importFunctions(File $phpcsFile, int $namespacePtr, array $functionNames) : void
    {
        if (! $functionNames) {
            return;
        }

        sort($functionNames);

        $phpcsFile->fixer->beginChangeset();

        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$namespacePtr]['scope_opener'])) {
            $ptr = $tokens[$namespacePtr]['scope_opener'];
        } else {
            $ptr = $phpcsFile->findEndOfStatement($namespacePtr);
            $phpcsFile->fixer->addNewline($ptr);
        }

        $content = '';
        foreach ($functionNames as $functionName) {
            $content .= sprintf('%suse function %s;', $phpcsFile->eolChar, $functionName);
        }

        $phpcsFile->fixer->addContent($ptr, $content);

        $phpcsFile->fixer->endChangeset();
    }
}
