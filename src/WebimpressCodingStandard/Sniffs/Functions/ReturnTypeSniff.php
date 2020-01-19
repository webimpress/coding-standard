<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Functions;

use Generator;
use Iterator;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use Traversable;
use WebimpressCodingStandard\Helper\MethodsTrait;

use function array_filter;
use function array_intersect;
use function array_merge;
use function array_udiff;
use function array_unique;
use function count;
use function current;
use function explode;
use function implode;
use function in_array;
use function ltrim;
use function preg_grep;
use function preg_replace;
use function preg_split;
use function sprintf;
use function str_replace;
use function strcasecmp;
use function strpos;
use function strtolower;
use function strtr;
use function trim;
use function ucfirst;

use const T_ANON_CLASS;
use const T_ARRAY;
use const T_ARRAY_CAST;
use const T_BOOL_CAST;
use const T_BOOLEAN_AND;
use const T_BOOLEAN_NOT;
use const T_BOOLEAN_OR;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_COALESCE;
use const T_COLON;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_DIR;
use const T_DNUMBER;
use const T_DOC_COMMENT_STRING;
use const T_DOUBLE_CAST;
use const T_DOUBLE_QUOTED_STRING;
use const T_FALSE;
use const T_FILE;
use const T_FN;
use const T_FUNCTION;
use const T_GREATER_THAN;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_INT_CAST;
use const T_IS_EQUAL;
use const T_IS_GREATER_OR_EQUAL;
use const T_IS_IDENTICAL;
use const T_IS_NOT_EQUAL;
use const T_IS_NOT_IDENTICAL;
use const T_IS_SMALLER_OR_EQUAL;
use const T_LESS_THAN;
use const T_LNUMBER;
use const T_LOGICAL_AND;
use const T_LOGICAL_OR;
use const T_LOGICAL_XOR;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_NULL;
use const T_OBJECT_CAST;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_RETURN;
use const T_SELF;
use const T_SEMICOLON;
use const T_SPACESHIP;
use const T_STATIC;
use const T_STRING;
use const T_STRING_CAST;
use const T_TRUE;
use const T_VARIABLE;
use const T_YIELD;
use const T_YIELD_FROM;

class ReturnTypeSniff implements Sniff
{
    use MethodsTrait;

    /**
     * @var string
     */
    private $returnDoc;

    /**
     * @var array
     */
    private $returnDocTypes = [];

    /**
     * @var string
     */
    private $returnDocValue;

    /**
     * @var null|string
     */
    private $returnDocDescription;

    /**
     * @var bool
     */
    private $returnDocIsValid = true;

    /**
     * @var string
     */
    private $returnType;

    /**
     * @var string
     */
    private $returnTypeValue;

    /**
     * @var bool
     */
    private $returnTypeIsValid = true;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->initScope($phpcsFile, $stackPtr);

        $this->returnDoc = null;
        $this->returnDocTypes = [];
        $this->returnDocValue = null;
        $this->returnDocDescription = null;
        $this->returnDocIsValid = true;

        $this->returnType = null;
        $this->returnTypeValue = null;
        $this->returnTypeIsValid = true;

        if ($commentStart = $this->getCommentStart($phpcsFile, $stackPtr)) {
            $this->processReturnDoc($phpcsFile, $commentStart);
        }
        $this->processReturnType($phpcsFile, $stackPtr);
        $this->processReturnStatements($phpcsFile, $stackPtr);
    }

    private function getReturnType(File $phpcsFile, int $stackPtr) : ?int
    {
        $tokens = $phpcsFile->getTokens();

        $eol = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $stackPtr + 1);
        $last = $phpcsFile->findPrevious(Tokens::$emptyTokens, $eol - 1, null, true);

        if ($tokens[$last]['code'] === T_CLOSE_PARENTHESIS) {
            return null;
        }

        return $last;
    }

    private function processReturnDoc(File $phpcsFile, int $commentStart) : void
    {
        $tokens = $phpcsFile->getTokens();

        $returnDoc = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if (strtolower($tokens[$tag]['content']) !== '@return') {
                continue;
            }

            if ($returnDoc !== null) {
                $error = 'Only 1 @return tag is allowed in a function comment';
                $phpcsFile->addError($error, $tag, 'DuplicateReturn');

                $this->returnDoc = $returnDoc;
                $this->returnDocIsValid = false;
                return;
            }

            if ($this->isSpecialMethod) {
                $error = sprintf('@return tag is not allowed for "%s" method', $this->methodName);
                $phpcsFile->addError($error, $tag, 'SpecialMethodReturnTag');
            }

            $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag + 1);
            if ($string !== $tag + 2
                || $tokens[$string]['line'] !== $tokens[$tag]['line']
            ) {
                $this->returnDoc = $tag;
                $this->returnDocIsValid = false;
                return;
            }

            $returnDoc = $tag;
        }

        if (! $returnDoc || $this->isSpecialMethod) {
            return;
        }

        $this->returnDoc = $returnDoc;

        $split = preg_split('/\s/', $tokens[$returnDoc + 2]['content'], 2);
        $this->returnDocValue = $split[0];
        $this->returnDocDescription = isset($split[1]) ? trim($split[1]) : null;

        if (strtolower($this->returnDocValue) === 'void') {
            $this->returnDocIsValid = false;
            return;
        }

        if (! $this->isType('@return', $this->returnDocValue)) {
            $this->returnDocIsValid = false;
            return;
        }

        // Return tag contains only null, null[], null[][], ...
        $cleared = strtolower(strtr($this->returnDocValue, ['[' => '', ']' => '']));
        if ($cleared === 'null') {
            $this->returnDocIsValid = false;
            return;
        }

        $this->returnDocTypes = explode('|', $this->returnDocValue);
    }

    private function processReturnType(File $phpcsFile, int $stackPtr) : void
    {
        // Get return type from method signature
        $returnType = $this->getReturnType($phpcsFile, $stackPtr);
        if (! $returnType) {
            return;
        }

        $this->returnType = $returnType;

        if ($this->isSpecialMethod) {
            $error = 'Method "%s" cannot declare return type';
            $data = [$this->methodName];
            $phpcsFile->addError($error, $stackPtr, 'SpecialMethodReturnType', $data);

            $this->returnTypeIsValid = false;
            return;
        }

        $colon = $phpcsFile->findPrevious(T_COLON, $returnType - 1, $stackPtr + 1);
        $firstNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $colon + 1, null, true);

        $this->returnTypeValue = preg_replace(
            '/\s/',
            '',
            $phpcsFile->getTokensAsString($firstNonEmpty, $returnType - $firstNonEmpty + 1)
        );
        $lowerReturnTypeValue = strtolower($this->returnTypeValue);

        $suggestedType = $this->getSuggestedType($this->returnTypeValue);
        if ($suggestedType !== $this->returnTypeValue) {
            $error = 'Invalid return type; expected %s, but found %s';
            $data = [
                $suggestedType,
                $this->returnTypeValue,
            ];
            $fix = $phpcsFile->addFixableError($error, $returnType, 'InvalidReturnType', $data);

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $firstNonEmpty; $i < $returnType; ++$i) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
                $phpcsFile->fixer->replaceToken($returnType, $suggestedType);
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        if (! $this->returnDoc || ! $this->returnDocIsValid) {
            return;
        }

        $hasNullInDoc = preg_grep('/^null$/i', $this->returnDocTypes);

        if (! $hasNullInDoc && strpos($this->returnTypeValue, '?') === 0) {
            $error = 'Missing "null" as possible return type in PHPDocs.'
                . ' Nullable type has been found in return type declaration';
            $fix = $phpcsFile->addFixableError($error, $this->returnDoc + 2, 'MissingNull');

            if ($fix) {
                $content = trim('null|' . $this->returnDocValue . ' ' . $this->returnDocDescription);
                $phpcsFile->fixer->replaceToken($this->returnDoc + 2, $content);
            }

            return;
        }

        if ($hasNullInDoc && strpos($this->returnTypeValue, '?') !== 0) {
            $error = 'Null type has been found in PHPDocs for return type.'
                . ' It is not declared with function return type';

            $this->redundantType($phpcsFile, $error, $this->returnDoc + 2, 'AdditionalNull', 'null');
            return;
        }

        // @phpcs:disable WebimpressCodingStandard.Formatting.StringClassReference
        $needSpecificationTypes = [
            'array',
            '?array',
            'iterable',
            '?iterable',
            'traversable',
            '?traversable',
            '\traversable',
            '?\traversable',
            'generator',
            '?generator',
            '\generator',
            '?\generator',
            'iterator',
            '?iterator',
            '\iterator',
            '?\iterator',
            'object',
            '?object',
        ];

        if ($this->typesMatch($this->returnTypeValue, $this->returnDocValue)) {
            // There is no description and values are the same so PHPDoc tag is redundant.
            if (! $this->returnDocDescription) {
                $error = 'Return tag is redundant';
                $fix = $phpcsFile->addFixableError($error, $this->returnDoc, 'RedundantReturnDoc');

                if ($fix) {
                    $this->removeTag($phpcsFile, $this->returnDoc);
                }
            }

            return;
        }

        if (! in_array($lowerReturnTypeValue, $needSpecificationTypes, true)) {
            if (in_array($lowerReturnTypeValue, ['parent', '?parent'], true)) {
                if (! in_array(strtolower($this->returnDocValue), [
                    'parent',
                    'null|parent',
                    'parent|null',
                    'self',
                    'null|self',
                    'self|null',
                    'static',
                    'null|static',
                    'static|null',
                    '$this',
                    'null|$this',
                    '$this|null',
                ], true)) {
                    $error = 'Return type is "parent" so return tag must be one of:'
                        . ' "parent", "self", "static" or "$this"';
                    $phpcsFile->addError($error, $this->returnDoc + 2, 'ReturnParent');
                }

                return;
            }

            if (in_array($lowerReturnTypeValue, ['self', '?self'], true)) {
                if (! in_array(strtolower($this->returnDocValue), [
                    'self',
                    'null|self',
                    'self|null',
                    'static',
                    'null|static',
                    'static|null',
                    '$this',
                    'null|$this',
                    '$this|null',
                ], true)) {
                    $error = 'Return type is "self" so return tag must be one of: "self", "static" or "$this"';
                    $phpcsFile->addError($error, $this->returnDoc + 2, 'ReturnSelf');
                }

                return;
            }

            if (! in_array($lowerReturnTypeValue, $this->simpleReturnTypes, true)) {
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if (array_filter($this->simpleReturnTypes, static function (string $v) use ($lower) {
                        return $v === $lower || strpos($lower, $v . '[') === 0;
                    })) {
                        $error = 'Unexpected type "%s" found in return tag';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'ReturnComplexType', $data);
                    }
                }

                return;
            }

            $error = 'Return type in PHPDoc tag is different than declared type in method declaration: "%s" and "%s"';
            $data = [
                $this->returnDocValue,
                $this->returnTypeValue,
            ];
            $phpcsFile->addError($error, $this->returnDoc + 2, 'DifferentTagAndDeclaration', $data);

            return;
        }

        $map = [
            'array' => ['array'],
            'iterable' => ['iterable'],
            'traversable' => ['traversable', '\traversable'],
            'generator' => ['generator', '\generator'],
            'iterator' => ['iterator', '\iterator'],
            'object' => ['object'],
        ];

        $count = strpos($lowerReturnTypeValue, '?') === 0 ? 2 : 1;

        if (! $this->returnDocDescription || count($this->returnDocTypes) > $count) {
            foreach ($this->returnDocTypes as $type) {
                $lower = strtolower($type);

                if ($lower === 'null') {
                    continue;
                }

                if (! in_array($lower, $map[strtr($lowerReturnTypeValue, ['?' => '', '\\' => ''])], true)) {
                    continue;
                }

                $this->redundantType(
                    $phpcsFile,
                    sprintf('Type "%s" is redundant', $type),
                    $this->returnDoc + 2,
                    sprintf('%sRedundant', ucfirst(str_replace('\\', '', $lower))),
                    $lower
                );
            }
        }

        $simpleTypes = array_merge($this->simpleReturnTypes, ['mixed']);

        switch ($lowerReturnTypeValue) {
            case 'array':
            case '?array':
                foreach ($this->returnDocTypes as $type) {
                    if (in_array(strtolower($type), ['null', 'array'], true)) {
                        continue;
                    }

                    if (strpos($type, '[]') === false) {
                        $error = 'Return type contains "%s" which is not an array type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotArrayType', $data);
                    }
                }
                break;

            case 'iterable':
            case '?iterable':
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if ($lower === 'iterable') {
                        continue;
                    }

                    if (in_array($lower, $simpleTypes, true)) {
                        $error = 'Return type contains "%s" which is not an iterable type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotIterableType', $data);
                    }
                }
                break;

            case 'iterator':
            case '?iterator':
            case '\iterator':
            case '?\iterator':
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if (in_array($lower, ['iterator', '\iterator'], true)) {
                        continue;
                    }

                    if (in_array($lower, $simpleTypes, true)) {
                        $error = 'Return type contains "%s" which is not an Iterator type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotIteratorType', $data);
                    }
                }
                break;

            case 'traversable':
            case '?traversable':
            case '\traversable':
            case '?\traversable':
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if (in_array($lower, ['traversable', '\traversable'], true)) {
                        continue;
                    }

                    if (in_array($lower, $simpleTypes, true)) {
                        $error = 'Return type contains "%s" which is not a traversable type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotTraversableType', $data);
                    }
                }
                break;

            case 'generator':
            case '?generator':
            case '\generator':
            case '?\generator':
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if (in_array($lower, ['generator', '\generator'], true)) {
                        continue;
                    }

                    if (in_array($lower, $simpleTypes, true)) {
                        $error = 'Return type contains "%s" which is not a generator type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotGeneratorType', $data);
                    }
                }
                break;

            case 'object':
            case '?object':
                foreach ($this->returnDocTypes as $type) {
                    $lower = strtolower($type);
                    if ($lower === 'object') {
                        continue;
                    }

                    if (in_array($lower, $simpleTypes, true)
                        || strpos($lower, '[]') !== false
                    ) {
                        $error = 'Return type contains "%s" which is not an object type';
                        $data = [
                            $type,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'NotObjectType', $data);
                    }
                }
                break;
        }
        // @phpcs:enable
    }

    private function redundantType(File $phpcsFile, string $error, int $ptr, string $code, string $redundantType) : void
    {
        $fix = $phpcsFile->addFixableError($error, $ptr, $code);

        if ($fix) {
            foreach ($this->returnDocTypes as $key => $type) {
                if (strtolower($type) === $redundantType) {
                    unset($this->returnDocTypes[$key]);
                    break;
                }
            }

            $content = trim(implode('|', $this->returnDocTypes) . ' ' . $this->returnDocDescription);
            $phpcsFile->fixer->replaceToken($ptr, $content);
        }
    }

    private function processReturnStatements(File $phpcsFile, int $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        // Method does not have a body.
        if (! isset($tokens[$stackPtr]['scope_opener'])) {
            return;
        }

        $returnValues = [];

        // Search all return/yield/yield from in the method.
        for ($i = $tokens[$stackPtr]['scope_opener'] + 1; $i < $tokens[$stackPtr]['scope_closer']; ++$i) {
            // Skip closures and anonymous classes.
            if ($tokens[$i]['code'] === T_CLOSURE
                || $tokens[$i]['code'] === T_FN
                || $tokens[$i]['code'] === T_ANON_CLASS
            ) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            if ($tokens[$i]['code'] !== T_RETURN
                && $tokens[$i]['code'] !== T_YIELD
                && $tokens[$i]['code'] !== T_YIELD_FROM
            ) {
                continue;
            }

            $isYield = $tokens[$i]['code'] !== T_RETURN;
            if ($isYield && $this->returnType && $this->returnTypeIsValid) {
                $type = strtolower(ltrim($this->returnTypeValue, '?'));

                if ($type !== 'iterable'
                    && ((! isset($this->importedClasses[$type]['fqn'])
                            && ! in_array(ltrim($type, '\\'), [
                                'generator',
                                'iterator',
                                'traversable',
                            ], true))
                        || (isset($this->importedClasses[$type]['fqn'])
                            && ! in_array($this->importedClasses[$type]['fqn'], [
                                Generator::class,
                                Iterator::class,
                                Traversable::class,
                            ], true)))
                ) {
                    $phpcsFile->addError(
                        sprintf(
                            'Generators may only declare a return type of Generator, Iterator, Traversable,'
                            . ' or iterable, %s is not permitted',
                            $this->returnTypeValue
                        ),
                        $this->returnType,
                        'InvalidGeneratorType'
                    );

                    return;
                }
            }

            $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i + 1, null, true);
            if ($tokens[$next]['code'] === T_SEMICOLON) {
                $this->returnCodeVoid($phpcsFile, $i);
            } else {
                $this->returnCodeValue($phpcsFile, $i);

                if ($isYield) {
                    return;
                }

                $returnValues[$next] = $this->getReturnValue($phpcsFile, $next);

                if ($this->returnDoc
                    && $this->returnDocIsValid
                    && in_array(strtolower($this->returnDocValue), ['$this', 'null|$this', '$this|null'], true)
                ) {
                    $isThis = ! in_array($tokens[$next]['code'], [
                        T_CLOSURE,
                        T_CONSTANT_ENCAPSED_STRING,
                        T_DIR,
                        T_DOUBLE_QUOTED_STRING,
                        T_FILE,
                        T_NEW,
                        T_NS_SEPARATOR,
                        T_OPEN_PARENTHESIS,
                        T_OBJECT_CAST,
                        T_SELF,
                        T_STATIC,
                        T_STRING_CAST,
                    ], true);

                    if ($isThis
                        && $tokens[$next]['code'] === T_VARIABLE
                        && strtolower($tokens[$next]['content']) !== '$this'
                    ) {
                        $isThis = false;
                    }

                    if (! $isThis) {
                        $error = 'Return type of "%s" function is "$this",'
                            . ' but function is returning not $this here';
                        $data = [$this->methodName];
                        $phpcsFile->addError($error, $i, 'InvalidReturnNotThis', $data);
                    }
                }
            }
        }

        if (! $returnValues
            && (($this->returnDoc && $this->returnDocIsValid)
                || ($this->returnType && $this->returnTypeIsValid && strtolower($this->returnTypeValue) !== 'void'))
        ) {
            $error = 'Return type of "%s" function is not void, but function has no return statement';
            $data = [$this->methodName];
            $phpcsFile->addError(
                $error,
                $this->returnDoc && $this->returnDocIsValid ? $this->returnDoc : $this->returnType,
                'InvalidNoReturn',
                $data
            );
        }

        if (! $returnValues || ! $this->returnDoc || ! $this->returnDocIsValid) {
            return;
        }

        $uniq = array_unique($returnValues);
        if (count($uniq) === 1) {
            // We have to use current because index in the array is $ptr
            switch (current($uniq)) {
                case 'array':
                    if ($matches = array_udiff(
                        preg_grep('/[^\]]$/', $this->returnDocTypes),
                        ['null', 'array', 'iterable'],
                        static function (string $a, string $b) {
                            return strcasecmp($a, $b);
                        }
                    )) {
                        $error = 'Function returns only array, but return type contains not array types: %s';
                        $data = [
                            implode(', ', $matches),
                        ];
                        $phpcsFile->addError($error, $this->returnDoc, 'ReturnArrayOnly', $data);
                    }
                    break;

                case 'bool':
                    if (! in_array(strtolower($this->returnDocValue), ['bool', 'boolean'], true)) {
                        $error = 'Functions returns only boolean value, but return type is not only bool';
                        $phpcsFile->addError($error, $this->returnDoc, 'ReturnBoolOnly');
                    }
                    break;

                case 'false':
                    if (strtolower($this->returnDocValue) !== 'false') {
                        $error = 'Function returns only boolean false, but return type is not only false';
                        $phpcsFile->addError($error, $this->returnDoc, 'ReturnFalseOnly');
                    }
                    break;

                case 'true':
                    if (strtolower($this->returnDocValue) !== 'true') {
                        $error = 'Function returns only boolean true, but return type is not only true';
                        $phpcsFile->addError($error, $this->returnDoc, 'ReturnTrueOnly');
                    }
                    break;

                case 'new':
                    $instances = [];
                    foreach ($returnValues as $ptr => $new) {
                        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $ptr + 1, null, true);
                        if ($tokens[$next]['code'] !== T_STRING
                            && $tokens[$next]['code'] !== T_NS_SEPARATOR
                        ) {
                            // It is unknown instance, break switch.
                            break 2;
                        }

                        $after = $phpcsFile->findNext(
                            Tokens::$emptyTokens + [T_NS_SEPARATOR => T_NS_SEPARATOR, T_STRING => T_STRING],
                            $next + 1,
                            null,
                            true
                        );

                        $last = $phpcsFile->findPrevious(T_STRING, $after - 1, $next);
                        $content = $this->getSuggestedType(
                            $phpcsFile->getTokensAsString($next, $last - $next + 1)
                        );

                        $instances[strtolower($content)] = $content;
                    }

                    // If function returns instances of different types, break.
                    if (count($instances) !== 1) {
                        break;
                    }

                    $className = current($instances);
                    if ($this->returnDocValue !== $className) {
                        $error = 'Function returns only new instance of %s, but return type is not only %s';
                        $data = [
                            $className,
                            $className,
                        ];
                        $phpcsFile->addError($error, $this->returnDoc + 2, 'ReturnNewInstanceOnly', $data);
                    }
                    break;

                case '$this':
                    if (($isClassName = $this->isClassName($this->returnDocValue))
                        || strtolower($this->returnDocValue) === 'self'
                    ) {
                        $error = 'Function returns only $this so return type should be $this instead of '
                            . ($isClassName ? 'class name' : 'self');
                        $fix = $phpcsFile->addFixableError($error, $this->returnDoc + 2, 'ReturnThisOnly');

                        if ($fix) {
                            $content = trim('$this ' . $this->returnDocDescription);
                            $phpcsFile->fixer->replaceToken($this->returnDoc + 2, $content);
                        }
                    }
                    break;
            }
        }
    }

    private function endOfExpression(File $phpcsFile, int $ptr) : ?int
    {
        $tokens = $phpcsFile->getTokens();

        do {
            if ($tokens[$ptr]['code'] === T_OPEN_PARENTHESIS
                || $tokens[$ptr]['code'] === T_ARRAY
            ) {
                $ptr = $tokens[$ptr]['parenthesis_closer'];
            } elseif ($tokens[$ptr]['code'] === T_OPEN_SQUARE_BRACKET
                || $tokens[$ptr]['code'] === T_OPEN_CURLY_BRACKET
                || $tokens[$ptr]['code'] === T_OPEN_SHORT_ARRAY
            ) {
                $ptr = $tokens[$ptr]['bracket_closer'];
            } elseif (in_array(
                $tokens[$ptr]['code'],
                Tokens::$booleanOperators + Tokens::$comparisonTokens
                + [
                    T_SEMICOLON,
                    T_CLOSE_PARENTHESIS,
                    T_INLINE_ELSE,
                    T_INLINE_THEN,
                ],
                true
            )) {
                return $ptr;
            }
        } while (++$ptr);

        return null;
    }

    private function getReturnValue(File $phpcsFile, int $ptr) : string
    {
        $tokens = $phpcsFile->getTokens();

        $boolExpressionTokens = [
            // comparision tokens
            T_IS_EQUAL,
            T_IS_IDENTICAL,
            T_IS_NOT_EQUAL,
            T_IS_NOT_IDENTICAL,
            T_LESS_THAN,
            T_GREATER_THAN,
            T_IS_SMALLER_OR_EQUAL,
            T_IS_GREATER_OR_EQUAL,
            // boolean operators
            T_BOOLEAN_AND,
            T_BOOLEAN_OR,
            T_LOGICAL_AND,
            T_LOGICAL_OR,
            T_LOGICAL_XOR,
        ];

        $endOfExpression = $this->endOfExpression($phpcsFile, $ptr);
        $isBool = in_array($tokens[$endOfExpression]['code'], $boolExpressionTokens, true);

        while ($endOfExpression && in_array($tokens[$endOfExpression]['code'], $boolExpressionTokens, true)) {
            $endOfExpression = $this->endOfExpression($phpcsFile, $endOfExpression + 1);
        }

        if ($endOfExpression
            && ($tokens[$endOfExpression]['code'] === T_INLINE_THEN
                || $tokens[$endOfExpression]['code'] === T_COALESCE
                || $tokens[$endOfExpression]['code'] === T_SPACESHIP)
        ) {
            return 'unknown';
        }

        if ($isBool) {
            if (! $this->hasCorrectType(['bool', '?bool'], ['bool', 'boolean'])) {
                $error = 'Function return type is not bool, but function returns boolean value here';
                $phpcsFile->addError($error, $ptr, 'ReturnBool');
            }

            return 'bool';
        }

        switch ($tokens[$ptr]['code']) {
            case T_ARRAY:
            case T_ARRAY_CAST:
            case T_OPEN_SHORT_ARRAY:
                if (! $this->hasCorrectType(['array', '?array', 'iterable', '?iterable'], [])
                    || ($this->returnDoc
                        && $this->returnDocIsValid
                        && strpos($this->returnDocValue, '[]') === false
                        && ! array_intersect(
                            explode('|', strtolower($this->returnDocValue)),
                            ['array', 'iterable']
                        ))
                ) {
                    $error = 'Function return type is array nor iterable, but function returns array here';
                    $phpcsFile->addError($error, $ptr, 'ReturnArray');
                }
                return 'array';

            case T_BOOL_CAST:
            case T_BOOLEAN_NOT:
                if (! $this->hasCorrectType(['bool', '?bool'], ['bool', 'boolean', 'mixed'])) {
                    $error = 'Function return type is not bool, but function returns boolean value here';
                    $phpcsFile->addError($error, $ptr, 'ReturnBool');
                }
                return 'bool';

            case T_FALSE:
                if (! $this->hasCorrectType(['bool', '?bool'], ['bool', 'boolean', 'false', 'mixed'])) {
                    $error = 'Function return type is not bool, but function returns boolean false here';
                    $phpcsFile->addError($error, $ptr, 'ReturnFalse');
                }
                return 'false';

            case T_TRUE:
                if (! $this->hasCorrectType(['bool', '?bool'], ['bool', 'boolean', 'true', 'mixed'])) {
                    $error = 'Function return type is not bool, but function returns boolean true here';
                    $phpcsFile->addError($error, $ptr, 'ReturnTrue');
                }
                return 'true';

            // integer value or integer cast
            case T_LNUMBER:
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $ptr + 1, null, true);
                if ($tokens[$next]['code'] !== T_SEMICOLON) {
                    return 'unknown';
                }
                // no break
            case T_INT_CAST:
                if (! $this->hasCorrectType(['int', '?int'], ['int', 'integer', 'mixed'])) {
                    $error = 'Function return type is not int, but function return int here';
                    $phpcsFile->addError($error, $ptr, 'ReturnInt');
                }
                return 'int';

            // float value or float cast
            case T_DNUMBER:
            case T_DOUBLE_CAST:
                if (! $this->hasCorrectType(['float', '?float'], ['double', 'float', 'real', 'mixed'])) {
                    $error = 'Function return type is not float, but function returns float here';
                    $phpcsFile->addError($error, $ptr, 'ReturnFloat');
                }
                return 'float';

            case T_NEW:
                return 'new';

            case T_NULL:
                if (! $this->hasCorrectType([], ['null'])
                    || ($this->returnType
                        && $this->returnTypeIsValid
                        && strpos($this->returnTypeValue, '?') !== 0)
                ) {
                    $error = 'Function return type is not nullable, but function returns null here';
                    $phpcsFile->addError($error, $ptr, 'ReturnNull');
                }
                return 'null';

            case T_VARIABLE:
                if (strtolower($tokens[$ptr]['content']) !== '$this') {
                    return 'variable';
                }

                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $ptr + 1, null, true);
                if ($tokens[$next]['code'] !== T_SEMICOLON) {
                    // This is not "$this" return but something else.
                    return 'unknown';
                }
                return '$this';
        }

        return 'unknown';
    }

    /**
     * @param string[] $expectedType
     * @param string[] $expectedDoc
     */
    private function hasCorrectType(array $expectedType, array $expectedDoc) : bool
    {
        if ($expectedType
            && $this->returnType
            && $this->returnTypeIsValid
            && ! in_array(strtolower($this->returnTypeValue), $expectedType, true)
        ) {
            return false;
        }

        if ($expectedDoc
            && $this->returnDoc
            && $this->returnDocIsValid
            && ! array_filter($this->returnDocTypes, static function (string $v) use ($expectedDoc) {
                return in_array(strtolower($v), $expectedDoc, true);
            })
        ) {
            return false;
        }

        return true;
    }

    private function returnCodeVoid(File $phpcsFile, int $ptr) : void
    {
        if (($this->returnDoc && $this->returnDocIsValid)
            || ($this->returnType && $this->returnTypeIsValid && strtolower($this->returnTypeValue) !== 'void')
        ) {
            $error = 'Return type of "%s" function is not void, but function is returning void here';
            $data = [$this->methodName];
            $phpcsFile->addError($error, $ptr, 'InvalidReturnNotVoid', $data);
        }
    }

    private function returnCodeValue(File $phpcsFile, int $ptr) : void
    {
        // Special method cannot return any values.
        if ($this->isSpecialMethod) {
            $error = 'Method "%s" cannot return any value, but returns it here';
            $data = [$this->methodName];
            $phpcsFile->addError($error, $ptr, 'SpecialMethodReturnValue', $data);

            return;
        }

        // Function is void but return a value.
        if ((! $this->returnType
                || ! $this->returnTypeIsValid
                || $this->returnTypeValue === 'void')
            && (! $this->returnDoc
                || ! $this->returnDocIsValid
                || $this->returnDocValue === 'void')
        ) {
            $error = 'Function "%s" returns value but it is not specified.'
                . ' Please add return tag or declare return type';
            $data = [
                $this->methodName,
            ];
            $phpcsFile->addError($error, $ptr, 'ReturnValue', $data);
        }
    }
}
