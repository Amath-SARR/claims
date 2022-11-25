<?php

namespace App\Console\Commands;

use App\Mail\SendUserRecapMail;
use App\Models\Application;
use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendUsersRecapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-users-recap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a summary email hourly to a specific user';

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
        $users = User::whereHas('categorieReclamations', function ($query) {
            $query->whereHas('reclamations', function ($query) {
                $query->where('user_id', null)
                    ->whereRelation('state', 'code', 'ENATTENTE')
                    ->where('created_at', '>=', now()->subHours(1));
            });
        })->get();
        if (count($users) == 0) {
            $this->info("Aucun utilisateur concerné.");
            return 0;
        }

        foreach ($users as $user) {
            $applications = Application::whereHas('reclamations', function ($query) {
                $query->where('user_id', null)
                    ->whereRelation('state', 'code', 'ENATTENTE')
                    ->where('reclamations.created_at', '>=', now()->subHours(1));
            })->whereHas('intervenants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
            $this->info('Email envoyé à [' . $user->email . ']');
            $tabApplicationAndReclamations = [];
            foreach ($applications as $application) {
                $this->info('Application concernée: ' . $application->nom);
                $reclamations = Reclamation::where('user_id', null)
                    ->where('created_at', '>=', now()->subHours(1))
                    ->whereRelation('state', 'code', 'ENATTENTE')
                    ->whereRelation('categorieReclamation', 'application_id', $application->id)
                    ->get();
                $this->info('Nombre de réclamation: ' . count($reclamations));
                $j = 0;
                foreach ($reclamations as $reclamation) {
                    $j++;
                    $this->info('Objet de la réclamation ' . $j . ': [' . $reclamation->getNumberAttribute() . '] - ' . $reclamation->objet);
                }
                $tabApplicationAndReclamations[] = array('application' => $application, 'reclamations' => $reclamations);
                $this->info('--');
            }
            Mail::to($user->email)->send(new SendUserRecapMail($user, $tabApplicationAndReclamations));
        }
        $this->info('--');
        $this->info("Nombre total d'utilisateur(s) concerné(s): " . $users->count() . '.');
        $this->info('Fait le: ' . date('d-m-Y') . ' à ' . date("h:i:sa"));
        return 0;
    }
}
