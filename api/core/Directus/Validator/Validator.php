<?php

namespace Directus\Validator;

use Directus\Validator\Exception\UnknownConstraintException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

class Validator
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $provider;

    public function __construct()
    {
        $this->provider = Validation::createValidator();
    }

    /**
     * @param mixed $value
     * @param array $constraints
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validate($value, array $constraints)
    {
        // TODO: Support OR validation
        // Ex. value must be Numeric or string type (Scalar without boolean)
        return $this->provider->validate($value, $this->createConstraintFromList($constraints));
    }

    /**
     * Creates constraints object from name
     *
     * @param array $constraints
     *
     * @return Constraint[]
     */
    protected function createConstraintFromList(array $constraints)
    {
        $constraintsObjects = [];

        foreach ($constraints as $constraint) {
            $constraintsObjects[] = $this->getConstraint($constraint);
        }

        return $constraintsObjects;
    }

    /**
     * Gets constraint with the given name
     *
     * @param $name
     * @return null|Constraint
     *
     * @throws UnknownConstraintException
     */
    protected function getConstraint($name)
    {
        $constraint = null;

        switch ($name) {
            case 'required':
                $constraint = new NotBlank();
                break;
            case 'email':
                $constraint = new Email();
                break;
            case 'numeric':
            case 'string':
                $constraint = new Type(['type' => $name]);
                break;
            default:
                throw new UnknownConstraintException(sprintf('Unknown "%s" constraint', $name));
        }

        return $constraint;
    }
}
