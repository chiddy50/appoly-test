<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CheckDataHourlyJob;

class CheckDataHourlyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:data:hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check data hourly';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CheckDataHourlyJob::dispatch();
        return Command::SUCCESS;
    }
}
