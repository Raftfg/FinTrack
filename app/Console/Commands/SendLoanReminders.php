<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use App\Domain\Loan\Models\Loan;
use App\Notifications\SystemAlert;
use App\Mail\LoanReminderMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

#[Signature('app:send-loan-reminders')]
#[Description('Envoie des rappels par e-mail/SMS aux débiteurs en retard et des notifications aux prêteurs.')]
class SendLoanReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des prêts arrivant à échéance...');
        
        $loans = Loan::with('user')
            ->where('statut', '!=', 'rembourse')
            ->whereNotNull('date_echeance')
            ->get();
            
        $count = 0;

        foreach ($loans as $loan) {
            $diffInDays = now()->startOfDay()->diffInDays($loan->date_echeance, false);

            // Trigger reminder if due date is in 3 days or less, or has passed
            if ($diffInDays <= 3) {
                // Check if user was already notified about this specific loan today
                $alreadyNotifiedToday = $loan->user->notifications()
                    ->whereDate('created_at', now()->toDateString())
                    ->get()
                    ->contains(fn($n) => 
                        isset($n->data['type']) && 
                        $n->data['type'] === 'loan_reminder' && 
                        isset($n->data['loan_id']) && 
                        $n->data['loan_id'] == $loan->id
                    );

                if ($alreadyNotifiedToday) {
                    $this->line("Alerte déjà envoyée aujourd'hui pour le prêt de {$loan->personne}.");
                    continue;
                }

                $messageText = '';

                if ($diffInDays < 0) {
                    $days = abs($diffInDays);
                    $messageText = "Le prêt accordé à {$loan->personne} de " . number_format($loan->montant_initial, 0, ',', ' ') . " FCFA a dépassé l'échéance de {$days} jour(s).";
                } elseif ($diffInDays == 0) {
                    $messageText = "Le prêt accordé à {$loan->personne} de " . number_format($loan->montant_initial, 0, ',', ' ') . " FCFA arrive à échéance AUJOURD'HUI.";
                } else {
                    $messageText = "Le prêt accordé à {$loan->personne} de " . number_format($loan->montant_initial, 0, ',', ' ') . " FCFA arrive à échéance dans {$diffInDays} jour(s).";
                }

                // 1. Send Email to the debtor if email is set
                $emailSent = false;
                if ($loan->email) {
                    try {
                        Mail::to($loan->email)->send(new LoanReminderMailable($loan));
                        $emailSent = true;
                        $this->info("Email de rappel envoyé à {$loan->personne} ({$loan->email})");
                    } catch (\Exception $e) {
                        $this->error("Échec d'envoi d'email à {$loan->email} : " . $e->getMessage());
                    }
                }

                // 2. Simulate sending SMS to the debtor if telephone is set
                $smsSent = false;
                if ($loan->telephone) {
                    Log::info("SIMULATION SMS envoyé à {$loan->telephone} : Bonjour {$loan->personne}, votre prêt de " . number_format($loan->montant_initial, 0, ',', ' ') . " FCFA chez {$loan->user->name} est en retard ou arrive à échéance. Merci de le régulariser.");
                    $smsSent = true;
                    $this->info("SMS (Simulé) de rappel envoyé à {$loan->personne} ({$loan->telephone})");
                }

                // 3. Send database Notification to the App User (creditor)
                $notifTitle = ($diffInDays < 0) ? 'Prêt en retard !' : 'Échéance de prêt proche';
                $extraNotifDetails = $messageText;
                if ($emailSent && $smsSent) {
                    $extraNotifDetails .= " Un rappel par email et par SMS a été automatiquement envoyé au débiteur.";
                } elseif ($emailSent) {
                    $extraNotifDetails .= " Un rappel par email a été automatiquement envoyé au débiteur.";
                } elseif ($smsSent) {
                    $extraNotifDetails .= " Un rappel par SMS a été automatiquement envoyé au débiteur (simulé).";
                } else {
                    $extraNotifDetails .= " Aucun e-mail ni téléphone n'est configuré pour ce débiteur.";
                }

                // Notify User
                $loan->user->notify(new class($notifTitle, $extraNotifDetails, 'loan_reminder', $loan->id) extends \Illuminate\Notifications\Notification {
                    public function __construct(
                        public string $title,
                        public string $message,
                        public string $type,
                        public int $loanId
                    ) {}
                    
                    public function via($notifiable) { return ['database']; }
                    
                    public function toArray($notifiable) {
                        return [
                            'title' => $this->title,
                            'message' => $this->message,
                            'type' => $this->type,
                            'loan_id' => $this->loanId,
                        ];
                    }
                });

                $count++;
            }
        }

        $this->info("{$count} rappels traités.");
    }
}
