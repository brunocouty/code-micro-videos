<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{

    public function testFillableAttribute()
    {
        $fillable = [
            'name',
            'description',
            'is_active',
        ];
        $category = new Category();
        $this->assertEquals($fillable, $category->getFillable());
    }

    public function testCastsAttributes()
    {
        $casts = [
            'id' => 'string',
            'is_active' => 'boolean'
        ];
        $category = new Category();
        $this->assertEquals($casts, $category->getCasts());
    }

    public function testDatesAttributes()
    {
        $dates = [
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $category = new Category();
        foreach ($dates as $date) {
            $this->assertContains($date, $category->getDates());
        }
        $this->assertCount(count($dates), $category->getDates());
    }

    public function testIncrements()
    {
        $category = new Category();
        $this->assertFalse($category->getIncrementing());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }
}
