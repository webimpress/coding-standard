<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use WebimpressCodingStandard\Helper\NamingTrait;

use const T_STRING;
use const T_TRAIT;

class TraitSniff implements Sniff
{
    use NamingTrait;

    /**
     * @var string
     */
    public $prefix = '';

    /**
     * @var string
     */
    public $suffix = 'Trait';

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_TRAIT];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $name = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
        $this->check($phpcsFile, $name, 'Trait');
    }
}
