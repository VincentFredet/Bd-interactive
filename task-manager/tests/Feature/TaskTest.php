<?php

namespace Tests\Feature;

use App\Models\Context;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Context $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->context = Context::create(['name' => 'Test Context']);

        // Fake storage for image uploads
        Storage::fake('public');
    }

    /** @test */
    public function it_can_display_tasks_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.index');
        $response->assertViewHas(['tasks', 'contexts', 'users']);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'todo',
            'priority' => 'high',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
            'due_date' => Carbon::tomorrow()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Tâche créée avec succès!');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'todo',
            'priority' => 'high',
        ]);
    }

    /** @test */
    public function it_requires_title_when_creating_task()
    {
        $taskData = [
            'description' => 'Test Description',
            'status' => 'todo',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function it_validates_status_enum()
    {
        $taskData = [
            'title' => 'Test Task',
            'status' => 'invalid_status',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function it_validates_priority_enum()
    {
        $taskData = [
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'invalid_priority',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertSessionHasErrors('priority');
    }

    /** @test */
    public function it_can_upload_image_when_creating_task()
    {
        $image = UploadedFile::fake()->image('task.jpg', 800, 600);

        $taskData = [
            'title' => 'Test Task with Image',
            'status' => 'todo',
            'priority' => 'high',
            'image' => $image,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));

        $task = Task::where('title', 'Test Task with Image')->first();
        $this->assertNotNull($task->image);
        Storage::disk('public')->assertExists('tasks/' . $task->image);
    }

    /** @test */
    public function it_validates_image_type_and_size()
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $taskData = [
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'high',
            'image' => $invalidFile,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertSessionHasErrors('image');
    }

    /** @test */
    public function it_can_update_a_task()
    {
        $task = Task::create([
            'title' => 'Original Title',
            'status' => 'todo',
            'priority' => 'low',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'in_progress',
            'priority' => 'urgent',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('tasks.update', $task), $updateData);

        $response->assertRedirect(route('tasks.index'));

        $task->refresh();
        $this->assertEquals('Updated Title', $task->title);
        $this->assertEquals('Updated Description', $task->description);
        $this->assertEquals('in_progress', $task->status);
        $this->assertEquals('urgent', $task->priority);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $task = Task::create([
            'title' => 'Task to Delete',
            'status' => 'todo',
            'priority' => 'low',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_deletes_image_when_deleting_task()
    {
        $image = UploadedFile::fake()->image('task.jpg');

        $task = Task::create([
            'title' => 'Task with Image',
            'status' => 'todo',
            'priority' => 'low',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        // Manually store an image
        $imageName = time() . '.jpg';
        Storage::disk('public')->put('tasks/' . $imageName, $image->getContent());
        $task->update(['image' => $imageName]);

        Storage::disk('public')->assertExists('tasks/' . $imageName);

        $this->actingAs($this->user)
            ->delete(route('tasks.destroy', $task));

        Storage::disk('public')->assertMissing('tasks/' . $imageName);
    }

    /** @test */
    public function it_can_update_task_status_via_ajax()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('tasks.update-status', $task), [
                'status' => 'done',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $task->refresh();
        $this->assertEquals('done', $task->status);
    }

    /** @test */
    public function it_can_mark_task_as_completed()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'status' => 'in_progress',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('tasks.complete', $task));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $task->refresh();
        $this->assertEquals('done', $task->status);
        $this->assertNotNull($task->completed_at);
    }

    /** @test */
    public function it_can_postpone_a_task()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::today(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $newDate = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->patchJson(route('tasks.postpone', $task), [
                'date' => $newDate,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $task->refresh();
        $this->assertEquals($newDate, $task->due_date->format('Y-m-d'));
    }

    /** @test */
    public function it_cannot_postpone_to_past_date()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::today(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('tasks.postpone', $task), [
                'date' => Carbon::yesterday()->format('Y-m-d'),
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('date');
    }

    /** @test */
    public function it_can_filter_tasks_by_week()
    {
        $weekStart = Task::getWeekStart();
        $nextWeek = $weekStart->copy()->addWeek();

        // Create tasks for this week
        Task::create([
            'title' => 'This Week Task',
            'status' => 'todo',
            'priority' => 'medium',
            'week_date' => $weekStart,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        // Create task for next week
        Task::create([
            'title' => 'Next Week Task',
            'status' => 'todo',
            'priority' => 'medium',
            'week_date' => $nextWeek,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index', ['week' => $weekStart->format('Y-m-d')]));

        $response->assertStatus(200);
        $response->assertSee('This Week Task');
        $response->assertDontSee('Next Week Task');
    }

    /** @test */
    public function it_can_filter_tasks_by_context()
    {
        $otherContext = Context::create(['name' => 'Other Context']);

        Task::create([
            'title' => 'Task in First Context',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        Task::create([
            'title' => 'Task in Other Context',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $otherContext->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index', ['context' => $this->context->id]));

        $response->assertStatus(200);
        $response->assertSee('Task in First Context');
        $response->assertDontSee('Task in Other Context');
    }

    /** @test */
    public function it_displays_daily_view()
    {
        $today = Carbon::today();

        Task::create([
            'title' => 'Today Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => $today,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.daily'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.daily');
        $response->assertSee('Today Task');
    }

    /** @test */
    public function it_shows_overdue_tasks_in_daily_view()
    {
        $yesterday = Carbon::yesterday();

        Task::create([
            'title' => 'Overdue Task',
            'status' => 'todo',
            'priority' => 'urgent',
            'due_date' => $yesterday,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.daily'));

        $response->assertStatus(200);
        $response->assertSee('Overdue Task');

        $overdueTasks = $response->viewData('overdueTasks');
        $this->assertCount(1, $overdueTasks);
    }

    /** @test */
    public function it_automatically_sets_week_date_on_task_creation()
    {
        $task = Task::create([
            'title' => 'Auto Week Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertNotNull($task->week_date);
        $this->assertEquals(Task::getWeekStart()->format('Y-m-d'), $task->week_date->format('Y-m-d'));
    }

    /** @test */
    public function it_automatically_sets_due_date_to_today_on_task_creation()
    {
        $task = Task::create([
            'title' => 'Auto Due Date Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertNotNull($task->due_date);
        $this->assertEquals(Carbon::today()->format('Y-m-d'), $task->due_date->format('Y-m-d'));
    }

    /** @test */
    public function guests_cannot_access_tasks()
    {
        $response = $this->get(route('tasks.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_calculates_week_statistics_correctly()
    {
        Task::create([
            'title' => 'Todo Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        Task::create([
            'title' => 'In Progress Task',
            'status' => 'in_progress',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        Task::create([
            'title' => 'Done Task',
            'status' => 'done',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index'));

        $response->assertStatus(200);

        $weekStats = $response->viewData('weekStats');
        $this->assertEquals(3, $weekStats['total']);
        $this->assertEquals(1, $weekStats['todo']);
        $this->assertEquals(1, $weekStats['in_progress']);
        $this->assertEquals(1, $weekStats['done']);
    }
}
