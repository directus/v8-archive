<?php

namespace Directus\Database\Schema\Object;

use Directus\Config\StatusMapping;
use Directus\Database\Schema\SystemInterface;
use Directus\Util\ArrayUtils;

class Collection extends AbstractObject
{
    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $systemFields = [];

    /**
     * Gets the collection's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->attributes->get('collection');
    }

    /**
     * Sets the collection fields
     *
     * @param array $fields
     *
     * @return Collection
     */
    public function setFields(array $fields)
    {
        foreach ($fields as $field) {
            if (is_array($field)) {
                $field = new Field($field);
            }

            if (!($field instanceof Field)) {
                throw new \InvalidArgumentException('Invalid field object. ' . gettype($field) . ' given instead');
            }

            // @NOTE: This is a temporary solution
            // to always set the primary field to the first primary key field
            if (!$this->getPrimaryField() && $field->hasPrimaryKey()) {
                $this->setPrimaryField($field);
            } else if (!$this->getSortingField() && $field->getInterface() === SystemInterface::INTERFACE_SORTING) {
                $this->setSortingField($field);
            } else if (!$this->getStatusField() && $field->getInterface() === SystemInterface::INTERFACE_STATUS) {
                $this->setStatusField($field);
            } else if (!$this->getDateCreateField() && $field->getInterface() === SystemInterface::INTERFACE_DATE_CREATED) {
                $this->setDateCreateField($field);
            } else if (!$this->getUserCreateField() && $field->getInterface() === SystemInterface::INTERFACE_USER_CREATED) {
                $this->setUserCreateField($field);
            } else if (!$this->getDateUpdateField() && $field->getInterface() === SystemInterface::INTERFACE_DATE_MODIFIED) {
                $this->setDateUpdateField($field);
            } else if (!$this->getUserUpdateField() && $field->getInterface() === SystemInterface::INTERFACE_USER_MODIFIED) {
                $this->setUserUpdateField($field);
            }

            $this->fields[$field->getName()] = $field;
        }

        return $this;
    }

    /**
     * Gets a list of the collection's fields
     *
     * @param array $names
     *
     * @return Field[]
     */
    public function getFields(array $names = [])
    {
        $fields = $this->fields;

        if ($names) {
            $fields = array_filter($fields, function(Field $field) use ($names) {
                return in_array($field->getName(), $names);
            });
        }

        return $fields;
    }

    /**
     * Gets a field with the given name
     *
     * @param string $name
     *
     * @return Field
     */
    public function getField($name)
    {
        $fields = $this->getFields([$name]);

        // Gets the first matched result
        return array_shift($fields);
    }

    /**
     * Checks whether the collection is being managed by Directus
     *
     * @return bool
     */
    public function isManaged()
    {
        return $this->attributes->get('managed') == 1;
    }

    /**
     * Get all fields data as array
     *
     * @return array
     */
    public function getFieldsArray()
    {
        return array_map(function(Field $field) {
            return $field->toArray();
        }, $this->getFields());
    }

    /**
     * Gets all relational fields
     *
     * @param array $names
     *
     * @return Field[]
     */
    public function getRelationalFields(array $names = [])
    {
        return array_filter($this->getFields($names), function (Field $field) {
            return $field->hasRelationship();
        });
    }

    /**
     * Gets all relational fields
     *
     * @param array $names
     *
     * @return Field[]
     */
    public function getNonRelationalFields(array $names = [])
    {
        return array_filter($this->getFields($names), function(Field $field) {
            return !$field->hasRelationship();
        });
    }

    /**
     * Gets all the alias fields
     *
     * @return Field[]
     */
    public function getAliasFields()
    {
        return array_filter($this->getFields(), function(Field $field) {
            return $field->isAlias();
        });
    }

    /**
     * Gets all the non-alias fields
     *
     * @return Field[]
     */
    public function getNonAliasFields()
    {
        return array_filter($this->getFields(), function(Field $field) {
            return !$field->isAlias();
        });
    }

    /**
     * Gets all the fields name
     *
     * @return array
     */
    public function getFieldsName()
    {
        return array_map(function(Field $field) {
            return $field->getName();
        }, $this->getFields());
    }

    /**
     * Gets all the relational fields name
     *
     * @return array
     */
    public function getRelationalFieldsName()
    {
        return array_map(function (Field $field) {
            return $field->getName();
        }, $this->getRelationalFields());
    }

    /**
     * Gets all the alias fields name
     *
     * @return array
     */
    public function getAliasFieldsName()
    {
        return array_map(function(Field $field) {
            return $field->getName();
        }, $this->getAliasFields());
    }

    /**
     * Gets all the non-alias fields name
     *
     * @return array
     */
    public function getNonAliasFieldsName()
    {
        return array_map(function(Field $field) {
            return $field->getName();
        }, $this->getNonAliasFields());
    }

    /**
     * Checks whether the collection has a `primary key` interface field
     *
     * @return bool
     */
    public function hasPrimaryField()
    {
        return $this->getPrimaryField() ? true : false;
    }

    /**
     * Checks whether the collection has a `status` interface field
     *
     * @return bool
     */
    public function hasStatusField()
    {
        return $this->getStatusField() ? true : false;
    }

    /**
     * Checks whether the collection has a `sorting` interface field
     *
     * @return bool
     */
    public function hasSortingField()
    {
        return $this->getSortingField() ? true : false;
    }

    /**
     * Checks Whether or not the collection has the given field name
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasField($name)
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * Gets the schema/database this collection belongs to
     *
     * @return null|string
     */
    public function getSchema()
    {
        return $this->attributes->get('schema', null);
    }

    /**
     * Whether or not the collection is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return (bool)$this->attributes->get('hidden');
    }

    /**
     * Whether or not the collection is single
     *
     * @return bool
     */
    public function isSingle()
    {
        return (bool) $this->attributes->get('single');
    }

    /**
     * Gets the collection custom status mapping
     *
     * @return array|null
     */
    public function getStatusMapping()
    {
        $statusField = $this->getStatusField();
        if (!$statusField) {
            return null;
        }

        $mapping = $statusField->getOptions('status_mapping');
        if ($mapping === null) {
            return $mapping;
        }

        if ($mapping instanceof StatusMapping) {
            return $mapping;
        }

        if (!is_array($mapping)) {
            $mapping = @json_decode($mapping, true);
        }

        if (is_array($mapping)) {
            $this->attributes->set('status_mapping', new StatusMapping($mapping));

            $mapping = $this->attributes->get('status_mapping');
        }

        return $mapping;
    }

    /**
     * Sets the primary key interface field
     *
     * Do not confuse it with primary key
     *
     * @param Field $field
     *
     * @return Collection
     */
    public function setPrimaryField(Field $field)
    {
        return $this->setSystemField(SystemInterface::INTERFACE_PRIMARY_KEY, $field);
    }

    /**
     * Gets primary key interface field
     *
     * @return Field
     */
    public function getPrimaryField()
    {
        $field = $this->getSystemField(SystemInterface::INTERFACE_PRIMARY_KEY);

        if (!$field) {
            $field = $this->getPrimaryKey();
        }

        return $field;
    }

    /**
     * Gets the primary key field
     *
     * @return Field|null
     */
    public function getPrimaryKey()
    {
        $primaryKeyField = null;

        foreach ($this->getFields() as $field) {
            if ($field->hasPrimaryKey()) {
                $primaryKeyField = $field;
                break;
            }
        }

        return $primaryKeyField;
    }

    /**
     * Gets primary key interface field's name
     *
     * @return string
     */
    public function getPrimaryKeyName()
    {
        $primaryField = $this->getPrimaryKey();
        $name = null;

        if ($primaryField) {
            $name = $primaryField->getName();
        }

        return $name;
    }

    /**
     * Sets status interface field
     *
     * @param Field $field
     *
     * @return Collection
     */
    public function setStatusField(Field $field)
    {
        return $this->setSystemField(SystemInterface::INTERFACE_STATUS, $field);
    }

    /**
     * Gets status interface field
     *
     * @return Field|bool
     */
    public function getStatusField()
    {
        return $this->getSystemField(SystemInterface::INTERFACE_STATUS);
    }

    /**
     * Sets the sort interface field
     *
     * @param Field $field
     *
     * @return Collection
     */
    public function setSortingField(Field $field)
    {
        $this->setSystemField(SystemInterface::INTERFACE_SORTING, $field);

        return $this;
    }

    /**
     * Gets the sort interface field
     *
     * @return Field|null
     */
    public function getSortingField()
    {
        return $this->getSystemField(SystemInterface::INTERFACE_SORTING);
    }

    /**
     * Sets the field storing the record's user owner
     *
     * @param Field $field
     *
     * @return Collection
     */
    public function setUserCreateField(Field $field)
    {
        return $this->setSystemField(SystemInterface::INTERFACE_USER_CREATED, $field);
    }

    /**
     * Gets the field storing the record's user owner
     *
     * @return Field|bool
     */
    public function getUserCreateField()
    {
        return $this->getSystemField(SystemInterface::INTERFACE_USER_CREATED);
    }

    /**
     * Sets the field storing the user updating the record
     *
     * @param Field $field
     *
     * @return Collection
     */
    public function setUserUpdateField(Field $field)
    {
        return $this->setSystemField(SystemInterface::INTERFACE_USER_MODIFIED, $field);
    }

    /**
     * Gets the field storing the user updating the record
     *
     * @return Field|null
     */
    public function getUserUpdateField()
    {
        return $this->getSystemField(SystemInterface::INTERFACE_USER_MODIFIED);
    }

    /**
     * Sets the field storing the record created time
     *
     * @param Field $field
     *
     * @return Collection
     */
    public function setDateCreateField(Field $field)
    {
        return $this->setSystemField(SystemInterface::INTERFACE_DATE_CREATED, $field);
    }

    /**
     * Gets the field storing the record created time
     *
     * @return Field|null
     */
    public function getDateCreateField()
    {
        return $this->getSystemField(SystemInterface::INTERFACE_DATE_CREATED);
    }

    /**
     * Sets the field storing the record updated time
     *
     * @param Field $field
     *
     * @return Collection
     */
    public function setDateUpdateField(Field $field)
    {
        return $this->setSystemField(SystemInterface::INTERFACE_DATE_MODIFIED, $field);
    }

    /**
     * Gets the field storing the record updated time
     *
     * @return Field|null
     */
    public function getDateUpdateField()
    {
        return $this->getSystemField(SystemInterface::INTERFACE_DATE_MODIFIED);
    }

    /**
     * Gets the collection comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->attributes->get('comment');
    }

    /**
     * Gets the item preview url
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->attributes->get('preview_url');
    }

    /**
     * Gets Collection item name display template
     *
     * Representation value of the table items
     *
     * @return string
     */
    public function getItemNameTemplate()
    {
        return $this->attributes->get('item_name_template');
    }

    /**
     * @param string $interface
     *
     * @return Field|bool
     */
    protected function getSystemField($interface)
    {
        $systemField = ArrayUtils::get($this->systemFields, $interface, null);

        if ($systemField === null) {
            $systemField = false;

            foreach ($this->fields as $field) {
                if ($field->getInterface() === $interface) {
                    $systemField = $field;
                }
            }

            if ($systemField) {
                $this->setSystemField($interface, $systemField);
            } else {
                $this->systemFields[$interface] = $systemField;
            }
        }

        return $systemField;
    }

    /**
     * Sets the system interface field
     *
     * @param string $interface
     * @param Field $field
     *
     * @return $this
     */
    protected function setSystemField($interface, Field $field)
    {
        if ($field->getInterface() === $interface) {
            $this->systemFields[$interface] = $field;
        }

        return $this;
    }

    /**
     * Array representation of the collection with fields
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        $attributes['fields'] = $this->getFieldsArray();

        return $attributes;
    }
}
