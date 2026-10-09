<?php

namespace App\Jobs;

use App\Actions\SendAiResponseAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class SendAiResponseJob implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Collection $incomingMessages) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(SendAiResponseAction::class)->execute($this->incomingMessages);
    }
}
