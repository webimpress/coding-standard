<?php
// @phpcs:set WebimpressCodingStandard.Commenting.MethodAnnotation allowedAnnotations[] ORM

class MethodCommentWithSpecifiedAnnotations
{
    /**
     * @return void
     * @ORM\Annotation
     * @Annotation\Hello
     */
    public function foo() : void
    {
    }

    /**
     * @return int
     * @ORM\JoinColumn(
     *     @ORM\Something({
     *         @Rbac(entity=Some)
     *     })
     * )
     */
    public function bar() : int
    {
        return mt_rand();
    }
}
