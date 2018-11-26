<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function array_pop;
use function end;
use function in_array;
use function preg_match;
use function substr;
use function trim;

use const T_CLASS;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_INTERFACE;
use const T_TRAIT;
use const T_WHITESPACE;

class ClassAnnotationSniff implements Sniff
{
    /**
     * Allowed annotations in class doc-block.
     * When it is set to:
     *     - `null` - any annotation is allowed,
     *     - `[]` (empty array) - all annotations are not allowed,
     *     - `['ORM']` (specified values) - only specified annotations are allowed.
     *
     * @var null|string[]
     */
    public $allowedAnnotations = [];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
        ];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $endComment = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);

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

            if (preg_match('/@([A-Z][a-zA-Z0-9]*)\b/', $tokens[$token]['content'], $annotation)
                && ! $this->isAllowedAnnotation($annotation[1])
            ) {
                $error = 'Annotation %s is disallowed ' . $tag . ':' . $token;
                $data = ['@' . $annotation[1]];
                $phpcsFile->addError($error, $token, 'Disallowed', $data);
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

    private function isAllowedAnnotation(string $annotation) : bool
    {
        return $this->allowedAnnotations === null
            || in_array($annotation, $this->allowedAnnotations, true);
    }
}
