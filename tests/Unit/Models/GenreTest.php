<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreTest extends TestCase
{
    public function testFillableAttribute()
    {
        $fillable = [
            'name',
            'is_active',
        ];
        $genre = new Genre();
        $this->assertEquals($fillable, $genre->getFillable());
    }

    public function testCastsAttributes()
    {
        $casts = [
            'id' => 'string',
            'is_active' => 'boolean'
        ];
        $genre = new Genre();
        $this->assertEquals($casts, $genre->getCasts());
    }

    public function testDatesAttributes()
    {
        $dates = [
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $genre = new Genre();
        foreach ($dates as $date) {
            $this->assertContains($date, $genre->getDates());
        }
        $this->assertCount(count($dates), $genre->getDates());
    }

    public function testIncrements()
    {
        $genre = new Genre();
        $this->assertFalse($genre->getIncrementing());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $genreTraits);
    }
}
