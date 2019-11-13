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
     *
     * @ORM\Skip({
     *     @ORM\Skip(name="skip", value="skip this nested annotation")
     * })
     */
    public $annotationsNotAllowed;

    public function __construct(string $variable = '')
    {
        $localVariable = "Here is {$variable} in string.";
    }
};

class PropertiesWithTypes extends ParentClassName implements Interface1, Interface2
{
    public int $docBlockNotRequired;

    /**
     * @var null|DateTime Additional description
     */
    public ?DateTime $withDescription;

    /**
     * @var DateTime Additional description
     */
    public ?DateTime $incomplete;

    /** @var null|DateTime */
    public ?DateTime $redundant;

    /** @var string */
    protected int $invalid;

    /** @var int[] */
    protected array $spec;

    protected \PropertiesWithTypes $class;

    protected ParentClassName $parent;

    /** @var PropertiesWithTypes */
    protected $className;

    /** @var ParentClassName */
    protected $parentName;

    /** @var null|array|DateTime[] */
    public array $dates;

    /** @var array|DateTime */
    public array $date;

    /** @var iterable|int|MyObject */
    public iterable $iterator;

    /** @var Generator|string|object|MyClass */
    public Generator $generator;

    /** @var Traversable|int|parent|self|static */
    public Traversable $traversable;

    /** @var object|DateTime[] */
    public object $o1;

    /** @var object|int|DateTime */
    public object $o2;
}
