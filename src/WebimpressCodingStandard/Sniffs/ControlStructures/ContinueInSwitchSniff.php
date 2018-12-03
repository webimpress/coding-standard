<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function array_reverse;
use function in_array;

use const T_CASE;
use const T_CONTINUE;
use const T_ELSE;
use const T_ELSEIF;
use const T_IF;
use const T_LNUMBER;
use const T_OPEN_PARENTHESIS;
use const T_SWITCH;

class ContinueInSwitchSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CONTINUE];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $arg = $phpcsFile->findNext(
            Tokens::$emptyTokens + [T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS],
            $stackPtr + 1,
            null,
            true
        );

        if ($tokens[$arg]['code'] === T_LNUMBER) {
            return;
        }

        $conditions = array_reverse($tokens[$stackPtr]['conditions']);

        foreach ($conditions as $token) {
            if (in_array($token, [T_CASE, T_IF, T_ELSEIF, T_ELSE], true)) {
                continue;
            }

            if ($token === T_SWITCH) {
                $error = 'Continue instruction is disallowed in switch, use break instead';
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Disallow');

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($stackPtr, 'break');
                }
            }

            break;
        }
    }
}
