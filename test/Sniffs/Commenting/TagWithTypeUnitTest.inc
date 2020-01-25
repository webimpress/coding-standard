<?php

namespace MyNamespace\Test;

class TagWithType
{
    /**
     * @var array|bool|callable|float|int|iterable|object|parent|resource|self|static|string|MyClass
     */
    public $typesOrder;

    /** @var \Property_Type */
    public $className;

    /**
     * @ORM\ManyToMany(targetEntity="App\OAuth2\Domain\Scope", inversedBy="users")
     * @ORM\JoinTable(
     *  name="user_scopes",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="scope_id", referencedColumnName="id")
     *  }
     * )
     * @ORM\OrderBy({"urn" = "ASC"})
     *
     * @var Collection|Scope[]
     */
    protected $scopes;

    /**
     * @param \Param_Type $param
     * @return \Return_Type
     * @throws \Exception_Name
     */
    public function test(\Param_Type $param) : \Return_Type
    {
    }
}
