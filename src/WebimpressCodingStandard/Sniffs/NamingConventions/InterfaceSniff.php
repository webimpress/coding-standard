<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use WebimpressCodingStandard\Helper\NamingTrait;

use const T_INTERFACE;
use const T_STRING;

class InterfaceSniff implements Sniff
{
    use NamingTrait;

    /**
     * @var string
     */
    public $prefix = '';

    /**
     * @var string
     */
    public $suffix = 'Interface';

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_INTERFACE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $name = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
        $this->check($phpcsFile, $name, 'Interface');
    }
}
