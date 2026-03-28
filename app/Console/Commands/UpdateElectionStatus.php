<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Election;
use Carbon\Carbon;

class UpdateElectionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-election-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        Election::where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->update(['status' => 'active']);

        Election::where('end_at', '<', $now)
            ->update(['status' => 'ended']);

        Election::where('start_at', '>', $now)
            ->update(['status' => 'upcoming']);
    }
}
