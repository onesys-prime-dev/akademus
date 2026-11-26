<?php

namespace Tests\Feature;

use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\DatabaseTestCase;

/**
 * The public pages render on a freshly migrated and seeded database.
 *
 * @internal
 */
final class PublicPagesTest extends DatabaseTestCase
{
    use FeatureTestTrait;

    #[DataProvider('providePublicPageRenders')]
    public function testPublicPageRenders(string $uri): void
    {
        $this->get($uri)->assertOK();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function providePublicPageRenders(): iterable
    {
        foreach (['/', 'homepage', 'about-us', 'features', 'contact', 'sign-in', 'sign-up', 'admin/login', 'admission-officer/login'] as $uri) {
            yield $uri => [$uri];
        }
    }

    public function testSignInFormIsCsrfProtected(): void
    {
        $result = $this->get('sign-in');

        $result->assertOK();
        $result->assertSee('name="' . csrf_token() . '"');
    }

    public function testSignInShowsThePortalName(): void
    {
        $this->get('sign-in')->assertSee('Akademus Portal');
    }
}
