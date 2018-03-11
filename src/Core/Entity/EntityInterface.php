<?php

namespace Core\Entity;

use Core\ORM\ORMTable;

interface EntityInterface
{
    /**
     * EntityInterface constructor.
     * @param ORMTable $ORMTable
     */
    public function __construct(ORMTable $ORMTable);
}
