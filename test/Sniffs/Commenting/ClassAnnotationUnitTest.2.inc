<?php // @phpcs:set WebimpressCodingStandard.Commenting.ClassAnnotation allowedAnnotations[] ORM,Params

/**
 * @ORM\Table(name="table")
 * @ORM\Entity(repositoryClass="Application\Repository\Entity")
 * @ORM\HasLifecycleCallbacks
 *
 * @Gedmo\Loggable(logEntryClass="Application\Entity\ApplicationLog")
 *
 * @Config(
 *     variable="value",
 *     alias="alias",
 *     parameters=@Params(
 *          start="yesterday",
 *          stop="tomorrow",
 *          ticks={"1s","1m","1h"}
 *     )
 * )
 *
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
class Entity {
}
