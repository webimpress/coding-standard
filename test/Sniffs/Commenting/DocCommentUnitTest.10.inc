<?php

namespace MyApp\MyNamespace;

/**
 * @ORM\Table(
 *     name="foo",
 *     indexes={
 *         @ORM\Index(
 *             name="foo_idx",
 *             columns={"foo"}
 *         )
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="unique_idx",
 *             columns={"unique"}
 *         )
 *     }
 * )
 * @ORM\Table(
 *     name="foo",
 *     indexes={
 *         @ORM\Index(name="foo_idx", columns={"foo"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="unique_idx", columns={"unique"})
 *     }
 * )
 */
class Foo
{
    /**
     * @var bool
     *
     * @ORM\Column(name="required", type="boolean", nullable=false, options={
     *     "default": "false"
     * })
     */
    public $a;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="required",
     *     type="boolean",
     *     nullable=false,
     *     options={
     *         "default": "false"
     *     }
     * )
     */
    public $b;
}
