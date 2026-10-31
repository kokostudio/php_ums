<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RouteCompilePathTest extends TestCase
{
    #[DataProvider('pathProvider')]
    public function testCompileMatchesExpectedUris(string $path, string $uri, bool $shouldMatch): void
    {
        $pattern = Route::compilePath($path);
        $this->assertSame($shouldMatch, preg_match($pattern, $uri) === 1);
    }

    public static function pathProvider(): array
    {
        return [
            'static match'          => ['/users', '/users', true],
            'static mismatch'       => ['/users', '/roles', false],
            'named param match'     => ['/users/{id}/edit', '/users/42/edit', true],
            'named param mismatch'  => ['/users/{id}/edit', '/users/42/delete', false],
            'digit constraint ok'   => ['/users/{id:\d+}/delete', '/users/99/delete', true],
            'digit constraint fail' => ['/users/{id:\d+}/delete', '/users/abc/delete', false],
        ];
    }

    public function testNamedParamCapturesValue(): void
    {
        $pattern = Route::compilePath('/users/{id}/edit');
        $this->assertSame(1, preg_match($pattern, '/users/77/edit', $m));
        $this->assertSame('77', $m['id']);
    }
}
