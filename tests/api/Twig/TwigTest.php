<?php

namespace Directus\Tests\Api;

use Directus\View\Twig\DirectusTwigExtension;

class TwigTest extends \PHPUnit\Framework\TestCase
{
    public function testExtension()
    {
        $extension = new DirectusTwigExtension();

        $this->assertInternalType('array', $extension->getFilters());
        $this->assertInternalType('array', $extension->getFunctions());
        $this->assertInternalType('string', $extension->getName());
    }
}
