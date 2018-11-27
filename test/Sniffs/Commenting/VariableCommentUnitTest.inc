<?php // @phpcs:set WebimpressCodingStandard.Commenting.VariableComment allowedTags[] @link

class Foo {
    // @var int
    public $bar;

    // inline comment is allowed here

    public $missingDocCommentInlineCommentBefore;

    public $missingDocComment;

    /** @var $missingType */
    public $missingType;

    /** @VAR int */
    public $noLowerCase;

    /**
     * @var array {
     *     @var bool $required
     *     @var string $label
     * }
     */
    public $array;

    /**
     * @var int
     * @var string
     */
    public $doubleVar;

    /**
     * @var array {
     *     @var int $int
     */
    public $invalidNested;

    /**
     * @var array {
     *     @var array $arr {
     *         @var string $label
     *         @param int $int Cannot be nested.
     *     }
     * }
     */
    public $multipleNested;
}

new class () {
    /** @param string */
    protected $param;

    /** @const float */
    public $const;

    /**
     * @var null|string
     * @see Foo::$array
     * @link https://www.google.com/
     * @Skip This could be annotation, and it should be skipped here.
     */
    public $annotationsNotAllowed;

    public function __construct(string $variable = '')
    {
        $localVariable = "Here is {$variable} in string.";
    }
};
