<?php


namespace Directus\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class File extends Constraint
{  
    const NOT_FOUND_ERROR = 'd2a3fb6e-7ddc-4210-8fbf-2ab345ce1998';
    const NOT_READABLE_ERROR = 'c20c92a4-5bfa-4202-9477-28e800e0f6ff';
    const EMPTY_ERROR = '5d743385-9775-4aa5-8ff5-495fb1e60137';
    const TOO_LARGE_ERROR = 'df8637af-d466-48c6-a59d-e7126250a654';
    const INVALID_MIME_TYPE_ERROR = '744f00bc-4389-4c74-92de-9a43cde55534';

    protected static $errorNames = [
        self::NOT_FOUND_ERROR => 'NOT_FOUND_ERROR',
        self::NOT_READABLE_ERROR => 'NOT_READABLE_ERROR',
        self::EMPTY_ERROR => 'EMPTY_ERROR',
        self::TOO_LARGE_ERROR => 'TOO_LARGE_ERROR',
        self::INVALID_MIME_TYPE_ERROR => 'INVALID_MIME_TYPE_ERROR',
    ];
    public $maxSizeMessage = 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.';
    public $mimeTypesMessage = 'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.';

    public $binaryFormat;
    public $mimeTypes=[];

    protected $maxSize;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null !== $this->maxSize) {
            $this->normalizeBinaryFormat($this->maxSize);
        }
    }
    public function __set($option, $value)
    {
        if ('maxSize' === $option) {
            $this->normalizeBinaryFormat($value);

            return;
        }

        parent::__set($option, $value);
    }

    public function __get($option)
    {
        if ('maxSize' === $option) {
            return $this->maxSize;
        }

        return parent::__get($option);
    }

    public function __isset($option)
    {
        if ('maxSize' === $option) {
            return true;
        }

        return parent::__isset($option);
    }


    private function normalizeBinaryFormat($maxSize)
    {
        $factors = [
            'k' => 1000,
            'ki' => 1 << 10,
            'm' => 1000000,
            'mi' => 1 << 20,
        ];
        if (ctype_digit((string) $maxSize)) {
            $this->maxSize = (int) $maxSize;
            $this->binaryFormat = null === $this->binaryFormat ? false : $this->binaryFormat;
        } elseif (preg_match('/^(\d++)('.implode('|', array_keys($factors)).')$/i', $maxSize, $matches)) {
            $this->maxSize = $matches[1] * $factors[$unit = strtolower($matches[2])];
            $this->binaryFormat = null === $this->binaryFormat ? 2 === \strlen($unit) : $this->binaryFormat;
        } else {
            throw new ConstraintDefinitionException(sprintf('"%s" is not a valid maximum size', $this->maxSize));
        }
    }
}
