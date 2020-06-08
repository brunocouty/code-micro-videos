<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @link /api/genre/index
     */
    public function testIndex(): void
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('api.genres.index'));
        $response->assertStatus(200)
            // valida o JSON recebido
            ->assertJson([$genre->toArray()]);
    }

    /**
     * @link /api/genre/show/{genre}
     */
    public function testShow(): void
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('api.genres.show', ['genre' => $genre->id]));
        $response->assertStatus(200)
            // Valida o JSON recebido
            ->assertJson($genre->toArray());;
    }

    /**
     * Verifica o tratamento da validação de dados enviados
     * @return void
     * @link /api/genre/update/{genre}
     * @link /api/genre/store
     */
    public function testInvalidationData(): void
    {
        $response = $this->json('POST', route('api.genres.store'), []);
        $this->assertInvalidationRequired($response);
        $response = $this->json(
            'POST',
            route('api.genres.store'),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]);
        $this->assertInvalidationBoolean($response);
        $this->assertInvalidationMax($response);
        $genre = factory(Genre::class)->create();
        $response = $this->json(
            'PUT',
            route('api.genres.update', ['genre' => $genre->id]),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]);
        $this->assertInvalidationBoolean($response);
        $this->assertInvalidationMax($response);
    }

    public function testStore(): void
    {
        $response = $this->json(
            'POST',
            route('api.genres.store'),
            [
                'name' => 'Movie'
            ]);
        $genre = $response->json('id');
        /** @var Genre $genre */
        $genre = Genre::find($genre);
        $response->assertStatus(201)
            // Comparo os campos recebidos
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));
        // Outro Cenário
        $response = $this->json('POST', route('api.genres.store'), [
            'name' => 'Musical',
            'is_active' => false
        ]);
        $response->assertJsonFragment([
            'is_active' => false,
        ]);
        $this->assertFalse($response->json('is_active'));
    }

    public function testUpdate(): void
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create([
            'is_active' => false,
        ]);
        $response = $this->json(
            'PUT',
            route('api.genres.update', ['genre' => $genre->id]),
            [
                'name' => 'Musical',
                'is_active' => true,
            ]);
        $genre = $response->json('id');
        /** @var Genre $genre */
        $genre = Genre::find($genre);
        $response->assertStatus(200)
            // Comparo os campos recebidos
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true,
            ]);
        $this->assertTrue($response->json('is_active'));
    }

    public function testDestroy(): void
    {
        $genre = factory(Genre::class)->create();
        $response = $this->json(
            'GET',
            route('api.genres.show', ['genre' => $genre->id])
        );
        $response->assertStatus(200);
        $response = $this->json(
            'DELETE',
            route('api.genres.destroy', ['genre' => $genre->id])
        );
        $response->assertStatus(204);
        $response = $this->json(
            'GET',
            route('api.genres.show', ['genre' => $genre->id])
        );
        $response->assertStatus(404);
        $this->assertNotNull(Genre::withTrashed()->find(($genre->id)));
    }

    # Métodos auxiliares para as validações

    public function assertInvalidationRequired(TestResponse $response): void
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']);
    }

    public function assertInvalidationBoolean(TestResponse $response): void
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ])
            ->assertJsonFragment([
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function assertInvalidationMax(TestResponse $response): void
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }
}
