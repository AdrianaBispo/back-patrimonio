<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EquipamentoImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/equipaments/image-upload';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'create-equipaments']);
        Permission::firstOrCreate(['name' => 'update-equipaments']);
    }

    private function userWithPermissions(array $permissions): User
    {
        $user = User::create([
            'nome' => 'Teste',
            'celular' => '11999999999',
            'email' => uniqid('teste_', true) . '@example.com',
            'password' => 'senha1234',
        ]);

        $user->givePermissionTo($permissions);

        return $user;
    }

    private function uploadAs(?User $user, array $payload)
    {
        $request = $this->withHeader('Accept', 'application/json');

        if ($user) {
            $request = $request->withUnencryptedCookie('access_token', JWTAuth::fromUser($user));
        }

        return $request->post(self::URL, $payload);
    }

    public function test_sem_cookie_retorna_401(): void
    {
        $response = $this->uploadAs(null, [
            'image' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertStatus(401);
    }

    public function test_token_invalido_retorna_401(): void
    {
        $response = $this->withHeader('Accept', 'application/json')
            ->withUnencryptedCookie('access_token', 'token-invalido')
            ->post(self::URL, ['image' => UploadedFile::fake()->image('foto.jpg')]);

        $response->assertStatus(401);
    }

    public function test_usuario_sem_permissao_retorna_403(): void
    {
        $user = $this->userWithPermissions([]);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_usuario_so_com_create_equipaments_retorna_403(): void
    {
        $user = $this->userWithPermissions(['create-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_usuario_so_com_update_equipaments_retorna_403(): void
    {
        $user = $this->userWithPermissions(['update-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_jpg_valido_salva_arquivo(): void
    {
        $user = $this->userWithPermissions(['create-equipaments', 'update-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('foto.jpg', 800, 600),
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Imagem enviada com sucesso')
            ->assertJsonStructure(['message', 'imagepath']);

        Storage::disk('public')->assertExists($response->json('imagepath'));
    }

    public function test_upload_png_valido_salva_arquivo(): void
    {
        $user = $this->userWithPermissions(['create-equipaments', 'update-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('foto.png'),
        ]);

        $response->assertOk();
        Storage::disk('public')->assertExists($response->json('imagepath'));
    }

    public function test_sem_imagem_retorna_422(): void
    {
        $user = $this->userWithPermissions(['create-equipaments', 'update-equipaments']);

        $response = $this->uploadAs($user, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image' => 'A imagem é obrigatória']);
    }

    public function test_arquivo_que_nao_e_imagem_retorna_422(): void
    {
        $user = $this->userWithPermissions(['create-equipaments', 'update-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['image']);
    }

    public function test_gif_retorna_422(): void
    {
        $user = $this->userWithPermissions(['create-equipaments', 'update-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('animacao.gif'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image' => 'O arquivo deve ser uma imagem JPEG, PNG ou JPG']);
    }

    public function test_imagem_maior_que_5mb_retorna_422(): void
    {
        $user = $this->userWithPermissions(['create-equipaments', 'update-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('grande.jpg')->size(5121),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image' => 'O arquivo deve ter no máximo 5MB']);
    }

    public function test_imagem_com_exatamente_5mb_e_aceita(): void
    {
        $user = $this->userWithPermissions(['create-equipaments', 'update-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('limite.jpg')->size(5120),
        ]);

        $response->assertOk();
    }

    public function test_caminho_retornado_fica_em_uploads(): void
    {
        $user = $this->userWithPermissions(['create-equipaments', 'update-equipaments']);

        $response = $this->uploadAs($user, [
            'image' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertOk();
        $this->assertStringStartsWith('uploads/', $response->json('imagepath'));
    }
}
