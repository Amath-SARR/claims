<?php

namespace App\Console\Commands;

use App\Mail\SendReclamateurRappelMail;
use App\Models\Reclamation;
use App\Models\ReclamationState;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;


class SendReclamateurRappelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-reclamateur-rappel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer un mail de rappel au réclamateur si sa tâche est résolue depuis 2 ou 4 jours';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resolvedReclamationSinceTwoDays = Reclamation::whereHas('reclamationStates', function ($query){
            $query->whereDate('created_at', Carbon::today()->subDays(2)->toDateString());
        })->whereRelation('state', 'code', 'RESOLVED')->get();
        $i = 0;
        foreach ($resolvedReclamationSinceTwoDays as $reclamation) {
            $i++;
            Mail::to($reclamation->email)->send(new SendReclamateurRappelMail($reclamation));
            $this->info('Email de rappel envoyé à : [' . $reclamation->email . ']');
            $this->info('--');
        }

        $resolvedReclamationSinceFourDays = Reclamation::whereHas('reclamationStates', function ($query) {
            $query->whereDate('created_at', Carbon::today()->subDays(4)->toDateString());
        })->whereRelation('state', 'code', 'RESOLVED')->get();
        $j = 0;
        foreach ($resolvedReclamationSinceFourDays as $reclamation) {
            $j++;
            Mail::to($reclamation->email)->send(new SendReclamateurRappelMail($reclamation));
            $this->info('Email de rappel envoyé à : [' . $reclamation->email . ']');
            $this->info('--');
        }

        $this->info($i . ' réclamation(s) à létat résolu depuis 2 jours.');
        $this->info($j . ' réclamation(s) à létat résolu depuis 4 jours.');
        $this->info('--');
        $this->info('--');
        $this->info('Fait le: ' . date('d-m-Y') . ' à ' . date("h:i:sa"));
        return 0;
    }
}
