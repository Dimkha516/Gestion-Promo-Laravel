<?php

namespace App\Jobs;

use App\Repositories\ApprenantRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ApprenantImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportApprenantsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $filePath;
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        \Log::info('Début de l\'importation des apprenants à partir de : ' . $this->filePath);

        // Vérification de la connexion à Firebase Realtime Database
        try {
            $database = app('firebase.database');
            \Log::info('Connexion à Realtime Database établie');
        } catch (\Exception $e) {
            \Log::error('Erreur de connexion à Firebase : ' . $e->getMessage());
            return;
        }

        Excel::import(new ApprenantImport, $this->filePath);
        \Log::info('Importation terminée pour : ' . $this->filePath);
    }
}
