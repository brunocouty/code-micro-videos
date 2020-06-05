<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genres = Genre::all();
        $this->assertCount(1, $genres);
        $genreKeys = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'is_active',
            'created_at',
            'deleted_at',
            'updated_at',
        ],
            $genreKeys);
    }

    public function testCreate()
    {
        // Cadastra um registro
        /** @var Genre $genre */
        $genre = Genre::create([
            'name' => 'test'
        ]);
        $genre->refresh();
        // O "id" deve ter 36 caracteres
        $this->assertEquals(36, strlen($genre->id));
        // O valor cadastrado deve ser "test"
        $this->assertEquals('test', $genre->name);
        // is_active deve ter valor TRUE
        $this->assertTrue((bool)$genre->is_active);
    }

    public function testUpdate()
    {
        // Adiciona o autocomplete na IDE
        /** @var Genre $genre */
        // Gera um registro
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ])->first();
        // Array com dados para alteração
        $data = [
            'name' => 'Name Updated',
            'is_active' => true
        ];
        $genre->update($data);
        // Verifica se cada campo teve seu respectivo valor persistido
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create()->first();
        // Apaga o registro
        $genre->delete();
        // O valor da consulta deve ser NULL
        $this->assertNull(Genre::find($genre->id));
        // Testa a restauração do dado (SoftDeletes)
        $genre->restore();
        // Não deve ser nulo
        $this->assertNotNull($genre);
        // Deve ser um objeto do tipo "Genre"
        $this->assertInstanceOf(Genre::class, $genre);
    }
}
