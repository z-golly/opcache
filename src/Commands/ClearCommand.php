<?php


namespace Golly\Opcache\Commands;


use Golly\Opcache\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;

/**
 * Class ClearCommand
 * @package Golly\OpcacheService\Commands
 */
class ClearCommand extends Command
{
    use Client;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'opcache:clear';

    /**
     * @var string
     */
    protected $description = 'Clear opcache';

    /**
     * @throws RequestException
     */
    public function handle()
    {
        $response = $this->get('/api/opcache/clear');
        $response->throw();

        if ($response->json('result')) {
            $this->info('Opcache cleared success!');
            return 0;
        } else {
            $this->error('Opcache not configured!');
            return 2;
        }
    }
}
