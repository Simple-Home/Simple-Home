<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Illuminate\Support\Facades\Storage;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request, FormBuilder $formBuilder)
    {
        $logsAll = $this->logFile();
        if (!empty ($logsAll)) {
            $logs = $logsAll[0];
            asort($logs);
        }

        if (!empty ($request->input('delete'))) {
            unlink (storage_path('logs/' . $logs[$request->input('logFile')]));
            $logsAll = $this->logFile();
            if (!empty ($logsAll)) {
                $logs = $logsAll[0];
            }
            asort($logs);
            $request->request->remove('logFile');
            $request->request->remove('delete');
        }

        $logForm = $formBuilder->create(\App\Forms\LogForm::class, [
            'model' => (!empty($request->input('logFile')) ? $request->input('logFile') : ""),
            'method' => 'POST',
            'url' => route('system.logs'),
        ], ['logFiles' => (!empty($logsAll[1]) ? $logsAll[1] : array())]);

        $content = "";
        if ($request->input('logFile')) {
            $actualLogFilePath = storage_path('logs/' . $logs[$request->input('logFile')]);
            if ($actualLogFilePath != null && file_exists($actualLogFilePath)) {
                $content = file_get_contents($actualLogFilePath);
            }
        }

        $logsStats = $this->getStats();

        return view('system.logs.list', compact('logs', 'logsStats', 'logForm', 'content'));
    }

    private function logFile()
    {
        $result = array();
        $dir = storage_path('logs/');
        $logFiles = scandir($dir);
        foreach ($logFiles as $key => $file) {
            if (in_array($file, array(".", "..", ".gitkeep", ".gitignore"))) {
                continue;
            }
            $result[0][] = $file;
            $result[1][] = $file . " - " . $this->bytesToHuman(filesize($dir . $file));
        }
        return $result;
    }

    private function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function getStats()
    {
        $stats = array(
            'ERROR' => 0,
            'WARNING' => 0,
            'EXEPTION' => 0,
            'INFO' => 0,
        );

        $actualLogFilePath = storage_path('logs/laravel-' . date("Y-m-d") . ".log");
        if (!file_exists($actualLogFilePath)) return $stats;

        $content = file_get_contents($actualLogFilePath);

        $matches = array();
        $re = '/ERROR|WARNING|INFO/';
        preg_match_all($re, $content, $matches, PREG_PATTERN_ORDER);


        if (count($matches[0]) == 0) return $stats;

        foreach ($matches[0] as $match) {
            switch ($match) {
                case 'ERROR':
                    $stats['ERROR']++;
                    break;
                case 'WARNING':
                    $stats['WARNING']++;
                    break;
                case 'EXEPTION':
                    $stats['EXEPTION']++;
                    break;
                default:
                    $stats['INFO']++;
                    break;
            }
        }

        return $stats;
    }
}
