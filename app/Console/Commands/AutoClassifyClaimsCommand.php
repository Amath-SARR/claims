<?php

namespace App\Console\Commands;

use App\Models\Reclamation;
use App\Models\ReclamationState;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoClassifyClaimsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classify-claims';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permets de classer toutes les réclamations qui sont à létat résolue depuis 7 jours.';

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
        // recuperer les reclamations classées il y'a une semaine
        $reclamations = Reclamation::whereRelation('state','code','RESOLVED')
        ->whereRelation('latestReclamationState',function($query) {
            $query->whereRelation('state','code','RESOLVED')
            ->where('created_at','like',date_format(today()->addDays(-7),'Y-m-d').'%');
        })
        ->get();


       // $state = State::where('code', 'RESOLVED')->first();

      /*  $reclamationLastStates = DB::table('reclamation_states')
            ->orderByDesc('id')
            ->get()->unique('reclamation_id');

        foreach ($reclamationLastStates as $reclamationLastState) {
            if (($reclamationLastState->state_id == $state->id)) {
               $this->info($reclamationLastState->id);
            }
        } */

       // $resolvedReclamations = Reclamation::whereRelation('state', 'code', 'RESOLVED')->get();
        foreach ($reclamations as $reclamation) {
            $this->info("{$reclamation->number} - {$reclamation->objet}");
           /* $reclamationLastState = ReclamationState::where('reclamation_id', $reclamation->id)
            ->where('state_id', $reclamation->state->id)->latest()->first();
            if (intval(date_diff($reclamationLastState->created_at, Carbon::now())->format('%d')) == 7) {
                //Envoi de mail
            } */
        }

        // $i = 0;
        // foreach ($resolvedClaimsSinceOneWeeks as $reclamation) {
        //     DB::beginTransaction();
        //     try {
        //         $i++;
        //         $state = State::where('code', 'CLASSED')->first();
        //         $reclamation->state_id = $state->id;
        //         $reclamation->archivee = 1;
        //         $reclamation->update();
        //         $reclamationState = new ReclamationState();
        //         $reclamationState->state_id = $reclamation->state_id;
        //         $reclamationState->user_id = null;
        //         $reclamation->reclamationStates()->save($reclamationState);
        //         DB::commit();
        //     } catch (\Throwable $th) {
        //         DB::rollback();
        //         return $this->info("Une erreur est survenue lors de la modification...");
        //     }
        // }
        // if ($i == 0) {
        //     $this->info('Aucune réclamation à létat résolu depuis 7 jours.');
        // } else {
        //     $this->info($i . ' réclamation(s) à létat résolu depuis 7 jours. Réclamation(s) classée(s)');
        // }
        // $this->info('--');
        // $this->info('Fait le: ' . date('d-m-Y') . ' à ' . date("h:i:sa"));
        return 0;
    }
}
