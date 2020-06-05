<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class CategoryTest
 * @package Tests\Feature\Models
 */
class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'deleted_at',
            'updated_at',
        ],
            $categoryKeys);
    }

    public function testCreate()
    {
        // Cadastra um registro
        /** @var Category $category */
        $category = Category::create([
            'name' => 'test'
        ]);
        $category->refresh();
        // O "id" deve ter 36 caracteres
        $this->assertEquals(36, strlen($category->id));
        // O valor cadastrado deve ser "test"
        $this->assertEquals('test', $category->name);
        // "description" deve ter valor NULL
        $this->assertNull($category->description);
        // is_active deve ter valor TRUE
        $this->assertTrue((bool)$category->is_active);
        // Cadastra um novo registro
        /** @var Category $category */
        $category = Category::create([
            'name' => 'test',
            'description' => null
        ]);
        // O valor de "description" deve ser NULL
        $this->assertNull($category->description);
        // Cadastra um novo registro
        /** @var Category $category */
        $category = Category::create([
            'name' => 'test',
            'description' => 'Test description'
        ]);
        // O valor de "description" deve ser "Test description"
        $this->assertEquals('Test description', $category->description);
    }

    public function testUpdate()
    {
        // Adiciona o autocomplete na IDE
        /** @var Category $category */
        // Gera um registro
        $category = factory(Category::class)->create([
            'description' => 'Test description.',
            'is_active' => false
        ])->first();
        // Array com dados para alteração
        $data = [
            'name' => 'Name Updated',
            'description' => 'New description.',
            'is_active' => true
        ];
        $category->update($data);
        // Verifica se cada campo teve seu respectivo valor persistido
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create()->first();
        // Apaga o registro
        $category->delete();
        // O valor da consulta deve ser NULL
        $this->assertNull(Category::find($category->id));
        // Testa a restauração do dado (SoftDeletes)
        $category->restore();
        // Não deve ser nulo
        $this->assertNotNull($category);
        // Deve ser um objeto do tipo "Category"
        $this->assertInstanceOf(Category::class, $category);
    }
}
