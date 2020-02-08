<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractArraySniff;
use PHP_CodeSniffer\Util\Tokens;

use function in_array;
use function is_array;
use function preg_match;
use function preg_replace;
use function strtr;
use function substr;
use function token_get_all;

use const T_CONSTANT_ENCAPSED_STRING;
use const T_DOUBLE_QUOTED_STRING;
use const T_VARIABLE;

class DuplicateKeySniff extends AbstractArraySniff
{
    private const ALLOWED_CHARS = '/(?<!\\\\)(?:\\\\{2})*\\\(?:[0-7nrftve]|x[A-Fa-f0-9])/';

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param int $arrayStart
     * @param int $arrayEnd
     * @param array $indices
     */
    protected function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices) : void
    {
        $this->processArray($phpcsFile, $indices);
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param int $arrayStart
     * @param int $arrayEnd
     * @param array $indices
     */
    protected function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices) : void
    {
        $this->processArray($phpcsFile, $indices);
    }

    private function processArray(File $phpcsFile, array $indices) : void
    {
        $tokens = $phpcsFile->getTokens();

        $keys = [];
        foreach ($indices as $element) {
            if (! isset($element['index_start']) || ! isset($element['index_end'])) {
                continue;
            }

            $key = '';
            for ($i = $element['index_start']; $i <= $element['index_end']; ++$i) {
                $token = $tokens[$i];

                if ($token['code'] === T_DOUBLE_QUOTED_STRING
                    || $token['code'] === T_CONSTANT_ENCAPSED_STRING
                ) {
                    $content = '';
                    do {
                        $content .= $tokens[$i]['content'];

                        if (! isset($tokens[$i + 1])
                            || $tokens[$i + 1]['code'] !== $token['code']
                        ) {
                            break;
                        }
                    } while (++$i);

                    $key .= $this->doubleQuoted($content);
                } elseif (! in_array($token['code'], Tokens::$emptyTokens, true)) {
                    $key .= $token['content'];
                }
            }

            if (isset($keys[$key])) {
                $phpcsFile->addError(
                    'Duplicated array key; first usage in line %d',
                    $element['index_start'],
                    'DuplicateKey',
                    [$keys[$key]]
                );
            } else {
                $keys[$key] = $tokens[$element['index_start']]['line'];
            }
        }
    }

    private function doubleQuoted(string $string) : string
    {
        if ($string[0] !== '"' || substr($string, -1) !== '"') {
            return $this->preg($string);
        }

        $tokens = token_get_all('<?php ' . $string);
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_VARIABLE) {
                return $this->preg($string, true);
            }
        }

        if (preg_match(self::ALLOWED_CHARS, $string)) {
            return $this->preg($string, true);
        }

        $string = strtr(substr($string, 1, -1), [
            '\\"' => '"',
            "'" => "\\'",
        ]);

        return "'" . $this->preg($string) . "'";
    }

    private function preg(string $string, bool $doubleQuotes = false) : string
    {
        $chars = $doubleQuotes ? '[\\\\"0-7nrftve]|x[A-Fa-f0-9]' : '[\\\\\']';

        return preg_replace('/(?<!\\\\)(?:\\\\{2})*\\\(?!' . $chars . ')/', '$0\\\\', $string);
    }
}
