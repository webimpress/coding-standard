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
     * @var bool
     * @ORM\Column(@ORM\Value({"var", "bar", "baz"}))
     */
    private $baz;

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
