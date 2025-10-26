<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat admin
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Buat petugas
        $this->petugas = User::factory()->create([
            'role' => 'petugas'
        ]);
    }

    //Test Otorisasi (Petugas)
    public function test_petugas_cannot_view_user_list_page()
    {
        $response = $this->actingAs($this->petugas)->get(route('pengguna.index'));
        $response->assertStatus(302); // Asumsi Anda menggunakan 403 Forbidden
    }

    //Test Case untuk (Create)
    public function test_admin_can_create_new_user()
    {
        $userData = [
            'name' => 'User Baru',
            'email' => 'baru@example.com',
            'password' => 'password123',
            'role' => 'petugas',
            'status' => 'aktif',
        ];

        $response = $this->actingAs($this->admin)->post(route('pengguna.store'), $userData);

        $response->assertRedirect(route('pengguna.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => 'baru@example.com']);
    }

    //Test Validasi (Email User)
    public function test_admin_cannot_create_user_with_duplicate_email()
    {
        $userData = [
            'name' => 'User Gagal',
            'email' => $this->petugas->email, 
            'password' => 'password123',
            'role' => 'petugas',
            'status' => 'aktif',
        ];

        $response = $this->actingAs($this->admin)->post(route('pengguna.store'), $userData);

        $response->assertSessionHasErrors('email'); 
        $this->assertDatabaseMissing('users', ['name' => 'User Gagal']);
    }

    //Test Case untuk (update data user)
    public function test_admin_can_update_user_data()
    {
        $updateData = [
            'name' => 'Nama Sudah Diubah',
            'email' => $this->petugas->email,
            'role' => $this->petugas->role,
            'status' => 'non-aktif',
            'password' => null, // Tidak ganti password
        ];

        $response = $this->actingAs($this->admin)->put(route('pengguna.update', $this->petugas), $updateData);

        $response->assertRedirect(route('pengguna.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->petugas->id,
            'name' => 'Nama Sudah Diubah',
            'status' => 'non-aktif'
        ]);
    }

    //Test Case untuk (update password)
    public function test_admin_can_update_user_password()
    {
        $updateData = [
            'name' => $this->petugas->name,
            'email' => $this->petugas->email,
            'role' => $this->petugas->role,
            'status' => $this->petugas->status,
            'password' => 'passwordBaru123', 
        ];

        $response = $this->actingAs($this->admin)->put(route('pengguna.update', $this->petugas), $updateData);

        $this->assertTrue(Hash::check('passwordBaru123', $this->petugas->fresh()->password));
    }

    //Test Case untuk (delete)
    public function test_admin_can_delete_user()
    {
        $userToDelete = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('pengguna.destroy', $userToDelete));

        $response->assertRedirect(route('pengguna.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }
}