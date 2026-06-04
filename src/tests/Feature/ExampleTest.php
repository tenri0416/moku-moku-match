<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function トップページへアクセスした時_正常に表示される(): void
    {
        // Arrange

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertOk();
    }
}
