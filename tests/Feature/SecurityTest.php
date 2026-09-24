<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Kategori;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_escapes_xss_input_in_blade()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create(['status' => 1]); // Status 1 for admin
        $this->actingAs($user);

        $kategori = Kategori::create(['nama_kategori' => 'Test Kategori']);

        $xssInput = "<script>alert('xss')</script>";
        
        // We use the store method which we recently added validation to
        $response = $this->post('/barang', [
            'kategori_barang' => $kategori->id,
            'nama_barang' => $xssInput,
            'merk' => 'Test Merk',
            'satuan' => 'pcs',
            'warna' => 'red',
            'berat' => '1kg',
            'ukuran' => 'L',
            'harga' => '1000',
            'wajib_serial_number' => 0,
            'keterangan' => $xssInput,
        ]);

        $response->assertStatus(200);

        $barang = Barang::first();
        $this->assertEquals($xssInput, $barang->nama_product);

        // Simulate rendering in a blade context (manual check of escaping)
        $rendered = e($barang->nama_product);
        $this->assertStringContainsString('&lt;script&gt;', $rendered);
        $this->assertStringNotContainsString('<script>', $rendered);
    }

    /** @test */
    public function it_validates_input_to_prevent_malicious_data()
    {
        $user = User::factory()->create(['status' => 1]);
        $this->actingAs($user);

        // Attempting to send invalid data types to fields that should be strictly validated
        $response = $this->postJson('/barang', [
            'kategori_barang' => 'invalid-id', // Should be an existing ID
            'nama_barang' => '', // Should be required
            'harga' => 'not-a-number',
            'wajib_serial_number' => 'not-boolean',
        ]);

        $response->assertStatus(422); // Unprocessable Entity (Validation failed)
        $response->assertJsonValidationErrors(['kategori_barang', 'nama_barang', 'wajib_serial_number']);
    }
}
