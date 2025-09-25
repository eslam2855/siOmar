<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessUnitImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    protected $unitId;
    protected $imagePaths;

    /**
     * Create a new job instance.
     */
    public function __construct(int $unitId, array $imagePaths)
    {
        $this->unitId = $unitId;
        $this->imagePaths = $imagePaths;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            foreach ($this->imagePaths as $imagePath) {
                $this->processImage($imagePath);
            }

            Log::info("Unit images processed successfully for unit ID: {$this->unitId}");
        } catch (\Exception $e) {
            Log::error("Failed to process unit images for unit ID: {$this->unitId}", [
                'error' => $e->getMessage(),
                'unit_id' => $this->unitId,
            ]);
            
            throw $e;
        }
    }

    /**
     * Process a single image.
     */
    protected function processImage(string $imagePath): void
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            Log::warning("Image not found: {$imagePath}");
            return;
        }

        // Here you can add image optimization logic
        // For example: resize, compress, create thumbnails, etc.
        
        Log::info("Image processed: {$imagePath}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Unit image processing job failed for unit ID: {$this->unitId}", [
            'error' => $exception->getMessage(),
            'unit_id' => $this->unitId,
        ]);
    }
}
