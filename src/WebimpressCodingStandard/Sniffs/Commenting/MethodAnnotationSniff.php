<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use WebimpressCodingStandard\Helper\AnnotationsTrait;

use const T_CLASS;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_TRAIT;

class MethodAnnotationSniff extends AbstractScopeSniff
{
    use AnnotationsTrait;

    public function __construct()
    {
        parent::__construct([T_CLASS, T_INTERFACE, T_TRAIT], [T_FUNCTION]);
    }

    /**
     * @param int $stackPtr
     * @param int $currScope
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope) : void
    {
        $this->processAnnotations($phpcsFile, $stackPtr);
    }

    /**
     * @codeCoverageIgnore
     *
     * @param int $stackPtr
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr) : void
    {
        // We are only checking methods in the class/interface/trait
    }
}
