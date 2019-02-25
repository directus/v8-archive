<?php
namespace Directus\GraphQL;

use Directus\GraphQL\Type\Directus\FilesType;
use Directus\GraphQL\Type\Directus\FileThumbnailType;
use Directus\GraphQL\Type\Directus\UsersType;
use Directus\GraphQL\Type\FieldsType;
use Directus\GraphQL\Type\QueryType;
use Directus\GraphQL\Type\NodeType;
use Directus\GraphQL\Type\Scalar\DateType;
use Directus\GraphQL\Type\Scalar\DateTimeType;
use Directus\GraphQL\Type\Scalar\JSONType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Directus\Application\Application;

class Types
{
    // Object types:
    private static $files;
    private static $fileThumbnail;
    private static $users;
    private static $query;
    private static $userCollection;
    //Used to save the reference of the already created user collection.
    private static $userCollections = [];

    public static function directusFile()
    {
        return self::$files ?: (self::$files = new FilesType());
    }

    public static function fileThumbnail()
    {
        return self::$fileThumbnail ?: (self::$fileThumbnail = new FileThumbnailType());
    }

    public static function users()
    {
        return self::$users ?: (self::$users = new UsersType());
    }

    /**
     * This function creates run-time GraphQL type according to user created collections.
     * If the type is already created, we'll return existing object.
     * Else create a new type and add it to array.
     */
    public static function userCollection($query)
    {
        if (!array_key_exists($query, self::$userCollections)) {
            $fieldsType =  new FieldsType($query);
            self::$userCollections[$query] = $fieldsType;
            return $fieldsType;
        } else {
            return self::$userCollections[$query];
        }
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
