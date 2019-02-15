<?php
namespace Directus\GraphQL;

use Directus\GraphQL\Type\DirectusFileType;
use Directus\GraphQL\Type\QueryType;
use Directus\GraphQL\Type\NodeType;
use Directus\GraphQL\Type\Scalar\DateType;
use Directus\GraphQL\Type\Scalar\DateTimeType;
use Directus\GraphQL\Type\Scalar\JSONType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

class Types
{
    // Object types:
    private static $directusFile;
    private static $query;

    public static function directusFile()
    {
        return self::$directusFile ?: (self::$directusFile = new DirectusFileType());
    }

    /**
     * @return QueryType
     */
    public static function query()
    {
        return self::$query ?: (self::$query = new QueryType());
    }

    // Custom scalar type Date
    private static $date;
    public static function date()
    {
        return self::$date ?: (self::$date = new DateType());
    }

    // Custom scalar type DateTime
    private static $datetime;
    public static function datetime()
    {
        return self::$datetime ?: (self::$datetime = new DateTimeType());
    }

    // Custom scalar type JSON
    private static $json;
    public static function json()
    {
        return self::$json ?: (self::$json = new JSONType());
    }

    // Interface types
    private static $node;
    /**
     * @return NodeType
     */
    public static function node()
    {
        return self::$node ?: (self::$node = new NodeType());
    }

    // Add internal types as well for consistent experience
    public static function boolean()
    {
        return Type::boolean();
    }
    /**
     * @return \GraphQL\Type\Definition\FloatType
     */
    public static function float()
    {
        return Type::float();
    }

    /**
     * @return \GraphQL\Type\Definition\IDType
     */
    public static function id()
    {
        return Type::id();
    }
    /**
     * @return \GraphQL\Type\Definition\IntType
     */
    public static function int()
    {
        return Type::int();
    }
    /**
     * @return \GraphQL\Type\Definition\StringType
     */
    public static function string()
    {
        return Type::string();
    }
    /**
     * @param Type $type
     * @return ListOfType
     */
    public static function listOf($type)
    {
        return new ListOfType($type);
    }
    /**
     * @param Type $type
     * @return NonNull
     */
    public static function nonNull($type)
    {
        return new NonNull($type);
    }
}
