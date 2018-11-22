<?php
// @phpcs:set WebimpressCodingStandard.Commenting.VariableComment allowedAnnotations[] ORM

class VariableCommentWithSpecifiedAnnotations
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
