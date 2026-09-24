<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Kategori;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_admin_to_create_barang()
    {
        $user = User::factory()->create(['status' => 1]); // Admin
        $this->actingAs($user);

        $kategori = Kategori::create(['nama_kategori' => 'Electronics']);

        $response = $this->post('/barang', [
            'kategori_barang' => $kategori->id,
            'nama_barang' => 'Laptop ASUS',
            'merk' => 'ASUS',
            'satuan' => 'unit',
            'warna' => 'Silver',
            'berat' => '2kg',
            'ukuran' => '14 inch',
            'harga' => '10,000,000',
            'wajib_serial_number' => 1,
            'keterangan' => 'High performance laptop',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('barangs', [
            'nama_product' => 'Laptop ASUS',
            'harga' => 10000000,
        ]);
    }

    /** @test */
    public function it_allows_admin_to_update_barang()
    {
        $user = User::factory()->create(['status' => 1]);
        $this->actingAs($user);

        $kategori = Kategori::create(['nama_kategori' => 'Electronics']);
        $barang = Barang::create([
            'kategori_id' => $kategori->id,
            'nama_product' => 'Old Name',
            'harga' => 5000,
            'wajib_serial_number' => 0,
        ]);

        $response = $this->post('/barang/update', [
            'id' => $barang->id,
            'kategori_barang_edit' => $kategori->id,
            'nama_barang_edit' => 'New Name',
            'harga_edit' => '7,000',
            'wajib_serial_number_edit' => 1,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'nama_product' => 'New Name',
            'harga' => 7000,
        ]);
    }

    /** @test */
    public function it_allows_admin_to_delete_barang()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create(['status' => 1]);
        $this->actingAs($user);

        $barang = Barang::create([
            'nama_product' => 'To be deleted',
            'harga' => 1000,
            'wajib_serial_number' => 0,
        ]);

        $response = $this->post('/barang/delete', [
            'id' => $barang->id,
        ]);

        $response->assertStatus(200);
        $this->assertSoftDeleted('barangs', ['id' => $barang->id]);
    }
}
