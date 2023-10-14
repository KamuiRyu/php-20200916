<?php

namespace App\Console\Commands;

use App\Http\Controllers\ProductController;
use Illuminate\Console\Command;

class syncproducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:syncproducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza os produtos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('schedule:run');
    }
}
