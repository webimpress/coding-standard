<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Common;

use function preg_match;
use function preg_replace;
use function strpos;
use function trim;

use const T_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_WHITESPACE;
use const T_WHITESPACE;

class DisallowEmptyCommentSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_DOC_COMMENT_OPEN_TAG,
            T_COMMENT,
        ];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr) : int
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_OPEN_TAG) {
            $next = $phpcsFile->findNext([T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], $stackPtr + 1, null, true);

            if ($tokens[$next]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                $error = 'Empty doc-block comment';

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'DocBlock');
                if ($fix) {
                    $after = $phpcsFile->findNext(T_WHITESPACE, $next + 1, null, true);
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $stackPtr; $i < $after; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            }

            return $stackPtr + 1;
        }

        $line = $tokens[$stackPtr]['line'];
        $comment = trim($tokens[$stackPtr]['content']);
        if (strpos($comment, '/*') === 0) {
            $i = $stackPtr;
            $content = '';
            do {
                $content .= $tokens[$i]['content'];
            } while ($tokens[++$i]['code'] === T_COMMENT);

            $end = $i;

            if (preg_replace('#[\s\*/]#', '', $content) === '') {
                $fix = $phpcsFile->addFixableError('Empty comment', $stackPtr, 'Multiline');
                if ($fix) {
                    $next = $phpcsFile->findNext(T_WHITESPACE, $i, null, true) ?: $phpcsFile->numTokens;
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $stackPtr; $i < $next; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                }

                return $end;
            }

            $newContent = preg_replace('#(^/\*\s*?$)(?:\n^[\s\*]*$)+#m', '\\1', $content);
            $newContent = preg_replace('#(?:^[\s\*]*$\n)+(\s*?\*/)#m', '\\1', $newContent);
            $newContent = preg_replace('#(^[\s\*]*$\n){2,}#m', '\\1', $newContent);

            if ($newContent !== $content) {
                $error = 'Redundant empty lines in comment; found: %s but expected %s';
                $data = [
                    Common::prepareForOutput($content),
                    Common::prepareForOutput($newContent),
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Lines', $data);
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    while (--$i >= $stackPtr) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->addContent($stackPtr, $newContent);
                    $phpcsFile->fixer->endChangeset();
                }
            }

            return $end;
        }

        if ($this->isEmptyComment($comment)) {
            $before = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
            $after = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

            $hasBefore = false;
            $hasAfter = false;

            if ($tokens[$before]['code'] === T_COMMENT
                && $tokens[$before]['line'] === $line - 1
                && ! $this->isEmptyComment($tokens[$before]['content'])
            ) {
                $hasBefore = true;
            }

            if ($tokens[$after]['code'] === T_COMMENT
                && $tokens[$after]['line'] === $line + 1
                && ! $this->isEmptyComment($tokens[$after]['content'])
            ) {
                $hasAfter = true;
            }

            if (! $hasBefore || ! $hasAfter) {
                $fix = $phpcsFile->addFixableError('Empty inline comment ' . $comment, $stackPtr, 'Inline');
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($stackPtr, '');
                }
            }
        }

        return $stackPtr + 1;
    }

    private function isEmptyComment(string $comment) : bool
    {
        return preg_match('@^(//|#)[\s/#*-]*$@', $comment) === 1;
    }
}
