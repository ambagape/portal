<?php


namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishAngular extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:angular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes Angular, moves index.html to resources/views.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $input = resource_path('angular/dist/angular/index.html');
        $output = resource_path('views/angular.blade.php');

        if (!file_exists($input)) {
            throw new Exception("Index.html does not exsist in angular dist, run ng build first.");
        }
        if (file_exists($output)) {
            File::delete($output);
        }

        File::prepend($output, file_get_contents($input));
        File::delete($input);

        File::moveDirectory(
            resource_path('angular/dist/angular'),
            base_path('public'),
            true
        );

        $this->info('Published Angular!');
    }
}
