<?php

namespace Directus\Database\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\Varchar;

class Uuid extends Varchar
{
    /**
     * @return int
     */
    public function getLength()
    {
        return 36;
    }

    /**
     * @return string
     */
    protected function getLengthExpression()
    {
        return (string) $this->getLength();
    }
}
