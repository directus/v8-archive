<?php
namespace Directus\GraphQL;

use Directus\GraphQL\Type\Directus\DirectusFileType;
use Directus\GraphQL\Type\Directus\DirectusFileThumbnailType;
use Directus\GraphQL\Type\Directus\DirectusUserType;
use Directus\GraphQL\Type\Directus\DirectusRoleType;
use Directus\GraphQL\Type\MetaType;
use Directus\GraphQL\Type\CollectionType;
use Directus\GraphQL\Type\FieldsType;
use Directus\GraphQL\Type\QueryType;
use Directus\GraphQL\Type\NodeType;
use Directus\GraphQL\Type\Scalar\DateType;
use Directus\GraphQL\Type\Scalar\TimeType;
use Directus\GraphQL\Type\Scalar\DateTimeType;
use Directus\GraphQL\Type\Scalar\JSONType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Directus\Application\Application;

class Types
{
    // Object types:
    private static $directusFile;
    private static $directusFileThumbnail;
    private static $directusUser;
    private static $directusRole;
    private static $query;
    private static $meta;

    //Used to save the reference of the created user collection.
    private static $userCollections = [];

    //Used to save the reference of the created list of collection.
    private static $collections = [];

    // Custom scalar types
    private static $date;
    private static $time;
    private static $datetime;
    private static $json;

    private static $node;

    public static function directusFile()
    {
        return self::$directusFile ?: (self::$directusFile = new DirectusFileType());
    }

    public static function directusFileThumbnail()
    {
        return self::$directusFileThumbnail ?: (self::$directusFileThumbnail = new DirectusFileThumbnailType());
    }

    public static function directusUser()
    {
        return self::$directusUser ?: (self::$directusUser = new DirectusUserType());
    }

    public static function directusRole()
    {
        return self::$directusRole ?: (self::$directusRole = new DirectusRoleType());
    }

    public static function meta()
    {
        return self::$meta ?: (self::$meta = new MetaType());
    }

    public static function collections($type)
    {
        if( ! array_key_exists($type , self::$collections) ) {
            $collectionType =  new CollectionType($type);
            self::$collections[$type] = $collectionType;
            return $collectionType;
        }else{
            return self::$collections[$type];
        }

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
    public static function date()
    {
        return self::$date ?: (self::$date = new DateType());
    }

    // Custom scalar type Time
    public static function time()
    {
        return self::$time ?: (self::$time = new TimeType());
    }

    // Custom scalar type DateTime
    public static function datetime()
    {
        return self::$datetime ?: (self::$datetime = new DateTimeType());
    }

    // Custom scalar type JSON
    public static function json()
    {
        return self::$json ?: (self::$json = new JSONType());
    }

    // Interface types
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
