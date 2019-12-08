<?php
// @phpcs:set WebimpressCodingStandard.Commenting.PropertyAnnotation allowedAnnotations[]

class PropertyCommentWithoutAnnotations
{
    /**
     * @var string
     * @ORM\Annotation
     * @Annotation\Hello
     */
    public $foo;

    /**
     * @var int
     * @ORM\JoinColumn({
     *     @ORM\Something,
     *     @Rbac(entity=Some)
     * })
     */
    protected static $bar;

    /**
     * @var bool
     * @ORM\Boolean
     * @Config(key="value")
     */
    var $var;

    /**
     * @var null|DateTime
     * @ORM\WithType
     * @Config(require="PHP 7.4")
     */
    private ?\DateTime $typed;

    /**
     * @var bool
     * @ORM\Column(@ORM\Value({"var", "bar", "baz"}))
     */
    private $baz;

    /**
     * @ORM\CallbackFunction
     * @Config(require="PHP 7.4")
     */
    private callable $callback;

    /**
     * @var null|string
     */
    public $noAnnotations;

    /**
     * This is the description.
     */
    public $noTags;

    public $noComments;

    public function __construct(string $variable = '')
    {
        $localVariable = "Here is {$variable} in string.";
    }
}
