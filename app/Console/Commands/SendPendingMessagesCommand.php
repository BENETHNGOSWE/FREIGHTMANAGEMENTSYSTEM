<?php

namespace App\Console\Commands;

use App\Models\Message;

use Illuminate\Console\Command;

class SendPendingMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:send-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pending messages in batches of 20';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = 20;
        
        $pendingMessages = Message::where('status', 'pending')
            ->limit($batchSize)
            ->get();
            
        if ($pendingMessages->isEmpty()) {
            $this->info('No pending messages to send.');
            return;
        }
        
        $this->info("Processing {$pendingMessages->count()} pending messages...");
        
        $success = 0;
        $failed = 0;
        
        foreach ($pendingMessages as $message) {
            $response = sendMessageLeadPending(
                $message->customer_number,
                $message->content,
                $message->sender_id
            );
            
            // Update message status based on response
            $message->status = $response['success'] ? 'delivered' : 'failed';
            $message->save();
            
            if ($response['success']) {
                $success++;
            } else {
                $failed++;
            }
        }
        
        $this->info("Processed {$pendingMessages->count()} messages. Success: $success, Failed: $failed");
    }

}
