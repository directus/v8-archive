<?php

namespace Directus\Tests\Api\Hook;

use Directus\Hook\Payload;

class HookListener
{
    public function __invoke(Payload $payload)
    {
        $payload->set('called', true);

        return $payload;
    }
}

class EmitterTest extends \PHPUnit_Framework_TestCase
{
    public function testEmitter()
    {
        $emitter = new \Directus\Hook\Emitter();

        // =========================================================
        // Empty listeners
        // =========================================================
        $this->assertEmpty($emitter->getActionListeners('removed'));
        $this->assertEmpty($emitter->getFilterListeners('fetched'));


        // =========================================================
        // Expect Action callback to be called once
        // =========================================================
        $actionMock = create_mock($this, 'stdClass', ['actionCallback']);
        $actionMock->expects($this->at(1))
            ->method('actionCallback')
            ->with($this->equalTo(2))
            ->will($this->returnValue(true));

        $this->assertFalse($emitter->hasActionListeners('removed'));

        $emitter->addAction('removed', [$actionMock, 'actionCallback']);

        // Using Hook Interface
        $actionHookMock = create_mock($this, '\Directus\Hook\HookInterface', array('handle'));
        $actionHookMock->expects($this->at(0))
            ->method('handle');

        $emitter->addAction('removed', [$actionMock, 'actionCallback']);
        $emitter->addAction('removed', $actionHookMock, $emitter::P_HIGH);
        $emitter->run('removed', 2);
        $emitter->execute('removed', 2);

        $this->assertTrue($emitter->hasActionListeners('removed'));

        // =========================================================
        // Expect Filter callback to be called once
        // and change the value
        // =========================================================
        $data = ['string' => 'rngr'];

        $this->assertFalse($emitter->hasFilterListeners('fetched'));
        $emitter->addFilter('fetched', function (\Directus\Hook\Payload $payload) {
            $payload->set('string', $payload->get('string') . 'a');

            return $payload;
        });

        $emitter->addFilter('fetched', function (\Directus\Hook\Payload $payload) {
            $payload->set('string', $payload->get('string') . 'b');

            return $payload;
        }, $emitter::P_LOW);

        $index = $emitter->addFilter('fetched', function (\Directus\Hook\Payload $payload) {
            $payload->set('string', $payload->get('string') . 'c');

            return $payload;
        }, $emitter::P_HIGH);

        $this->assertTrue($emitter->hasFilterListeners('fetched'));

        $result = $emitter->apply('fetched', $data);
        $this->assertSame('rngrcab', $result['string']);

        $data = ['string' => 'rngr'];
        $emitter->removeListenerWithIndex($index);
        $result = $emitter->apply('fetched', $data);
        $this->assertSame('rngrab', $result['string']);
    }

    public function testStringListener()
    {
        $emitter = new \Directus\Hook\Emitter();

        $data = [];
        $emitter->addFilter('filter', HookListener::class);

        $data = $emitter->apply('filter', $data);
        $this->assertArrayHasKey('called', $data);
        $this->assertTrue($data['called']);
    }

    public function testPayload()
    {
        $payload = new Payload(['name' => 'john'], ['age' => 25]);

        $this->assertSame('john', $payload->get('name'));
        $this->assertSame(25, $payload->attribute('age'));

        $payload->set('country', 'us');
        $this->assertSame('us', $payload->get('country'));

        $data = $payload->getData();
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('country', $data);
        $this->assertSame('john', $data['name']);
        $this->assertSame('us', $data['country']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testActionException()
    {
        $emitter = new \Directus\Hook\Emitter();
        $emitter->addAction('removed', 2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFilterException()
    {
        $emitter = new \Directus\Hook\Emitter();
        $emitter->addFilter('fetched', 2);
    }
}
