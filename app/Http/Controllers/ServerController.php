<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ServerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $chartDisk = app()->chartjs
            ->name('chartDisk')
            ->type('doughnut')
            ->size(['width' => 300, 'height' => 300])
            ->labels([__('Free'), __('Used')])
            ->datasets([
                [
                    'backgroundColor' => ['rgb(234, 84, 85)', 'rgb(255, 255, 255)'],
                    'data' => [$this->disk_stat()["used"], ($this->disk_stat()["total"] - $this->disk_stat()["used"])]
                ]
            ])
            ->optionsRaw("{
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(t, d) {
                            console.log(d);
                            t.yLabel = d.datasets[0].data[t.index];
                            var yLabel = t.yLabel >= 1000 ?
                            t.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') : t.yLabel;
                            return d.labels[t.index] + ' ' + yLabel + 'GB';
                        }
                    },
                }
            }");

        $chartRam = app()->chartjs
            ->name('chartRam')
            ->type('doughnut')
            ->size(['width' => 300, 'height' => 300])
            ->labels([__('Free'), __('Used')])
            ->datasets([
                [
                    'backgroundColor' => ['rgb(234, 84, 85)', 'rgb(255, 255, 255)'],
                    'data' => [$this->ram_stat()["used"], ($this->ram_stat()["total"] - $this->ram_stat()["used"])]
                ]
            ])
            ->optionsRaw("{
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(t, d) {
                            console.log(d);
                            t.yLabel = d.datasets[0].data[t.index];
                            var yLabel = t.yLabel >= 1000 ?
                            t.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') : t.yLabel;
                            return d.labels[t.index] + ' ' + yLabel + 'GB';
                        }
                    },
                }
            }");


        $chartCpu = app()->chartjs
            ->name('chartCpu')
            ->type('doughnut')
            ->size(['width' => 300, 'height' => 300])
            ->labels([__('Free'), __('Used')])
            ->datasets([
                [
                    'backgroundColor' => ['rgb(234, 84, 85)', 'rgb(255, 255, 255)'],
                    'data' => [$this->cpu_stat(), (1 - $this->cpu_stat())]
                ]
            ])
            ->optionsRaw("{
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(t, d) {
                            console.log(d);
                            t.yLabel = d.datasets[0].data[t.index];
                            var yLabel = t.yLabel >= 1000 ?
                            t.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') : t.yLabel;
                            return d.labels[t.index] + ' ' + yLabel;
                        }
                    },
                }
            }");
        $services['apache2'] = $this->service_status('apache2');
        $services['mysql'] = $this->checkDatabase();
        $services['public_ip'] = $this->public_ip();
        $services['internal_ip'] = $_SERVER['SERVER_ADDR'];
        $services['hostname'] = gethostname();
        $valuesPerMinute = $this->values_per_minute();
        $uptime = $this->last_boot_time();
        $ssl = $this->get_https();

        return view('server', compact('chartDisk', 'chartRam', 'chartCpu', 'services', 'valuesPerMinute', 'uptime', 'ssl'));
    }

    /**
     * @return array|int[]
     */
    private function ram_stat(): array
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return [
                "used" => 0,
                "total" => 0,
            ];
        }

        //RAM usage
        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $usedmem = $mem[2];
        $usedmemInGB = number_format($usedmem / 1048576, 2);
        $memory1 = $mem[2] / $mem[1] * 100;
        $memory = round($memory1) . '%';
        $fh = fopen('/proc/meminfo', 'r');
        $mem = 0;
        while ($line = fgets($fh)) {
            $pieces = array();
            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                $mem = $pieces[1];
                break;
            }
        }
        fclose($fh);
        $totalram = number_format($mem / 1048576, 2);
        return [
            "used" => $usedmemInGB,
            "total" => $totalram,
        ];
    }

    /**
     * @return int|mixed
     */
    private function cpu_stat()
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return 0;
        }

        //cpu usage
        $cpu_load = sys_getloadavg();
        return $cpu_load[0] ?? 0;
    }

    /**
     * @param string $service_name
     * @return bool
     */
    private function service_status(string $service_name): bool
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return 0;
        }

        //service
        $serviceStatus = shell_exec('sudo service ' . $service_name . ' status');
        $serviceStatus = (string)trim($serviceStatus);
        $service_arr = explode("\n", $serviceStatus);

        if (isset($service_arr[2])) {
            $status = explode(" ", $service_arr[2]);
            return (array_key_exists(6, $status) ? ($status[6] == "active" ? true : false) : false);
        }

        $status = array_values(array_filter(explode(' ', $serviceStatus)));

        if (isset($status[1]) && $status[1] === 'RUNNING') {
            return true;
        }

        return 0;

    }

    /**
     * @return bool
     */
    private function checkDatabase(): bool
    {
        try {
            DB::select('SHOW TABLES;');
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * @return array|int[]
     */
    private function disk_stat(): array
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return [
                "used" => 0,
                "total" => 0,
            ];
        }

        $dt = round(disk_total_space("/var/www") / 1024 / 1024 / 1024);
        $df = round(disk_free_space("/var/www") / 1024 / 1024 / 1024);
        return [
            "used" => ($dt - $df),
            "total" => $dt,
        ];
    }


    /**
     * @return string|null
     */
    private function public_ip(): ?string
    {
        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, 'https://api.ipify.org/?format=json');
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $phoneList = curl_exec($cURLConnection);
        $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
        curl_close($cURLConnection);
        $jsonArrayResponse = json_decode($phoneList);
        if ($httpcode != 200) {
            return false;
        }
        return $jsonArrayResponse->ip;
    }

    /**
     * @return int
     */
    private function values_per_minute()
    {
        return (DB::table('records')
            ->where(
                'created_at',
                '>',
                Carbon::now()->subMinutes(1)->toDateTimeString()
            ))->count();
    }

    /**
     * @return int|string
     */
    private function last_boot_time()
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return 0;
        }

        $str = @file_get_contents('/proc/uptime');
        $num = (float)$str;
        $now = CarbonImmutable::now()->change('- ' . (int)round($num, 0) . ' seconds');
        return Carbon::parse($now)->diffForHumans();
    }

    /**
     * @return bool
     */
    private function get_https(): bool
    {
        return !(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on');
    }
}
