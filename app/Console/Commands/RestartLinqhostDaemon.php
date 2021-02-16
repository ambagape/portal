<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RestartLinqhostDaemon extends Command
{
    protected $signature = 'linqhost:restart-daemon {server} {password} {daemon}';

    protected $description = 'Restarts given Supervisor daemon at the LinQhost server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $username = 'hpw';
        $password = $this->argument('password');
        $server = $this->argument('server');
        $daemonName = $this->argument('daemon');

        $baseUrl = 'https://' . $server . '.host-ed.eu';
        $supervisorUrl = '/lh_supervisor/';

        $stopUrl = $supervisorUrl . 'control/stop/localhost/' . $daemonName;
        $startUrl = $supervisorUrl . 'control/start/localhost/' . $daemonName;

        /** @var \Illuminate\Http\Client\Response */
        $response = Http::withBasicAuth($username, $password)->post($baseUrl . $supervisorUrl);

        if ($response->status() === 200) {
            /** @var \Illuminate\Http\Client\Response */
            $stopResponse = Http::withBasicAuth($username, $password)->post($baseUrl . $stopUrl);

            if ($stopResponse->status() === 200) {
                $this->info('Daemon [' . $daemonName . '] stopped');

                /** @var \Illuminate\Http\Client\Response */
                $startResponse = Http::withBasicAuth($username, $password)->post($baseUrl . $startUrl);

                if ($startResponse->status() === 200) {
                    $this->info('Daemon [' . $daemonName . '] started');
                } else {
                    $this->error('Daemon start failed');
                }
            } else {
                $this->error('Daemon stop failed');
            }
        }
    }
}
