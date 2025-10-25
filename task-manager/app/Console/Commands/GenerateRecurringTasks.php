<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:generate-recurring {--days=7 : Number of days in advance to generate tasks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate instances of recurring tasks for the specified period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $endDate = Carbon::today()->addDays($days);

        $this->info("Generating recurring tasks up to {$endDate->format('Y-m-d')}...");

        $recurringTasks = Task::recurringTemplates()->get();

        if ($recurringTasks->isEmpty()) {
            $this->info('No recurring tasks found.');
            return Command::SUCCESS;
        }

        $totalGenerated = 0;

        foreach ($recurringTasks as $task) {
            $generated = 0;

            // Continue generating instances until we reach the end date
            while (true) {
                $nextDate = $task->getNextOccurrenceDate();

                if (!$nextDate || $nextDate->isAfter($endDate)) {
                    break;
                }

                // Check if an instance already exists for this date
                $existingInstance = Task::where('recurrence_parent_id', $task->id)
                    ->whereDate('due_date', $nextDate->format('Y-m-d'))
                    ->first();

                if (!$existingInstance) {
                    $instance = $task->generateNextInstance();
                    if ($instance) {
                        $generated++;
                        $this->line("  ✓ Generated instance for '{$task->title}' on {$nextDate->format('Y-m-d')}");
                    }
                } else {
                    // Update last_generated_at even if instance exists
                    $task->update(['last_generated_at' => Carbon::now()]);
                    break; // Stop if we found an existing instance
                }
            }

            if ($generated > 0) {
                $totalGenerated += $generated;
                $this->info("Generated {$generated} instance(s) for: {$task->title}");
            }
        }

        if ($totalGenerated > 0) {
            $this->info("\n✓ Total instances generated: {$totalGenerated}");
        } else {
            $this->info("\nNo new instances needed to be generated.");
        }

        return Command::SUCCESS;
    }
}
