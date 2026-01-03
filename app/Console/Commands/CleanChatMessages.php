<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;

class CleanChatMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surfer:clean-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove OPENROUTER PROCESSING noise from messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning messages...');

        $messages = Message::where('content', 'like', '%OPENROUTER PROCESSING%')->get();

        $count = $messages->count();
        $this->info("Found {$count} corrupted messages.");

        $bar = $this->output->createProgressBar($count);

        foreach ($messages as $message) {
            // Remove the specific noise string and any preceding colons/spaces if they were part of the noise
            // Pattern: ": OPENROUTER PROCESSING" or just "OPENROUTER PROCESSING" repeated
            
            $content = $message->content;
            
            // Aggressive cleaning of the specific string
            $content = str_replace(': OPENROUTER PROCESSING', '', $content);
            $content = str_replace('OPENROUTER PROCESSING', '', $content);
            
            // Clean up any double colons or leading debris left behind if necessary
            // (Standardizing content generally)
            
            if (is_string($content)) {
                 $message->content = trim($content);
                 $message->save();
            } elseif (is_array($content)) {
                 // If content is JSON/array, we might need to look inside, but based on the bug report it looks like string corruption on raw text.
                 // Assuming standard text messages for now.
                 $this->warn("Skipping array content for message ID {$message->id}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Cleanup complete!');
    }
}
