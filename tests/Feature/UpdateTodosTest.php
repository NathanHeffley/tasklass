<?php

namespace Tests\Feature;

use App\Todo;
use App\User;
use Tests\TestCase;
use PHPUnit\Framework\Assert;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTodosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), 'Failed asserting that the collection contains the specified value.');
        });

        Collection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), 'Failed asserting that the collection does not contain the specified value.');
        });
    }

    /** @test */
    public function completingATodo()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $todo = factory(Todo::class)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        $response = $this->actingAs($user)->put("/todos/{$todo->id}", [
            'completed' => true,
        ]);

        $response->assertStatus(200);

        // Assert that the server responds with success data
        $this->assertTrue($response->original['todo']->completed, 'Failed asserting that the todo is completed.');

        // Assert that the todo was updated in the database
        tap(Todo::first(), function ($todo) use ($user) {
            $this->assertTrue($todo->user->is($user));
            $this->assertTrue($todo->completed, 'Failed asserting that the todo is completed.');
        });
    }

    /** @test */
    public function markingATodoIncomplete()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $todo = factory(Todo::class)->create([
            'user_id' => $user->id,
            'completed' => true,
        ]);

        $response = $this->actingAs($user)->put("/todos/{$todo->id}", [
            'completed' => false,
        ]);

        $response->assertStatus(200);

        // Assert that the server responds with success data
        $this->assertFalse($response->original['todo']->completed, 'Failed asserting that the todo is incomplete.');

        // Assert that the todo was updated in the database
        tap(Todo::first(), function ($todo) use ($user) {
            $this->assertTrue($todo->user->is($user));
            $this->assertFalse($todo->completed, 'Failed asserting that the todo is incomplete.');
        });
    }

    /** @test */
    public function renamingTodo()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $todo = factory(Todo::class)->create([
            'user_id' => $user->id,
            'name' => 'Old Name Todo',
        ]);

        $response = $this->actingAs($user)->put("/todos/{$todo->id}", [
            'name' => 'Renamed Todo',
        ]);

        $response->assertStatus(200);

        // Assert that the server responds with success data
        $this->assertEquals('Renamed Todo', $response->original['todo']->name);

        // Assert that the todo was updated in the database
        tap(Todo::first(), function ($todo) use ($user) {
            $this->assertTrue($todo->user->is($user));
            $this->assertEquals('Renamed Todo', $todo->name);
        });
    }

    /** @test */
    public function archivingTodo()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $todo = factory(Todo::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete("/todos/{$todo->id}");

        $response->assertStatus(200);

        Todo::all()->assertNotContains($todo);
        Todo::archived()->assertContains($todo);
    }

    /**
     * @test
     * @expectedException App\Exceptions\AlreadyArchivedException
     */
    public function archivingAlreadyArchivedTodoThrowsAlreadyArchivedException()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $todo = factory(Todo::class)->states('archived')->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete("/todos/{$todo->id}");
    }

    /**
     * @test
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function archivingNonexistantTodoThrowsNotFoundException()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->delete('/todos/1');
    }
}
