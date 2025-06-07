<?php

namespace App\Console\Commands;

use App\Models\Process;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import {job_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $context = [];
        $process = new Process(['data' => []]);
        try {
            $jobId = $this->argument('job_id');
            $file = $this->getFileFromJobId($jobId);
            $process = $this->getProcessFromJobId($jobId);
            $importer = $this->getImporterFromJobId($jobId, $process);

            $data = $process->data;
            $context = ['class' => $importer::class, 'user_id' => $data['user_id']];

            Log::info("Importing file " . $file . " ...", $context);
            $this->info("\nImporting file" . $file . " ...\n");

            $importer->withOutput($this->output)->import($file);

            $data['status'] = 'done';
            $process->update([
                'data' => $data,
            ]);
            Log::info('Done', $context);
            $this->info("Done.\n");
            unlink($file);
        } catch (\Exception $e) {

            $data = $process->data;
            $data['status'] = 'error';
            $data['trace'] = $e->getTraceAsString();
            $data['message'] = $e->getMessage();

            $process->update([
                'data' => $data,
            ]);
            Log::error($e->getMessage(), $context);
            $this->error("\n" . $e->getMessage() . "\n");
            throw new \Exception($e->getMessage());
        }
    }

    protected function getFileFromJobId(mixed $jobId)
    {
        $path = '/product/import';
        $file = Storage::path($path . "/" . $jobId . ".xlsx");
        if (!file_exists($file))
            throw new \Exception("There is not file " . $file);
        return $file;
    }

    protected function getProcessFromJobId(mixed $jobId)
    {
        $process = Process::where('job_id', '=', $jobId)->first();
        if (!$process)
            throw new \Exception("There is not process " . $jobId);
        return $process;
    }

    protected function getImporterFromJobId(mixed $jobId, mixed $process)
    {
        $data = $process->data;
        if (!$data || !key_exists('entity', $data))
            throw new \Exception("There is not entity in " . $jobId);
        if (!key_exists('mapping', $data))
            throw new \Exception("There is not mapping in " . $jobId);
        if (!key_exists('skipFirstRow', $data))
            throw new \Exception("There is not skip data in " . $jobId);
        if (!key_exists('match', $data))
            throw new \Exception("There is not match data in " . $jobId);
        $importerClass = "App\Imports\\" . ucfirst($data['entity']) . "sImport";
        $importer = new $importerClass(
            $data['mapping'],
            $data['skipFirstRow'] == "true" ? true : false,
            $data['match'],
            $data['user_id'],
            $jobId
        );
        return $importer;
    }
}
