<?php

namespace Directus\Config\Schema;

use Directus\Config\Schema\Exception\OmitException;

/**
 * Value node.
 */
class Value extends Base implements Node
{
    /**
     * Value type.
     */
    private $_type = 'string';

    /**
     * Default value.
     */
    private $_default;

    /**
     * Construct.
     *
     * @param mixed      $name
     * @param mixed      $type
     * @param null|mixed $default
     */
    public function __construct($name, $type, $default = null)
    {
        parent::__construct($name, []);
        $this->_type = $type;
        $this->_default = $default;
    }

    /**
     * Gets a value from leaf node.
     *
     * @param mixed $context
     */
    public function value($context)
    {
        $context = $this->normalize($context);

        if (!isset($context) || !isset($context[$this->key()])) {
            if ($this->optional()) {
                throw new OmitException();
            }

            return $this->_default;
        }

        $value = $context[$this->key()];

        switch ($this->_type) {
            case Types::INTEGER:
                return intval($value);
            case Types::BOOLEAN:
                $value = strtolower($value);

                return 'true' === $value || '1' === $value || 'on' === $value || 'yes' === $value || boolval($value);
            case Types::FLOAT:
                return floatval($value);
            case Types::ARRAY:
                if (!is_array($value)) {
                    return $this->_default;
                }
                // no break
            case Types::STRING:
            default:
                return $value;
        }
    }
}
