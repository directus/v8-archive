<?php
namespace Directus\GraphQL\Type\Scalar;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Directus\Util\DateTimeUtils;

class TimeType extends ScalarType
{

    public $name = 'Time';

    public $description = 'Time scalar type.';

    public function serialize($value)
    {
        return $value;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral($valueNode, array $variables = null)
    {
        return $valueNode->value;
    }

}