<?php

use App\Filament\Resources\Kategoris\KategoriResource;
use App\Filament\Resources\Kategoris\Pages\EditKategori;
use App\Filament\Resources\Tokos\Pages\EditToko;
use App\Filament\Resources\Tokos\TokoResource;
use App\Models\Kategori;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['status' => 1]));
});

test('saving a kategori edit page redirects to its list page', function () {
    $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);

    Livewire::test(EditKategori::class, ['record' => $kategori->getRouteKey()])
        ->fillForm(['nama_kategori' => 'Elektronik Updated'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect(KategoriResource::getUrl('index'));

    expect($kategori->refresh()->nama_kategori)->toBe('Elektronik Updated');
});

test('saving a toko edit page redirects to its list page', function () {
    $toko = Toko::create(['nama_toko' => 'Toko Pusat']);

    Livewire::test(EditToko::class, ['record' => $toko->getRouteKey()])
        ->fillForm(['nama_toko' => 'Toko Cabang'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect(TokoResource::getUrl('index'));

    expect($toko->refresh()->nama_toko)->toBe('Toko Cabang');
});
