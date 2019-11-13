<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use WebimpressCodingStandard\Helper\MethodsTrait;

use function array_filter;
use function array_merge;
use function count;
use function current;
use function explode;
use function implode;
use function in_array;
use function key;
use function next;
use function preg_grep;
use function preg_split;
use function sprintf;
use function str_replace;
use function strpos;
use function strtolower;
use function strtr;
use function substr;
use function ucfirst;

use const T_CALLABLE;
use const T_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_WHITESPACE;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_STATIC;
use const T_STRING;
use const T_VAR;
use const T_WHITESPACE;

class VariableCommentSniff extends AbstractVariableSniff
{
    use MethodsTrait;

    /**
     * @var string[]
     */
    public $allowedTags = [];

    /**
     * @var string[]
     */
    public $nestedTags = [
        '@var',
    ];

    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) : void
    {
        $this->initScope($phpcsFile, $stackPtr);

        $tokens = $phpcsFile->getTokens();
        $ignore = [
            T_PUBLIC,
            T_PRIVATE,
            T_PROTECTED,
            T_VAR,
            T_STATIC,
            T_WHITESPACE,
            T_STRING,
            T_NS_SEPARATOR,
            T_NULLABLE,
            T_CALLABLE,
        ];

        $propertyInfo = $phpcsFile->getMemberProperties($stackPtr);
        $typeHint = $propertyInfo['type'];

        if ($typeHint !== '') {
            $suggestedType = $this->getSuggestedType($typeHint);

            if ($suggestedType !== $typeHint) {
                $error = 'Invalid type for %s; property expected "%s", but found "%s"';
                $data = [
                    $tokens[$stackPtr]['content'],
                    $suggestedType,
                    $typeHint,
                ];
                $fix = $phpcsFile->addFixableError($error, $propertyInfo['type_token'], 'InvalidType', $data);

                if ($fix) {
                    $this->replaceParamTypeHint(
                        $phpcsFile,
                        $stackPtr,
                        $suggestedType
                    );
                }

                $typeHint = $suggestedType;
            }
        }
        $lowerTypeHint = strtolower($typeHint);

        $commentEnd = $phpcsFile->findPrevious($ignore, $stackPtr - 1, null, true);
        if ($commentEnd === false
            || ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
                && $tokens[$commentEnd]['code'] !== T_COMMENT)
        ) {
            // There is no comment and type is specified
            if ($propertyInfo['type'] !== '') {
                return;
            }

            $error = 'Missing member variable doc comment';
            $phpcsFile->addError($error, $stackPtr, 'Missing');
            return;
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            if ($tokens[$commentEnd]['line'] === $tokens[$stackPtr]['line'] - 1) {
                $error = 'You must use "/**" style comments for a member variable comment';
                $phpcsFile->addError($error, $commentEnd, 'WrongStyle');
            } else {
                $error = 'Missing member variable doc comment';
                $phpcsFile->addError($error, $stackPtr, 'Missing');
            }

            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];

        $foundVar = null;

        $tags = $tokens[$commentStart]['comment_tags'];
        while ($tag = current($tags)) {
            $key = key($tags);
            if (isset($tags[$key + 1])) {
                $lastFrom = $tags[$key + 1];
            } else {
                $lastFrom = $tokens[$commentStart]['comment_closer'];
            }

            $last = $phpcsFile->findPrevious(
                [T_DOC_COMMENT_STAR, T_DOC_COMMENT_WHITESPACE],
                $lastFrom - 1,
                null,
                true
            );

            $tagName = strtolower($tokens[$tag]['content']);
            $isValidTag = array_filter(array_merge($this->allowedTags, ['@var']), static function ($v) use ($tagName) {
                return strtolower($v) === $tagName;
            });

            if (substr($tokens[$last]['content'], -1) === '{') {
                $dep = 1;
                $i = $last;
                $max = $tokens[$commentStart]['comment_closer'];
                while ($dep > 0 && $i < $max) {
                    $i = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $i + 1, $max);

                    if (! $i) {
                        break;
                    }

                    if (strpos($tokens[$i]['content'], '}') === 0) {
                        --$dep;
                    }

                    if (substr($tokens[$i]['content'], -1) === '{') {
                        ++$dep;
                    }
                }

                if ($dep > 0) {
                    $error = 'Tag contains nested description, but cannot find the closing bracket';
                    $phpcsFile->addError($error, $last, 'NotClosed');
                    return;
                }

                while (isset($tags[$key + 1]) && $tags[$key + 1] < $i) {
                    $tagName = strtolower($tokens[$tags[$key + 1]]['content']);
                    if ($isValidTag && ! array_filter($this->nestedTags, static function ($v) use ($tagName) {
                        return strtolower($v) === $tagName;
                    })) {
                        $error = 'Tag %s cannot be nested';
                        $data = [
                            $tokens[$tags[$key + 1]]['content'],
                        ];
                        $phpcsFile->addError($error, $tags[$key + 1], 'NestedTag', $data);
                        return;
                    }

                    next($tags);
                    ++$key;
                }
            }

            $tagName = strtolower($tokens[$tag]['content']);
            if ($tagName === '@var') {
                if ($foundVar !== null) {
                    $error = 'Only one @var tag is allowed in a member variable comment';
                    $phpcsFile->addError($error, $tag, 'DuplicateVar');
                } else {
                    $foundVar = $tag;
                }
            } elseif (! $isValidTag && $tagName[1] === $tokens[$tag]['content'][1]) {
                $error = '%s tag is not allowed in member variable comment';
                $data = [$tokens[$tag]['content']];
                $phpcsFile->addError($error, $tag, 'TagNotAllowed', $data);
            }

            next($tags);
        }

        // The @var tag is the only one we require.
        if ($foundVar === null) {
            $error = 'Missing @var tag in member variable comment';
            $phpcsFile->addError($error, $commentEnd, 'MissingVar');
            return;
        }

        // There is no type hint for the property
        if ($typeHint === ''
            || $tokens[$foundVar + 2]['code'] !== T_DOC_COMMENT_STRING
            || $tokens[$foundVar + 2]['line'] !== $tokens[$foundVar]['line']
        ) {
            return;
        }

        // Property has typehint and there is @var tag.
        $split = preg_split('/\s/', $tokens[$foundVar + 2]['content'], 2);
        $typeStr = $split[0];
        $description = $split[1] ?? null;

        $types = explode('|', $typeStr);
        $propertyName = $tokens[$stackPtr]['content'];

        // Check if null is one of the types
        if ($propertyInfo['nullable_type']
            && ! preg_grep('/^null$/i', $types)
        ) {
            $error = 'Missing type "null" for nullable property %s';
            $data = [$propertyName];
            $fix = $phpcsFile->addFixableError($error, $foundVar + 2, 'PropertyDocMissingNull', $data);

            if ($fix) {
                $content = 'null|' . implode('|', $types);
                if ($description !== null) {
                    $content .= ' ' . $description;
                }
                $phpcsFile->fixer->replaceToken($foundVar + 2, $content);
            }
        }

        // @phpcs:disable WebimpressCodingStandard.Formatting.StringClassReference
        $map = [
            'array' => ['array'],
            'iterable' => ['iterable'],
            'traversable' => ['traversable', '\traversable'],
            'generator' => ['generator', '\generator'],
            'object' => ['object'],
        ];
        // @phpcs:enable

        $count = strpos($lowerTypeHint, '?') === 0 ? 2 : 1;
        $redundantTagCheck = count($types) > $count;

        $break = false;
        foreach ($types as $key => $type) {
            $lower = strtolower($type);

            if ($redundantTagCheck
                && $lower !== 'null'
                && in_array($lower, $map[strtr($lowerTypeHint, ['?' => '', '\\' => ''])] ?? [], true)
            ) {
                $this->redundantType(
                    $phpcsFile,
                    sprintf('Type "%s" is redundant', $type),
                    $foundVar + 2,
                    sprintf('%sRedundant', ucfirst(str_replace('\\', '', $lower))),
                    $lower,
                    $types,
                    $description
                );

                continue;
            }

            if ($lower === 'null'
                && $typeHint
                && ! $propertyInfo['nullable_type']
            ) {
                $error = 'Property %s cannot have "null" value';
                $data = [$propertyName];
                $fix = $phpcsFile->addFixableError($error, $foundVar + 2, 'PropertyDocNull', $data);

                if ($fix) {
                    unset($types[$key]);
                    $content = implode('|', $types);
                    if ($description !== null) {
                        $content .= ' ' . $description;
                    }
                    $phpcsFile->fixer->replaceToken($foundVar + 2, $content);
                }

                $break = true;
                continue;
            }

            if ($typeHint) {
                $simpleTypes = array_merge($this->simpleReturnTypes, ['mixed']);

                // array
                if (in_array($lowerTypeHint, ['array', '?array'], true)
                    && ! in_array($lower, ['null', 'array'], true)
                    && strpos($type, '[]') === false
                ) {
                    $error = 'Property type contains "%s" which is not an array type';
                    $data = [$type];
                    $phpcsFile->addError($error, $foundVar + 2, 'NotArrayType', $data);

                    $break = true;
                    continue;
                }

                // iterable
                if (in_array($lowerTypeHint, ['iterable', '?iterable'], true)
                    && $lower !== 'iterable'
                    && in_array($lower, $simpleTypes, true)
                ) {
                    $error = 'Property type contains "%s" which is not an iterable type';
                    $data = [$type];
                    $phpcsFile->addError($error, $foundVar + 2, 'NotIterableType', $data);

                    $break = true;
                    continue;
                }

                // @phpcs:disable WebimpressCodingStandard.Formatting.StringClassReference
                // traversable
                if (in_array($lowerTypeHint, [
                        'traversable',
                        '?traversable',
                        '\traversable',
                        '?\traversable',
                    ], true)
                    && ! in_array($lower, ['traversable', '\traversable'], true)
                    && in_array($lower, $simpleTypes, true)
                ) {
                    $error = 'Property type contains "%s" which is not a traversable type';
                    $data = [$type];
                    $phpcsFile->addError($error, $foundVar + 2, 'NotTraversableType', $data);

                    $break = true;
                    continue;
                }

                // generator
                if (in_array($lowerTypeHint, [
                        'generator',
                        '?generator',
                        '\generator',
                        '?\generator',
                    ], true)
                    && ! in_array($lower, ['generator', '\generator'], true)
                    && in_array($lower, $simpleTypes, true)
                ) {
                    $error = 'Property type contains %s which is not a generator type';
                    $data = [$type];
                    $phpcsFile->addError($error, $foundVar + 2, 'NotGeneratorType', $data);

                    $break = true;
                    continue;
                }

                // object
                if (in_array($lowerTypeHint, ['object', '?object'], true)
                    && $lower !== 'object'
                    && (in_array($lower, $simpleTypes, true)
                        || strpos($type, '[]') !== false)
                ) {
                    $error = 'Property type contains %s which is not an object type';
                    $data = [$type];
                    $phpcsFile->addError($error, $foundVar + 2, 'NotObjectType', $data);

                    $break = true;
                    continue;
                }

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
                    'object',
                    '?object',
                ];
                // @phpcs:enable

                if (! in_array($lowerTypeHint, $needSpecificationTypes, true)
                    && ((in_array($lowerTypeHint, $simpleTypes, true)
                            && $lower !== 'null'
                            && $lower !== $lowerTypeHint
                            && '?' . $lower !== $lowerTypeHint)
                        || (! in_array($lowerTypeHint, $simpleTypes, true)
                            && array_filter($simpleTypes, static function (string $v) use ($lower) {
                                return $v === $lower || strpos($lower, $v . '[') === 0;
                            })))
                ) {
                    $error = 'Invalid type "%s" for property %s';
                    $data = [
                        $type,
                        $propertyName,
                    ];
                    $phpcsFile->addError($error, $foundVar, 'PropertyDocInvalidType', $data);

                    $break = true;
                    continue;
                }
            }
        }

        // If some parameter is invalid, we don't want to preform other checks
        if ($break) {
            return;
        }

        // Check if PHPDocs param is required
        if ($typeHint && ! $description && $this->typesMatch($typeHint, $typeStr)) {
            $error = 'Redundant @var tag for property %s';
            $data = [$propertyName];
            $fix = $phpcsFile->addFixableError($error, $foundVar, 'RedundantVarDoc', $data);

            if ($fix) {
                $this->removeTag($phpcsFile, $foundVar);
            }
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr) : void
    {
        // Sniff process only class member vars.
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) : void
    {
        // Sniff process only class member vars.
    }

    private function replaceParamTypeHint(File $phpcsFile, int $varPtr, string $newTypeHint) : void
    {
        $last = $phpcsFile->findPrevious([T_CALLABLE, T_STRING], $varPtr - 1);
        $first = $phpcsFile->findPrevious([T_NULLABLE, T_STRING, T_NS_SEPARATOR], $last - 1, null, true);

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->replaceToken($last, $newTypeHint);
        for ($i = $last - 1; $i > $first; --$i) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
        $phpcsFile->fixer->endChangeset();
    }

    private function redundantType(
        File $phpcsFile,
        string $error,
        int $ptr,
        string $code,
        string $redundantType,
        array $types,
        ?string $description
    ) : void {
        $fix = $phpcsFile->addFixableError($error, $ptr, $code);

        if ($fix) {
            foreach ($types as $key => $type) {
                if (strtolower($type) === $redundantType) {
                    unset($types[$key]);
                    break;
                }
            }

            $content = implode('|', $types);
            if ($description !== null) {
                $content .= ' ' . $description;
            }
            $phpcsFile->fixer->replaceToken($ptr, $content);
        }
    }
}
