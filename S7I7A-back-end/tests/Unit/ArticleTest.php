<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleTest extends TestCase
{

    protected $rollbackAfterTest = true;

    public function test_articles()
    {
        $doctorRole = Role::where('name', 'Doctor')->first();

        $user = User::factory()->create();
        $user->roles()->attach($doctorRole->id);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/doctor/articles');


        $response->assertStatus(403);
    }
}
