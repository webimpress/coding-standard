<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Helper;

use PHP_CodeSniffer\Files\File;

use function array_pop;
use function end;
use function in_array;
use function preg_match;
use function preg_match_all;
use function substr;
use function trim;

use const T_ABSTRACT;
use const T_CALLABLE;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_FINAL;
use const T_NS_SEPARATOR;
use const T_NULLABLE;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_STATIC;
use const T_STRING;
use const T_VAR;
use const T_WHITESPACE;

/**
 * @internal
 */
trait AnnotationsTrait
{
    /**
     * Annotation regular expression.
     * The same pattern is used (with case-insensitive modifier) to match PHPDoc tags.
     *
     * @var string
     */
    public $annotationRegexp = '/@([A-Z][a-zA-Z0-9]*)\b/';

    /**
     * Allowed annotations in PHPDocs.
     * When it is set to:
     *     - `null` - any annotation is allowed,
     *     - `[]` (empty array) - all annotations are not allowed,
     *     - `['ORM']` (specified values) - only specified annotations are allowed.
     *
     * @var null|string[]
     */
    public $allowedAnnotations = [];

    private function processAnnotations(File $phpcsFile, int $stackPtr) : void
    {
        $ignored = [
            T_PUBLIC,
            T_PROTECTED,
            T_PRIVATE,
            T_STATIC,
            T_ABSTRACT,
            T_FINAL,
            T_WHITESPACE,
            T_VAR,
            T_STRING,
            T_NS_SEPARATOR,
            T_NULLABLE,
            T_CALLABLE,
        ];

        $tokens = $phpcsFile->getTokens();
        $endComment = $phpcsFile->findPrevious($ignored, $stackPtr - 1, null, true);

        if ($tokens[$endComment]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            return;
        }

        $opener = $tokens[$endComment]['comment_opener'];

        if (empty($tokens[$opener]['comment_tags'])) {
            return;
        }

        $last = 0;
        foreach ($tokens[$opener]['comment_tags'] as $tag) {
            if ($last > $tag) {
                continue;
            }

            $last = $this->processTag($phpcsFile, $tag, $endComment);
        }
    }

    private function processTag(File $phpcsFile, int $tag, int $endComment) : int
    {
        $tokens = $phpcsFile->getTokens();

        $brackets = [];

        // Check if the opening tag is allowed annotation. Only if it is, we check all
        // nested annotations.
        $isAnnotation = $this->isAnnotation($tokens[$tag]['content'], $annotation);
        $isAllowedAnnotation = $isAnnotation && $this->isAllowedAnnotation($annotation[1]);

        $token = $tag;
        $expectedTokens = [T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG];
        do {
            $last = end($brackets);
            $first = $tokens[$token]['content'][0];
            if (($first === '}' && $last === '{')
                || ($first === ')' && $last === '(')
            ) {
                array_pop($brackets);
            }

            if (($isAllowedAnnotation || ($isAnnotation && $token === $tag))
                && $this->isTag($tokens[$token]['content'], $annotation)
            ) {
                foreach ($annotation[1] as $matchedTag) {
                    if (! $this->isAllowedAnnotation($matchedTag)) {
                        $error = 'Annotation %s is disallowed';
                        $data = ['@' . $matchedTag];
                        $phpcsFile->addError($error, $token, 'Disallowed', $data);
                    }

                    // Skip after first tag, if it is the opening tag
                    if ($isAnnotation && ! $isAllowedAnnotation && $token === $tag) {
                        break;
                    }
                }
            }

            $last = substr(trim($tokens[$token]['content']), -1);
            if (in_array($last, ['{', '('], true)) {
                $brackets[] = $last;
            }

            if (! $brackets) {
                break;
            }
        } while ($token = $phpcsFile->findNext($expectedTokens, $token + 1, $endComment));

        return $token + 1;
    }

    /**
     * @param null|string[] $tag
     * @return false|int
     */
    private function isTag(string $content, ?array &$tag = null)
    {
        return preg_match_all($this->annotationRegexp . 'i', $content, $tag);
    }

    /**
     * @param null|string[] $annotation
     * @return false|int
     */
    private function isAnnotation(string $content, ?array &$annotation = null)
    {
        return preg_match($this->annotationRegexp, $content, $annotation);
    }

    private function isAllowedAnnotation(string $annotation) : bool
    {
        return $this->allowedAnnotations === null
            || in_array($annotation, $this->allowedAnnotations, true);
    }
}
