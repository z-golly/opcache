<?php


namespace Golly\Opcache\Commands;


use Golly\Opcache\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;

/**
 * Class CompileCommand
 * @package Golly\Opcache\Commands
 */
class CompileCommand extends Command
{
    use Client;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'opcache:compile {--force}';

    /**
     * @var string
     */
    protected $description = 'Compile opcache';

    /**
     * @throws RequestException
     */
    public function handle()
    {
        $response = $this->get('/api/opcache/compile', [
            'force' => $this->option('force') ?? false
        ]);
        $response->throw();

        if ($result = $response->json('result')) {
            dd($result);
            if (isset($result['message'])) {
                $this->warn($result['message']);
                return 1;
            } else {
                $this->info(sprintf(
                    '%s of %s files compiled',
                    $response['result']['compiled_count'],
                    $response['result']['total_files_count']
                ));
                return 0;
            }

        } else {
            $this->error('Opcache not configured!');
            return 2;
        }
    }
}
