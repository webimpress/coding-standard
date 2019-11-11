<?php
// @phpcs:set WebimpressCodingStandard.Commenting.MethodAnnotation allowedAnnotations

class MethodCommentWithAnyAnnotations
{
    /**
     * @return int
     * @ORM\Annotation
     * @Annotation\Hello
     */
    public function foo() : int
    {
        return mt_rand();
    }

    /**
     * @return void
     * @ORM\JoinColumn({
     *     @ORM\Something,
     *     @Rbac(entity=Some)
     * })
     */
    public function bar() : void
    {
    }

    /**
     * @ORM\FinalMethod
     * @Config(key="value")
     */
    final function finalMethod() : string
    {
        return __CLASS__;
    }
}
