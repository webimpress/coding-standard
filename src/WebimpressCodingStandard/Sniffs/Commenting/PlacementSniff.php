<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function implode;
use function substr;

use const T_CLOSE_TAG;
use const T_COMMENT;
use const T_OPEN_TAG;
use const T_WHITESPACE;

class PlacementSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_COMMENT];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['column'] !== 1
            && $tokens[$stackPtr - 1]['code'] !== T_WHITESPACE
            && ($tokens[$stackPtr - 1]['code'] !== T_COMMENT
                || $tokens[$stackPtr - 1]['line'] === $tokens[$stackPtr]['line'])
            && ($tokens[$stackPtr - 1]['code'] !== T_OPEN_TAG
                || substr($tokens[$stackPtr - 1]['content'], -1) !== ' ')
        ) {
            $error = 'Expected at least one space before comment';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'MissingSpaceBefore');

            if ($fix) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }
        }

        $lastInLine = $stackPtr;
        while ($next = $phpcsFile->findNext(Tokens::$emptyTokens + [T_CLOSE_TAG], $lastInLine + 1, null, true)) {
            if ($tokens[$next]['line'] === $tokens[$stackPtr]['line']) {
                $lastInLine = $next;
                continue;
            }

            break;
        }

        if ($lastInLine > $stackPtr) {
            $error = 'Comment must be at the end of the line';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'AtTheEnd');

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $comment = [];
                for ($i = $stackPtr; $i < $lastInLine; ++$i) {
                    if ($tokens[$i]['code'] === T_COMMENT) {
                        $comment[] = $tokens[$i]['content'];
                        $phpcsFile->fixer->replaceToken($i, ' ');
                    }
                }
                $phpcsFile->fixer->addContent($lastInLine, ' ' . implode(' ', $comment));
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE
            && $tokens[$stackPtr - 1]['content'] !== ' '
            && ($prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true))
            && $tokens[$prev]['code'] === T_COMMENT
            && $tokens[$prev]['line'] === $tokens[$stackPtr]['line']
        ) {
            $error = 'Expected exactly one space between comments';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBetweenComments');

            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr - 1, ' ');
            }
        }
    }
}
