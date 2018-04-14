<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use WebimpressCodingStandard\Helper\NamingTrait;

use const T_ABSTRACT;
use const T_CLASS;
use const T_STRING;

class AbstractClassSniff implements Sniff
{
    use NamingTrait;

    /**
     * @var string
     */
    public $prefix = 'Abstract';

    /**
     * @var string
     */
    public $suffix = '';

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_ABSTRACT];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $class = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$class]['code'] !== T_CLASS) {
            return;
        }

        $name = $phpcsFile->findNext(T_STRING, $class + 1);
        $this->check($phpcsFile, $name, 'Abstract class');
    }
}
