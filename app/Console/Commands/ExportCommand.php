<?php

namespace App\Console\Commands;

use App\Models\Process;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export {job_id}';

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
            $process = $this->getProcessFromJobId($jobId);
            $exporter = $this->getExporterFromJobId($jobId, $process);

            $data = $process->data;
            $context = ['class' => $exporter::class, 'user_id' => $data['user_id']];

            Log::info("Exporting " . ucfirst($data['entity']) . "s ...", $context);
            $this->info("\nExporting " . ucfirst($data['entity']) . "s ...\n");

            $filename = '/process/' . $jobId . '/' . ucfirst($data['entity']) . 's.xlsx';

            $exporter->store($filename);

            $data['url'] = Storage::url($filename);
            $data['status'] = 'done';

            $process->update([
                'data' => $data,
            ]);
            Log::info('Done', $context);
            $this->info("Done.\n");
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

    protected function getProcessFromJobId(mixed $jobId)
    {
        $process = Process::where('job_id', '=', $jobId)->first();
        if (!$process)
            throw new \Exception("There is not process " . $jobId);
        return $process;
    }

    protected function getExporterFromJobId(mixed $jobId, mixed $process)
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
        $exporterClass = "App\Exports\\" . ucfirst($data['entity']) . "sExport";
        $exporter = new $exporterClass(
            $data['mapping'],
            $data['skipFirstRow'] == "true" ? true : false,
            $data['match'],
            $data['user_id'],
            $jobId
        );
        return $exporter;
    }
}
