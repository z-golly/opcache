<?php


namespace Golly\Opcache\Commands;


use Golly\Opcache\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;

/**
 * Class ConfigCommand
 * @package Golly\Opcache\Commands
 */
class ConfigCommand extends Command
{
    use Client;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'opcache:config';

    /**
     * @var string
     */
    protected $description = 'Opcache configuration';

    /**
     * @throws RequestException
     */
    public function handle()
    {
        $response = $this->get('/api/opcache/config');
        $response->throw();

        if ($result = $response->json('result')) {
            $this->line('version info:');
            $this->table([], $this->parseTable($result['version']));
            // directives
            $this->line('configuration info:');
            $this->table([], $this->parseTable($result['directives']));
            return 0;
        } else {
            $this->error('Opcache not configured!');
            return 2;
        }
    }

    /**
     * Make up the table for console display.
     *
     * @param $input
     *
     * @return array
     */
    protected function parseTable($input)
    {
        $input = (array)$input;

        return array_map(function ($key, $value) {
            $bytes = ['opcache.memory_consumption'];
            if (in_array($key, $bytes)) {
                $value = number_format($value / 1048576, 2) . ' MB';
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            return [
                'key' => $key,
                'value' => $value,
            ];
        }, array_keys($input), $input);
    }
}
