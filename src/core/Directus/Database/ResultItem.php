<?php

namespace Directus\Database;

use Directus\Collection\Collection;

class ResultItem extends Collection
{
    /**
     * @return $this
     */
    public function exchangeArray(array $data)
    {
        $this->items = $data;

        return $this;
    }
}
