<?php
// @phpcs:set WebimpressCodingStandard.Commenting.PropertyAnnotation allowedAnnotations

class PropertyCommentWithAnyAnnotations
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
    public $bar;

    /**
     * @var bool
     * @ORM\Boolean
     * @Config(key="value")
     */
    var $var;
}
