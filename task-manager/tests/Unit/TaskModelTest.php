<?php

namespace Tests\Unit;

use App\Models\Context;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Context $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->context = Context::create(['name' => 'Test Context']);
    }

    /** @test */
    public function it_belongs_to_a_context()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(Context::class, $task->context);
        $this->assertEquals($this->context->id, $task->context->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($this->user->id, $task->user->id);
    }

    /** @test */
    public function it_casts_dates_correctly()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'medium',
            'week_date' => '2025-10-20',
            'due_date' => '2025-10-25',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(Carbon::class, $task->week_date);
        $this->assertInstanceOf(Carbon::class, $task->due_date);
    }

    /** @test */
    public function it_returns_correct_priority_badge_class()
    {
        $lowTask = Task::create([
            'title' => 'Low Priority',
            'status' => 'todo',
            'priority' => 'low',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $mediumTask = Task::create([
            'title' => 'Medium Priority',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $highTask = Task::create([
            'title' => 'High Priority',
            'status' => 'todo',
            'priority' => 'high',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $urgentTask = Task::create([
            'title' => 'Urgent Priority',
            'status' => 'todo',
            'priority' => 'urgent',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('bg-gray-100 text-gray-800', $lowTask->priority_badge_class);
        $this->assertEquals('bg-blue-100 text-blue-800', $mediumTask->priority_badge_class);
        $this->assertEquals('bg-yellow-100 text-yellow-800', $highTask->priority_badge_class);
        $this->assertEquals('bg-red-100 text-red-800', $urgentTask->priority_badge_class);
    }

    /** @test */
    public function it_returns_correct_status_badge_class()
    {
        $todoTask = Task::create([
            'title' => 'Todo Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $inProgressTask = Task::create([
            'title' => 'In Progress Task',
            'status' => 'in_progress',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $doneTask = Task::create([
            'title' => 'Done Task',
            'status' => 'done',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('bg-gray-100 text-gray-800', $todoTask->status_badge_class);
        $this->assertEquals('bg-blue-100 text-blue-800', $inProgressTask->status_badge_class);
        $this->assertEquals('bg-green-100 text-green-800', $doneTask->status_badge_class);
    }

    /** @test */
    public function it_calculates_week_start_correctly()
    {
        // Test with a Wednesday
        $wednesday = Carbon::parse('2025-10-22'); // A Wednesday
        $weekStart = Task::getWeekStart($wednesday);

        $this->assertTrue($weekStart->isMonday());
        $this->assertEquals('2025-10-20', $weekStart->format('Y-m-d'));
    }

    /** @test */
    public function it_calculates_week_end_correctly()
    {
        // Test with a Wednesday
        $wednesday = Carbon::parse('2025-10-22'); // A Wednesday
        $weekEnd = Task::getWeekEnd($wednesday);

        $this->assertTrue($weekEnd->isSunday());
        $this->assertEquals('2025-10-26', $weekEnd->format('Y-m-d'));
    }

    /** @test */
    public function scope_for_week_filters_tasks_correctly()
    {
        $weekStart = Task::getWeekStart();
        $nextWeek = $weekStart->copy()->addWeek();

        Task::create([
            'title' => 'This Week',
            'status' => 'todo',
            'priority' => 'medium',
            'week_date' => $weekStart,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        Task::create([
            'title' => 'Next Week',
            'status' => 'todo',
            'priority' => 'medium',
            'week_date' => $nextWeek,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $thisWeekTasks = Task::forWeek($weekStart)->get();

        $this->assertCount(1, $thisWeekTasks);
        $this->assertEquals('This Week', $thisWeekTasks->first()->title);
    }

    /** @test */
    public function scope_for_date_filters_tasks_correctly()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        Task::create([
            'title' => 'Today Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => $today,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        Task::create([
            'title' => 'Tomorrow Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => $tomorrow,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $todayTasks = Task::forDate($today)->get();

        $this->assertCount(1, $todayTasks);
        $this->assertEquals('Today Task', $todayTasks->first()->title);
    }

    /** @test */
    public function scope_today_returns_only_today_tasks()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        Task::create([
            'title' => 'Today Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => $today,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        Task::create([
            'title' => 'Yesterday Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => $yesterday,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $todayTasks = Task::today()->get();

        $this->assertCount(1, $todayTasks);
        $this->assertEquals('Today Task', $todayTasks->first()->title);
    }

    /** @test */
    public function scope_overdue_returns_only_overdue_incomplete_tasks()
    {
        $yesterday = Carbon::yesterday();
        $twoDaysAgo = Carbon::now()->subDays(2);

        // Overdue incomplete task
        Task::create([
            'title' => 'Overdue Task',
            'status' => 'todo',
            'priority' => 'urgent',
            'due_date' => $yesterday,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        // Overdue but completed task (should not appear)
        Task::create([
            'title' => 'Overdue but Done',
            'status' => 'done',
            'priority' => 'medium',
            'due_date' => $twoDaysAgo,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        // Today task (should not appear)
        Task::create([
            'title' => 'Today Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::today(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $overdueTasks = Task::overdue()->get();

        $this->assertCount(1, $overdueTasks);
        $this->assertEquals('Overdue Task', $overdueTasks->first()->title);
    }

    /** @test */
    public function is_overdue_returns_true_for_past_incomplete_tasks()
    {
        $overdueTask = Task::create([
            'title' => 'Overdue Task',
            'status' => 'todo',
            'priority' => 'urgent',
            'due_date' => Carbon::yesterday(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertTrue($overdueTask->is_overdue);
    }

    /** @test */
    public function is_overdue_returns_false_for_completed_past_tasks()
    {
        $completedTask = Task::create([
            'title' => 'Completed Past Task',
            'status' => 'done',
            'priority' => 'medium',
            'due_date' => Carbon::yesterday(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertFalse($completedTask->is_overdue);
    }

    /** @test */
    public function is_overdue_returns_false_for_future_tasks()
    {
        $futureTask = Task::create([
            'title' => 'Future Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::tomorrow(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertFalse($futureTask->is_overdue);
    }

    /** @test */
    public function is_today_returns_true_for_today_tasks()
    {
        $todayTask = Task::create([
            'title' => 'Today Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::today(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertTrue($todayTask->is_today);
    }

    /** @test */
    public function is_today_returns_false_for_non_today_tasks()
    {
        $tomorrowTask = Task::create([
            'title' => 'Tomorrow Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::tomorrow(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertFalse($tomorrowTask->is_today);
    }

    /** @test */
    public function mark_as_completed_updates_status_and_timestamp()
    {
        $task = Task::create([
            'title' => 'Task to Complete',
            'status' => 'in_progress',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertNull($task->completed_at);

        $task->markAsCompleted();

        $this->assertEquals('done', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    /** @test */
    public function postpone_to_updates_due_date()
    {
        $task = Task::create([
            'title' => 'Task to Postpone',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::today(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $newDate = Carbon::parse('+5 days');
        $task->postponeTo($newDate);

        $this->assertEquals($newDate->format('Y-m-d'), $task->fresh()->due_date->format('Y-m-d'));
    }

    /** @test */
    public function postpone_to_resets_completed_status()
    {
        $task = Task::create([
            'title' => 'Completed Task to Postpone',
            'status' => 'done',
            'priority' => 'medium',
            'due_date' => Carbon::yesterday(),
            'completed_at' => Carbon::now(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $task->postponeTo(Carbon::tomorrow());

        $task->refresh();
        $this->assertEquals('todo', $task->status);
        $this->assertNull($task->completed_at);
    }

    /** @test */
    public function postpone_to_tomorrow_sets_tomorrow_as_due_date()
    {
        $task = Task::create([
            'title' => 'Task to Postpone',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::today(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $task->postponeToTomorrow();

        $this->assertEquals(Carbon::tomorrow()->format('Y-m-d'), $task->fresh()->due_date->format('Y-m-d'));
    }

    /** @test */
    public function week_label_shows_this_week_for_current_week()
    {
        $task = Task::create([
            'title' => 'This Week Task',
            'status' => 'todo',
            'priority' => 'medium',
            'week_date' => Task::getWeekStart(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertStringContainsString('Cette semaine', $task->week_label);
    }

    /** @test */
    public function week_label_shows_date_range_for_other_weeks()
    {
        $nextWeek = Task::getWeekStart()->addWeek();

        $task = Task::create([
            'title' => 'Next Week Task',
            'status' => 'todo',
            'priority' => 'medium',
            'week_date' => $nextWeek,
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertStringContainsString('Semaine du', $task->week_label);
    }

    /** @test */
    public function due_date_badge_class_is_red_for_overdue()
    {
        $overdueTask = Task::create([
            'title' => 'Overdue Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::yesterday(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('bg-red-100 text-red-800', $overdueTask->due_date_badge_class);
    }

    /** @test */
    public function due_date_badge_class_is_blue_for_today()
    {
        $todayTask = Task::create([
            'title' => 'Today Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::today(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('bg-blue-100 text-blue-800', $todayTask->due_date_badge_class);
    }

    /** @test */
    public function due_date_badge_class_is_gray_for_future()
    {
        $futureTask = Task::create([
            'title' => 'Future Task',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::tomorrow(),
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('bg-gray-100 text-gray-800', $futureTask->due_date_badge_class);
    }

    /** @test */
    public function boot_method_sets_week_date_automatically()
    {
        $task = Task::create([
            'title' => 'Auto Week Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
            // Not specifying week_date
        ]);

        $this->assertNotNull($task->week_date);
        $this->assertEquals(Task::getWeekStart()->format('Y-m-d'), $task->week_date->format('Y-m-d'));
    }

    /** @test */
    public function boot_method_sets_due_date_to_today_automatically()
    {
        $task = Task::create([
            'title' => 'Auto Due Date Task',
            'status' => 'todo',
            'priority' => 'medium',
            'context_id' => $this->context->id,
            'user_id' => $this->user->id,
            // Not specifying due_date
        ]);

        $this->assertNotNull($task->due_date);
        $this->assertEquals(Carbon::today()->format('Y-m-d'), $task->due_date->format('Y-m-d'));
    }
}
