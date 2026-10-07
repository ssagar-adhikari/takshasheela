<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_displays_the_public_homepage(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('A journey awaits.');
    }
}
