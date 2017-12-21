<?php

namespace Directus\Tests\Api;

use Directus\Validator\Exception\UnknownConstraintException;
use Directus\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessfulConstraint()
    {
        $validator = new Validator();

        $data = [
            'string' => 'string-value',
            'numeric' => 1,
            'required' => true,
            'email' => 'admin@getdirectus.com'
        ];
        $rules = [
            'string' => 'string',
            'numeric' => 'numeric',
            'required' => 'required',
            'email' => 'email'
        ];

        foreach ($data as $key => $value) {
            $violations = $validator->validate($value, [$key => $rules[$key]]);
            $this->assertSame(0, $violations->count());
        }
    }

    public function testFailedConstraint()
    {
        $validator = new Validator();

        $data = [
            'string' => 1,
            'numeric' => 'string',
            'required' => null,
            'email' => 'email'
        ];
        $rules = [
            'string' => 'string',
            'numeric' => 'numeric',
            'required' => 'required',
            'email' => 'email'
        ];

        foreach ($data as $key => $value) {
            $violations = $validator->validate($value, [$key => $rules[$key]]);
            $this->assertSame(1, $violations->count());
        }
    }

    /**
     * @expectedException \Directus\Validator\Exception\UnknownConstraintException
     */
    public function testUnknownConstraint()
    {
        $validator = new Validator();

        $validator->validate(['data' => 1], ['data' => 'boolean']);
    }
}
