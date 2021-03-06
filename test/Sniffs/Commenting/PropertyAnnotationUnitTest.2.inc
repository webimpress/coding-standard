<?php
// @phpcs:set WebimpressCodingStandard.Commenting.PropertyAnnotation allowedAnnotations[] ORM

class PropertyCommentWithSpecifiedAnnotations
{
    /**
     * @var string
     * @ORM\Annotation
     * @Annotation\Hello
     */
    public $foo;

    /**
     * @var int
     * @ORM\JoinColumn(
     *     @ORM\Something({
     *         @Rbac(entity=Some)
     *     })
     * )
     */
    public $bar;

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
     * @ORM\IntType
     * @Config(require="PHP 7.4")
     */
    private int $int;
}
