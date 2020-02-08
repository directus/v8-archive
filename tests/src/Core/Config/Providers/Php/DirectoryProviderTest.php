<?php

declare(strict_types=1);

namespace Directus\Tests\Core\Config\Providers\Php;

use Directus\Core\Config\Exception\FileNotFound;
use Directus\Core\Config\Loaders\RequireLoader;
use Directus\Core\Config\Providers\Php\DirectoryProvider;
use Directus\Core\Options\Exception\InvalidOption;
use Directus\Core\Options\Exception\MissingOptions;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * PHP Provider tests.
 *
 * @internal
 * @coversNothing
 */
final class DirectoryProviderTest extends DirectusTestCase
{
    private const FILE_PATH = 'config/projects/{project}.php';

    /**
     * Provider.
     *
     * @var DirectoryProvider
     */
    protected $provider;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        $this->provider = new DirectoryProvider([
            'path' => $this->getDataFilePath(static::FILE_PATH),
        ]);
    }

    /**
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::__construct
     */
    public function testPathShouldBeRequired(): void
    {
        $this->expectException(MissingOptions::class);

        new DirectoryProvider([]);
    }

    /**
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::__construct
     */
    public function testPathMustContainProject(): void
    {
        $this->expectException(InvalidOption::class);

        new DirectoryProvider([
            'path' => 'hello',
        ]);
    }

    /**
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::get
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::project
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::separate
     */
    public function testCanLoadProjectFile(): void
    {
        static::assertSame('mysql', $this->provider->get('project1.database.driver'));
        static::assertSame('sqlite', $this->provider->get('project2.database.driver'));
    }

    /**
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::load
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::project
     */
    public function testFailIfConfigFileDoesntExists(): void
    {
        $this->expectException(FileNotFound::class);
        $this->provider->get('project3.database.driver');
    }

    /**
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::load
     * @covers \Directus\Core\Config\Providers\Php\DirectoryProvider::project
     */
    public function testFetchFromMemoryIfAlreadyLoaded(): void
    {
        /** @var RequireLoader&\PHPUnit\Framework\MockObject\MockObject */
        $loader = $this->getMockBuilder(RequireLoader::class)
            ->getMock()
        ;

        $loader
            ->expects(static::any())
            ->method('exists')
            ->willReturn(true)
        ;

        $loader
            ->expects(static::once())
            ->method('load')
            ->willReturn([
                'database' => [
                    'driver' => 'from_mock',
                ],
            ])
        ;

        $provider = new DirectoryProvider([
            'path' => $this->getDataFilePath(static::FILE_PATH),
            'loader' => $loader,
        ]);

        static::assertSame('from_mock', $provider->get('project1.database.driver'));
        static::assertSame('from_mock', $provider->get('project1.database.driver'));
    }
}
