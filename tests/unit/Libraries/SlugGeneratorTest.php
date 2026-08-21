<?php

declare(strict_types=1);

namespace Tests\Unit\Libraries;

use App\Libraries\SlugGenerator;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class SlugGeneratorTest extends CIUnitTestCase
{
    public function testGeneratesSlugFromTitle(): void
    {
        $gen = new SlugGenerator();

        $slug = $gen->unique('Hello World', static fn ($s, $e = null) => false);

        $this->assertSame('hello-world', $slug);
    }

    public function testEmptyTitleReturnsItem(): void
    {
        $gen = new SlugGenerator();

        $slug = $gen->unique('', static fn ($s, $e = null) => false);

        $this->assertSame('item', $slug);
    }

    public function testAppendsIndexWhenExists(): void
    {
        $calls = [];

        $checker = static function (string $slug, ?int $excludeId = null) use (&$calls): bool {
            $calls[] = $slug;

            // Simulate that the base slug exists once, then becomes available
            return $slug === 'hello-world';
        };

        $gen  = new SlugGenerator();
        $slug = $gen->unique('Hello World', $checker);

        $this->assertSame('hello-world-2', $slug);
        $this->assertSame(['hello-world', 'hello-world-2'], $calls);
    }

    public function testExcludeIdIsPassedToChecker(): void
    {
        $seen = null;

        $checker = static function (string $slug, ?int $excludeId = null) use (&$seen): bool {
            $seen = $excludeId;

            return false;
        };

        $gen = new SlugGenerator();
        $gen->unique('Sample', $checker, 42);

        $this->assertSame(42, $seen);
    }
}
