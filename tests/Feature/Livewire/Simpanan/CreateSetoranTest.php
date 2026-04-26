<?php

namespace Tests\Feature\Livewire\Simpanan;

use Livewire\Volt\Volt;
use Tests\TestCase;

class CreateSetoranTest extends TestCase
{
    public function test_it_can_render(): void
    {
        $component = Volt::test('simpanan.create-setoran');

        $component->assertSee('');
    }
}
