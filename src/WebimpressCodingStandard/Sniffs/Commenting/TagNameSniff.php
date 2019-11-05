<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function rtrim;

use const T_DOC_COMMENT_TAG;

class TagNameSniff implements Sniff
{
    /**
     * @var string Characters disallowed at the end of the PHPDoc tag name
     */
    public $disallowedEndChars = ':;';

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $content = $tokens[$stackPtr]['content'];
        $newContent = rtrim($content, $this->disallowedEndChars);

        if ($content !== $newContent) {
            $error = 'Invalid tag name found %s, expected %s';
            $data = [
                $content,
                $newContent,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'DisallowedEndChars', $data);
            if ($fix) {
                $phpcsFile->fixer->replaceToken($stackPtr, $newContent);
            }
        }
    }
}
