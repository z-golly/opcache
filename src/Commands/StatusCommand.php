<?php


namespace Golly\Opcache\Commands;


use Golly\Opcache\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;

/**
 * Class ClearCommand
 * @package Golly\OpcacheService\Commands
 */
class StatusCommand extends Command
{
    use Client;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'opcache:status';

    /**
     * @var string
     */
    protected $description = 'Opcache status';

    /**
     * @var array
     */
    protected $targets = [
        'memory_usage',
        'interned_strings_usage',
        'opcache_statistics',
        'preload_statistics',
        'jit'
    ];

    /**
     * @throws RequestException
     */
    public function handle()
    {
        $response = $this->get('/api/opcache/status');
        $response->throw();

        if ($result = $response->json('result')) {
            $this->display($result);
            return 0;
        } else {
            $this->error('Opcache not configured!');
            return 2;
        }
    }

    /**
     * @param array $data
     */
    public function display(array $data)
    {
        $general = $data;
        foreach ($this->targets as $target) {
            unset($general[$target]);
        }

        $this->table([], $this->parseTable($general));
        if (isset($data['memory_usage'])) {
            $this->line('memory usage:');
            $this->table([], $this->parseTable($data['memory_usage']));
        }

        if (isset($data['opcache_statistics'])) {
            $this->line('statistics:');
            $this->table([], $this->parseTable($data['opcache_statistics']));
        }

        if (isset($data['interned_strings_usage'])) {
            $this->line('interned strings usage:');
            $this->table([], $this->parseTable($data['interned_strings_usage']));
        }

        if (isset($data['preload_statistics'])) {
            $this->line('preload statistics:');
            $this->table([], $this->parseTable($data['preload_statistics']));
        }

        if (isset($data['jit'])) {
            $this->line('jit:');
            $this->table([], $this->parseTable($data['jit']));
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
        $bytes = ['used_memory', 'free_memory', 'wasted_memory', 'buffer_size'];
        $times = ['start_time', 'last_restart_time'];

        return array_map(function ($key, $value) use ($bytes, $times) {
            if (in_array($key, $bytes)) {
                $value = number_format($value / 1048576, 2) . ' MB';
            } elseif (in_array($key, $times)) {
                $value = date('Y-m-d H:i:s', $value);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            return [
                'key' => $key,
                'value' => is_array($value) ? implode(PHP_EOL, $value) : $value,
            ];
        }, array_keys($input), $input);
    }
}
