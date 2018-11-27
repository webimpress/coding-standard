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
}
