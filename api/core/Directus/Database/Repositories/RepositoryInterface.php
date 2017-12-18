<?php

/**
 * Directus – <http://getdirectus.com>
 *
 * @link      The canonical repository – <https://github.com/directus/directus>
 * @copyright Copyright 2006-2017 RANGER Studio, LLC – <http://rangerstudio.com>
 * @license   GNU General Public License (v3) – <http://www.gnu.org/copyleft/gpl.html>
 */

namespace Directus\Database\Repositories;

use Directus\Database\RowGateway\BaseRowGateway;

interface RepositoryInterface
{
    /**
     * Finds one record with the given attribute
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return array|BaseRowGateway
     */
    public function findOneBy($attribute, $value);

    /**
     * Finds one record with the given id
     *
     * @param string|int $id
     *
     * @return array|BaseRowGateway
     */
    public function find($id);

}
