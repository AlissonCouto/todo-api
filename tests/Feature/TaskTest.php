<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_user_can_create_task()
    {
        $user = $this->authenticate();

        $payload = [
            'title' => 'Test Task',
            'description' => 'Descrição teste'
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'ok' => true,
                'message' => 'Tarefa criada com sucesso',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id
        ]);
    }

    public function test_user_can_list_own_tasks()
    {
        $user = $this->authenticate();

        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'message' => 'Tarefas retornadas com sucesso'
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_update_task_status()
    {
        $user = $this->authenticate();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'in_progress'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'message' => 'Tarefa atualizada com sucesso'
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress'
        ]);
    }

    public function test_user_can_delete_task()
    {
        $user = $this->authenticate();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'message' => 'Tarefa deletada com sucesso'
            ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_can_filter_tasks_by_status()
    {
        $user = $this->authenticate();

        Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        Task::factory()->create(['user_id' => $user->id, 'status' => 'in_progress']);
        Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $response = $this->getJson('/api/tasks/status/pending');

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'message' => 'Tarefas retornadas com sucesso'
            ])
            ->assertJsonCount(2, 'data');
    }

    // Cenários de erro
    public function test_cannot_update_task_status_of_another_user()
    {
        $this->authenticate();

        // Criando uma tarefa de outro usuário
        $otherUserTask = Task::factory()->create();

        $response = $this->patchJson("/api/tasks/{$otherUserTask->id}/status", [
            'status' => 'done'
        ]);

        $response->assertStatus(404);
    }

    public function test_cannot_delete_task_of_another_user()
    {
        $this->authenticate();

        // Criando uma tarefa de outro usuário
        $otherUserTask = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$otherUserTask->id}");

        $response->assertStatus(404);
    }
}
