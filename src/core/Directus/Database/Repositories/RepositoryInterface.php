<?php

namespace Directus\Database\Repositories;

use Directus\Database\RowGateway\BaseRowGateway;

interface RepositoryInterface
{
    /**
     * Finds one record with the given attribute.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return array|BaseRowGateway
     */
    public function findOneBy($attribute, $value);

    /**
     * Finds one record with the given id.
     *
     * @param int|string $id
     *
     * @return array|BaseRowGateway
     */
    public function find($id);
}
