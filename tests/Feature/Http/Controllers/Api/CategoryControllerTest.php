<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @link /api/category/index
     */
    public function testIndex(): void
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('api.categories.index'));
        $response->assertStatus(200)
            // valida o JSON recebido
            ->assertJson([$category->toArray()]);
    }

    /**
     * @link /api/category/show/{category}
     */
    public function testShow(): void
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('api.categories.show', ['category' => $category->id]));
        $response->assertStatus(200)
            // Valida o JSON recebido
            ->assertJson($category->toArray());;
    }

    /**
     * Verifica o tratamento da validação de dados enviados
     * @return void
     * @link /api/category/update/{category}
     * @link /api/category/store
     */
    public function testInvalidationData(): void
    {
        $response = $this->json('POST', route('api.categories.store'), []);
        $this->assertInvalidationRequired($response);
        $response = $this->json(
            'POST',
            route('api.categories.store'),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]);
        $this->assertInvalidationBoolean($response);
        $this->assertInvalidationMax($response);
        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
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
            route('api.categories.store'),
            [
                'name' => 'Movie'
            ]);
        $category = $response->json('id');
        /** @var Category $category */
        $category = Category::find($category);
        $response->assertStatus(201)
            // Comparo os campos recebidos
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));
        // Outro Cenário
        $response = $this->json('POST', route('api.categories.store'), [
            'name' => 'Musical',
            'description' => 'Any description',
            'is_active' => false
        ]);
        $response->assertJsonFragment([
            'is_active' => false,
            'description' => 'Any description'
        ]);
        $this->assertFalse($response->json('is_active'));
        $this->assertNotNull($response->json('description'));
    }

    public function testUpdate(): void
    {
        /** @var Category $category */
        $category = factory(Category::class)->create([
            'is_active' => false,
            'description' => 'Description.'
        ]);
        $response = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            [
                'name' => 'Musical',
                'is_active' => true,
                'description' => 'Any description'
            ]);
        $category = $response->json('id');
        /** @var Category $category */
        $category = Category::find($category);
        $response->assertStatus(200)
            // Comparo os campos recebidos
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'is_active' => true,
                'description' => 'Any description'
            ]);
        $this->assertTrue($response->json('is_active'));
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
