<?php

declare(strict_types=1);

namespace WebimpressCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use Throwable;
use WebimpressCodingStandard\Helper\NamingTrait;

use function class_exists;

use const T_CLASS;
use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_STRING;

class ExceptionSniff implements Sniff
{
    use NamingTrait;

    /**
     * @var string
     */
    public $prefix = '';

    /**
     * @var string
     */
    public $suffix = 'Exception';

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_CLASS];
    }

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $name = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
        $string = $tokens[$name]['content'];

        $fqn = $this->getNamespace($phpcsFile, $stackPtr) . '\\' . $string;

        try {
            if (! class_exists($fqn)) {
                return;
            }
        } catch (Throwable $e) {
            // Unable to load class for some reason - non existing parent class?
            return;
        }

        $reflection = new ReflectionClass($fqn);
        if (! $reflection->isSubclassOf(Throwable::class)) {
            return;
        }

        $this->check($phpcsFile, $name, 'Exception class');
    }

    private function getNamespace(File $phpcsFile, int $stackPtr) : string
    {
        $namespace = '';
        if ($i = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1)) {
            $tokens = $phpcsFile->getTokens();
            while (++$i) {
                switch ($tokens[$i]['code']) {
                    case T_SEMICOLON:
                    case T_OPEN_CURLY_BRACKET:
                        break 2;
                    case T_STRING:
                    case T_NS_SEPARATOR:
                        $namespace .= $tokens[$i]['content'];
                        break;
                }
            }
        }

        return $namespace;
    }
}
