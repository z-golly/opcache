<?php


namespace Golly\Opcache\Commands;


use Golly\Opcache\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;

/**
 * Class ResetCommand
 * @package Golly\Opcache\Commands
 */
class ResetCommand extends Command
{
    use Client;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'opcache:reset';

    /**
     * @var string
     */
    protected $description = 'Reset opcache';

    /**
     * @return int
     * @throws RequestException
     */
    public function handle()
    {
        $response = $this->get('/api/opcache/reset');
        $response->throw();

        if ($response->json('result')) {
            $this->info('Opcache reset success!');
            return 0;
        } else {
            $this->error('Opcache not configured!');
            return 2;
        }
    }
}
