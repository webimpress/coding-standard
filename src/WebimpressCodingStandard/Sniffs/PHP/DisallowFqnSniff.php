<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use WebimpressCodingStandard\CodingStandard;
use WebimpressCodingStandard\Helper\NamespacesTrait;

use function array_merge;
use function basename;
use function dirname;
use function end;
use function explode;
use function file_exists;
use function glob;
use function implode;
use function in_array;
use function ltrim;
use function preg_match;
use function preg_match_all;
use function preg_quote;
use function preg_replace;
use function sprintf;
use function str_replace;
use function stripos;
use function strlen;
use function strpos;
use function strstr;
use function strtolower;
use function strtoupper;
use function substr;

use const DIRECTORY_SEPARATOR;
use const GLOB_NOSORT;
use const T_BITWISE_AND;
use const T_BITWISE_OR;
use const T_CASE;
use const T_CATCH;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_COLON;
use const T_COMMA;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_DOUBLE_COLON;
use const T_ECHO;
use const T_ELLIPSIS;
use const T_EXTENDS;
use const T_FN;
use const T_FUNCTION;
use const T_IMPLEMENTS;
use const T_INCLUDE;
use const T_INCLUDE_ONCE;
use const T_INSTANCEOF;
use const T_INSTEADOF;
use const T_LOGICAL_AND;
use const T_LOGICAL_OR;
use const T_LOGICAL_XOR;
use const T_NAMESPACE;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_OPEN_PARENTHESIS;
use const T_PRINT;
use const T_REQUIRE;
use const T_REQUIRE_ONCE;
use const T_RETURN;
use const T_STRING;
use const T_THROW;
use const T_USE;
use const T_VARIABLE;

class DisallowFqnSniff implements Sniff
{
    use NamespacesTrait;

    /**
     * @var array Array of imported classes, constants and functions in current namespace.
     */
    private $imported;

    /**
     * @var array Hash map of all php built in constant names.
     */
    private $builtInConstants;

    /**
     * @var array Hash map of all php built in function names.
     */
    private $builtInFunctions;

    public function __construct()
    {
        $this->builtInConstants = $this->getBuiltInConstants();
        $this->builtInFunctions = $this->getBuiltInFunctions();
    }

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_DOC_COMMENT_TAG,
            T_NS_SEPARATOR,
        ];
    }

    /**
     * @param int $stackPtr
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $namespace = '';
        $currentNamespacePtr = null;
        $toImport = [];
        $toFix = [];

        do {
            $namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1) ?: null;

            if ($namespacePtr !== $currentNamespacePtr) {
                $namespace = $namespacePtr ? $this->getName($phpcsFile, $namespacePtr + 1) : '';
                if ($toImport || $toFix) {
                    $phpcsFile->fixer->beginChangeset();
                    if ($currentNamespacePtr) {
                        $this->importReferences($phpcsFile, $currentNamespacePtr, $toImport);
                    }
                    $this->fixErrors($phpcsFile, $toFix);
                    $phpcsFile->fixer->endChangeset();
                }

                $currentNamespacePtr = $namespacePtr;
                $toImport = [];
                $toFix = [];

                $this->imported = $this->getGlobalUses($phpcsFile, $stackPtr, 'all');
            }

            if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_TAG) {
                $this->processTag($phpcsFile, $stackPtr, $namespace, $toImport, $toFix);
            } else {
                $this->processString($phpcsFile, $stackPtr, $namespace, $toImport, $toFix);
            }
        } while ($stackPtr = $phpcsFile->findNext($this->register(), $stackPtr + 1));

        if ($toImport || $toFix) {
            $phpcsFile->fixer->beginChangeset();
            if ($currentNamespacePtr) {
                $this->importReferences($phpcsFile, $currentNamespacePtr, $toImport);
            }
            $this->fixErrors($phpcsFile, $toFix);
            $phpcsFile->fixer->endChangeset();
        }

        return $phpcsFile->numTokens + 1;
    }

    private function processTag(
        File $phpcsFile,
        int $stackPtr,
        string $namespace,
        array &$toImport,
        array &$toFix
    ) : void {
        $tokens = $phpcsFile->getTokens();

        if (! in_array($tokens[$stackPtr]['content'], CodingStandard::TAG_WITH_TYPE, true)
            || $tokens[$stackPtr + 1]['code'] !== T_DOC_COMMENT_WHITESPACE
            || $tokens[$stackPtr + 2]['code'] !== T_DOC_COMMENT_STRING
        ) {
            return;
        }

        $string = $tokens[$stackPtr + 2]['content'];
        [$type] = explode(' ', $string);
        $types = [$type];

        if ($tokens[$stackPtr]['content'] === '@method'
            && preg_match_all('/(?<=[\s(,])[^(\s,]+?(?=\s+\$)/', $string, $matches)
        ) {
            $types = array_merge($types, $matches[0]);
        }

        foreach ($types as $typesString) {
            $typesArr = explode('|', $typesString);

            // Create local array with classes to import, as we want to update main one only in fix mode
            $localToImport = [];
            $newTypesArr = [];
            foreach ($typesArr as $name) {
                $newTypesArr[] = $this->getExpectedName($phpcsFile, $stackPtr + 2, $namespace, $name, $localToImport);
            }

            $newTypes = implode('|', $newTypesArr);

            if ($newTypes !== $typesString) {
                $error = 'Invalid class name references: expected %s; found %s';
                $data = [
                    $newTypes,
                    $typesString,
                ];
                $fix = $phpcsFile->addFixableError($error, $stackPtr + 2, 'InvalidInPhpDocs', $data);

                if ($fix) {
                    // Update array with references to import
                    if ($localToImport) {
                        $toImport = array_merge($toImport, $localToImport);
                    }

                    $string = preg_replace(
                        '/(^|\s|,|\()' . preg_quote($typesString, '/') . '/',
                        '\\1' . $newTypes,
                        $string
                    );
                    $toFix[$stackPtr + 2] = $string;
                }
            }
        }
    }

    private function getExpectedName(
        File $phpcsFile,
        int $stackPtr,
        string $namespace,
        string $name,
        array &$toImport
    ) : string {
        if (! $namespace) {
            return ltrim($name, '\\');
        }

        if (strpos($name, '\\') !== 0) {
            return $name;
        }

        $suffix = strstr($name, '[');
        if ($suffix && ! preg_match('/^(\[\])+$/', $suffix)) {
            return $name;
        }

        $clear = str_replace(['[', ']'], '', ltrim($name, '\\'));

        if (stripos($clear . '\\', $namespace . '\\') === 0) {
            return substr($clear, strlen($namespace) + 1) . $suffix;
        }

        $alias = $this->getAliasFromName($clear);
        foreach ($this->imported['class'] ?? [] as $class) {
            if (strtolower($class['fqn']) === strtolower($clear)) {
                return $class['name'] . $suffix;
            }

            // If namespace or part of it is already imported
            if (stripos($clear, $class['fqn'] . '\\') === 0) {
                $name = substr($clear, strlen($class['fqn']));
                return $class['name'] . $name . $suffix;
            }
        }

        // We can't suggest anything in that case
        if (! $this->isValidClassName($phpcsFile, $stackPtr, $alias, $clear)) {
            return '\\' . $clear . $suffix;
        }

        // We need to import it
        $toImport += $this->import('class', $clear, $alias);

        return $alias . $suffix;
    }

    private function processString(
        File $phpcsFile,
        int $stackPtr,
        string $namespace,
        array &$toImport,
        array &$toFix
    ) : void {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);

        // Part of the name
        if ($tokens[$prev]['code'] === T_STRING || $tokens[$prev]['code'] === T_NAMESPACE) {
            return;
        }

        // In the global use statement
        if ($tokens[$prev]['code'] === T_USE && CodingStandard::isGlobalUse($phpcsFile, $prev)) {
            return;
        }

        if (! $namespace) {
            $error = 'FQN is not needed here, as file not have defined namespace';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoNamespace');
            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr, '');
            }

            return;
        }

        $next = $phpcsFile->findNext(
            Tokens::$emptyTokens + [T_NS_SEPARATOR => T_NS_SEPARATOR, T_STRING => T_STRING],
            $stackPtr + 1,
            null,
            true
        );

        $prevClassTokens = [
            T_NEW,
            T_USE,
            T_EXTENDS,
            T_IMPLEMENTS,
            T_INSTANCEOF,
            T_INSTEADOF,
            T_NULLABLE,
        ];

        $functionTokens = [
            T_CLOSURE,
            T_FN,
            T_FUNCTION,
        ];

        if (in_array($tokens[$prev]['code'], $prevClassTokens, true)
            || in_array($tokens[$next]['code'], [T_VARIABLE, T_ELLIPSIS, T_DOUBLE_COLON], true)
        ) {
            $type = 'class';
        } elseif ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            $type = 'function';
        } else {
            $type = 'const';
            if ($tokens[$prev]['code'] === T_COLON) {
                $before = $phpcsFile->findPrevious(Tokens::$emptyTokens, $prev - 1, null, true);

                if ($tokens[$before]['code'] === T_CLOSE_PARENTHESIS
                    && isset($tokens[$before]['parenthesis_owner'])
                    && in_array($tokens[$tokens[$before]['parenthesis_owner']]['code'], $functionTokens, true)
                ) {
                    $type = 'class';
                }
            } elseif ($tokens[$next]['code'] === T_BITWISE_AND) {
                if (! empty($tokens[$stackPtr]['nested_parenthesis'])
                    && ($owner = $tokens[end($tokens[$stackPtr]['nested_parenthesis'])]['parenthesis_owner'] ?? 0)
                    && in_array($tokens[$owner]['code'], $functionTokens, true)
                ) {
                    $type = 'class';
                }
            } elseif ($tokens[$next]['code'] === T_BITWISE_OR) {
                if (! empty($tokens[$stackPtr]['nested_parenthesis'])
                    && ($owner = $tokens[end($tokens[$stackPtr]['nested_parenthesis'])]['parenthesis_owner'] ?? 0)
                    && $tokens[$owner]['code'] === T_CATCH
                ) {
                    $type = 'class';
                }
            } elseif ($tokens[$prev]['code'] === T_COMMA) {
                $before = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens + [T_STRING => T_STRING, T_NS_SEPARATOR => T_NS_SEPARATOR],
                    $prev - 1,
                    null,
                    true
                );

                if ($tokens[$before]['code'] === T_IMPLEMENTS) {
                    $type = 'class';
                }
            }
        }

        $name = $this->getName($phpcsFile, $stackPtr);

        // If the found name is in the same namespace
        if (stripos($name . '\\', $namespace . '\\') === 0) {
            $error = 'FQN is disallowed for %s in namespace %s';
            $data = [
                $name,
                $namespace,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SameNamespace', $data);
            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, substr($name, strlen($namespace) + 1));
                $i = $stackPtr;
                while (isset($tokens[++$i])) {
                    if (in_array($tokens[$i]['code'], Tokens::$emptyTokens, true)) {
                        continue;
                    }

                    if (! in_array($tokens[$i]['code'], [T_NS_SEPARATOR, T_STRING], true)) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        // If function is built-in function; skip
        if ($type === 'function' && isset($this->builtInFunctions[strtolower($name)])) {
            return;
        }

        // If constant is built-in constant; skip
        if ($type === 'const' && isset($this->builtInConstants[strtoupper($name)])) {
            return;
        }

        foreach ($this->imported['class'] ?? [] as $class) {
            // If namespace or part of it is already imported
            if (stripos($name . '\\', $class['fqn'] . '\\') === 0) {
                $error = 'Namespace %s is already imported';
                $data = [$class['fqn']];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NamespaceImported', $data);
                if ($fix) {
                    $additional = substr($name, strlen($class['fqn']) + 1);
                    $expected = $class['name'] . ($additional ? '\\' . $additional : '');

                    $phpcsFile->fixer->beginChangeset();
                    $this->fixError($phpcsFile, $stackPtr, $expected);
                    $phpcsFile->fixer->endChangeset();
                }

                return;
            }
        }

        $alias = $this->getAliasFromName($name);

        if ($type === 'function') {
            foreach ($this->imported['function'] ?? [] as $function) {
                // If function is already imported
                if (strtolower($function['fqn']) === strtolower($name)) {
                    $error = 'Function %s is already imported';
                    $data = [$function['fqn']];

                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'FunctionImported', $data);
                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        $this->fixError($phpcsFile, $stackPtr, $function['name']);
                        $phpcsFile->fixer->endChangeset();
                    }

                    return;
                }
            }

            // If alias is in use
            if (isset($this->imported['function'][strtolower($alias)])) {
                $error = 'Function %s must be imported, but alias %s is already in use';
                $data = [$name, $alias];
                $phpcsFile->addError($error, $stackPtr, 'FunctionAliasUsed', $data);

                return;
            }
        }

        if ($type === 'const') {
            foreach ($this->imported['const'] ?? [] as $const) {
                // If constant is already imported
                if (strtolower($const['fqn']) === strtolower($name)) {
                    $error = 'Constant %s is already imported';
                    $data = [$const['fqn']];

                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ConstantImported', $data);
                    if ($fix) {
                        $phpcsFile->fixer->beginChangeset();
                        $this->fixError($phpcsFile, $stackPtr, $const['name']);
                        $phpcsFile->fixer->endChangeset();
                    }

                    return;
                }
            }

            // If alias is in use
            if (isset($this->imported['const'][strtoupper($alias)])) {
                $error = 'Constant %s must be imported, but alias %s is already in use';
                $data = [$name, $alias];
                $phpcsFile->addError($error, $stackPtr, 'ConstantAliasUsed', $data);

                return;
            }
        }

        if ($type === 'class' && ! $this->isValidClassName($phpcsFile, $stackPtr, $alias, $name)) {
            return;
        }

        $error = '%s must be imported as %s';
        $data = [$name, $alias];

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Import', $data);
        if ($fix) {
            $toFix[$stackPtr] = $alias;
            $toImport += $this->import($type, $name, $alias);
        }
    }

    private function isValidClassName(File $phpcsFile, int $stackPtr, string $alias, string $name) : bool
    {
        // If alias is in use
        if (isset($this->imported['class'][strtolower($alias)])) {
            $error = 'Class %s must be imported, but alias %s is already in use';
            $data = [$name, $alias];
            $phpcsFile->addError($error, $stackPtr, 'ClassAliasUsed', $data);

            return false;
        }

        $dirname = dirname($phpcsFile->getFilename());
        if (file_exists($dirname . DIRECTORY_SEPARATOR . $alias)) {
            $error = '%s must be imported but directory with name %s exists in the namespace';
            $data = [$name, $alias];
            $phpcsFile->addError($error, $stackPtr, 'DirName', $data);

            return false;
        }

        $files = glob($dirname . '/*', GLOB_NOSORT);
        foreach ($files as $file) {
            if (stripos(basename($file), $alias . '.') === 0) {
                $error = '%s must be imported but file with name %s exists in the namespace';
                $data = [$name, $alias];
                $phpcsFile->addError($error, $stackPtr, 'FileName', $data);

                return false;
            }
        }

        return true;
    }

    private function fixError(File $phpcsFile, int $stackPtr, string $expected) : void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_STRING) {
            $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            return;
        }

        if (in_array($tokens[$stackPtr - 1]['code'], [
            T_NEW,
            T_USE,
            T_EXTENDS,
            T_IMPLEMENTS,
            T_INSTANCEOF,
            T_INSTEADOF,
            T_CASE,
            T_PRINT,
            T_ECHO,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_RETURN,
            T_LOGICAL_AND,
            T_LOGICAL_OR,
            T_LOGICAL_XOR,
            T_THROW,
        ], true)) {
            $expected = ' ' . $expected;
        }

        $phpcsFile->fixer->replaceToken($stackPtr, $expected);
        $i = $stackPtr;
        while (isset($tokens[++$i])) {
            if (in_array($tokens[$i]['code'], Tokens::$emptyTokens, true)) {
                continue;
            }

            if (! in_array($tokens[$i]['code'], [T_NS_SEPARATOR, T_STRING], true)) {
                break;
            }

            $phpcsFile->fixer->replaceToken($i, '');
        }
    }

    private function import(string $type, string $fqn, string $alias) : array
    {
        $this->imported[$type][$type === 'const' ? strtoupper($alias) : strtolower($alias)] = [
            'name' => $alias,
            'fqn' => $fqn,
        ];

        return [$fqn => $type];
    }

    /**
     * @param string[][] $references
     */
    private function importReferences(File $phpcsFile, int $namespacePtr, array $references) : void
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$namespacePtr]['scope_opener'])) {
            $ptr = $tokens[$namespacePtr]['scope_opener'];
        } else {
            $ptr = $phpcsFile->findEndOfStatement($namespacePtr);
            $phpcsFile->fixer->addNewline($ptr);
        }

        $content = '';
        foreach ($references as $fqn => $type) {
            $content .= sprintf(
                '%suse %s%s;',
                $phpcsFile->eolChar,
                $type === 'class' ? '' : $type . ' ',
                $fqn
            );
        }

        $phpcsFile->fixer->addContent($ptr, $content);
    }

    private function fixErrors(File $phpcsFile, array $replacements)
    {
        foreach ($replacements as $ptr => $alias) {
            $this->fixError($phpcsFile, $ptr, $alias);
        }
    }
}
