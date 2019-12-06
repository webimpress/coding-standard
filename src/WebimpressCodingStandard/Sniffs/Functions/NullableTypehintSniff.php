<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function strtolower;

use const T_CLOSURE;
use const T_FN;
use const T_FUNCTION;
use const T_NULLABLE;

class NullableTypehintSniff implements Sniff
{
    /**
     * @var bool
     */
    public $withQuestionMark = true;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CLOSURE, T_FN, T_FUNCTION];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $params = $phpcsFile->getMethodParameters($stackPtr);

        foreach ($params as $param) {
            if (empty($param['type_hint_token'])) {
                continue;
            }

            if (empty($param['default']) || strtolower($param['default']) !== 'null') {
                continue;
            }

            if ($this->withQuestionMark && ! $param['nullable_type']) {
                $error = 'Question mark is required with nullable typehints';
                $fix = $phpcsFile->addFixableError($error, $param['type_hint_token'], 'MissingQuestionMark');

                if ($fix) {
                    $phpcsFile->fixer->addContentBefore($param['type_hint_token'], '?');
                }
            } elseif (! $this->withQuestionMark && $param['nullable_type']) {
                $questionMark = $phpcsFile->findPrevious(T_NULLABLE, $param['type_hint_token']);

                $error = 'Question mark is redundant for parameters with default null value';
                $fix = $phpcsFile->addFixableError($error, $questionMark, 'RedundantQuestionMark');

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($questionMark, '');
                }
            }
        }
    }
}
