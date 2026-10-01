<?php

use App\Filament\Resources\Kategoris\Pages\CreateKategori;
use App\Filament\Resources\Signatures\Pages\CreateSignature;
use App\Models\Kategori;
use App\Models\Signature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('creates a kategori from the filament form', function () {
    Livewire::test(CreateKategori::class)
        ->fillForm(['nama_kategori' => 'Elektronik'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Kategori::where('nama_kategori', 'Elektronik')->exists())->toBeTrue();
});

test('creates a signature and stores the base64 image', function () {
    $data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    Livewire::test(CreateSignature::class)
        ->fillForm([
            'name' => 'Andi',
            'signature' => $data,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Signature::where('name', 'Andi')->value('signature'))->toBe($data);
});
