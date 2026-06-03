# FinTrack — Prompt Maître Complet
## Application de gestion de finances personnelles
### Stack : Laravel 13 · PostgreSQL · Blade · Tailwind CSS · Alpine.js · Flux UI

---

## 1. CONTEXTE & OBJECTIF

Tu es un développeur Laravel senior avec 10+ ans d'expérience. Tu vas construire
de A à Z une application web de gestion de finances personnelles appelée **FinTrack**.

L'application doit permettre à un utilisateur authentifié de :
- Suivre ses revenus, dépenses, épargne
- Gérer les prêts accordés à d'autres personnes
- Définir des budgets mensuels par catégorie avec alertes
- Suivre des objectifs financiers avec progression
- Visualiser un dashboard complet avec graphiques

---

## 2. STACK TECHNIQUE

| Composant        | Technologie                        |
|------------------|------------------------------------|
| Framework        | Laravel 13                         |
| Base de données  | PostgreSQL 18+                     |
| Frontend         | Blade + Tailwind CSS 3 + Alpine.js |
| UI Kit           | Flux UI (livewire/flux)            |
| Auth             | Laravel Breeze (Blade stack)       |
| Graphiques       | Chart.js 4 (via CDN)               |
| Jobs / Queue     | Laravel Queue (driver: database)   |
| Cache            | Redis (ou database en fallback)    |
| Export           | maatwebsite/excel + barryvdh/dompdf|
| DTOs             | spatie/laravel-data                |
| Tests            | Pest PHP                           |
| Linting          | Laravel Pint                       |

---

## 3. INSTALLATION & CONFIGURATION INITIALE

### 3.1 Création du projet

```bash
composer create-project laravel/laravel fintrack
cd fintrack

# Auth scaffolding
composer require laravel/breeze --dev
php artisan breeze:install blade

# Packages métier
composer require livewire/flux
composer require spatie/laravel-data
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf

# Dev tools
composer require --dev pestphp/pest
./vendor/bin/pest --init
```

### 3.2 Variables d'environnement (.env)

```env
APP_NAME=FinTrack
APP_ENV=local
APP_KEY=           # généré par php artisan key:generate
APP_URL=http://localhost:8000
APP_LOCALE=fr
APP_TIMEZONE=Africa/Porto-Novo

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=fintrack
DB_USERNAME=postgres
DB_PASSWORD=secret

CACHE_DRIVER=redis
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@fintrack.app
MAIL_FROM_NAME=FinTrack
```

### 3.3 Configuration config/app.php

```php
'locale' => 'fr',
'timezone' => 'Africa/Porto-Novo',
'currency' => env('APP_CURRENCY', 'XOF'),
```

Créer `config/fintrack.php` :

```php
<?php
return [
    'currency'        => env('APP_CURRENCY', 'XOF'),
    'currency_symbol' => env('APP_CURRENCY_SYMBOL', 'FCFA'),
    'budget_alert_threshold' => 80, // % avant alerte orange
    'transaction_categories' => [
        'nourriture', 'transport', 'sante', 'logement',
        'education', 'aide_sociale', 'epargne', 'loisirs', 'autre',
    ],
];
```

---

## 4. STRUCTURE COMPLÈTE DU PROJET

```
fintrack/
├── app/
│   ├── Domain/
│   │   ├── Transaction/
│   │   │   ├── Enums/TransactionType.php
│   │   │   ├── Events/TransactionCreated.php
│   │   │   ├── Events/TransactionDeleted.php
│   │   │   ├── Models/Transaction.php
│   │   │   ├── Observers/TransactionObserver.php
│   │   │   └── Policies/TransactionPolicy.php
│   │   ├── Loan/
│   │   │   ├── Enums/LoanStatus.php
│   │   │   ├── Models/Loan.php
│   │   │   └── Observers/LoanObserver.php
│   │   ├── Budget/
│   │   │   └── Models/Budget.php
│   │   └── Goal/
│   │       └── Models/Goal.php
│   │
│   ├── Application/
│   │   ├── Actions/
│   │   │   ├── ProcessLoanRepaymentAction.php
│   │   │   └── RecalculateBudgetAction.php
│   │   ├── DTOs/
│   │   │   ├── CreateTransactionData.php
│   │   │   ├── CreateLoanData.php
│   │   │   ├── CreateBudgetData.php
│   │   │   └── CreateGoalData.php
│   │   ├── Listeners/
│   │   │   ├── CheckBudgetAlert.php
│   │   │   └── UpdateGoalProgress.php
│   │   └── Services/
│   │       ├── BudgetService.php
│   │       ├── DashboardService.php
│   │       ├── GoalService.php
│   │       ├── LoanService.php
│   │       └── TransactionService.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── TransactionController.php
│   │   │   ├── LoanController.php
│   │   │   ├── BudgetController.php
│   │   │   ├── GoalController.php
│   │   │   └── ExportController.php
│   │   ├── Middleware/
│   │   │   └── SetLocale.php
│   │   └── Requests/
│   │       ├── StoreTransactionRequest.php
│   │       ├── StoreLoanRequest.php
│   │       ├── StoreBudgetRequest.php
│   │       └── StoreGoalRequest.php
│   │
│   ├── Infrastructure/
│   │   ├── Notifications/
│   │   │   ├── BudgetDepasseNotification.php
│   │   │   └── LoanRappelNotification.php
│   │   └── Repositories/
│   │       ├── Contracts/
│   │       │   ├── TransactionRepositoryInterface.php
│   │       │   └── LoanRepositoryInterface.php
│   │       ├── TransactionRepository.php
│   │       └── LoanRepository.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── EventServiceProvider.php
│
├── database/
│   ├── migrations/
│   │   ├── 0001_create_users_table.php
│   │   ├── 0002_create_transactions_table.php
│   │   ├── 0003_create_loans_table.php
│   │   ├── 0004_create_budgets_table.php
│   │   ├── 0005_create_goals_table.php
│   │   └── 0006_create_notifications_table.php
│   ├── factories/
│   │   ├── TransactionFactory.php
│   │   ├── LoanFactory.php
│   │   └── GoalFactory.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── DemoDataSeeder.php
│
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── guest.blade.php
│       ├── components/
│       │   ├── stat-card.blade.php
│       │   ├── budget-bar.blade.php
│       │   ├── transaction-row.blade.php
│       │   ├── loan-badge.blade.php
│       │   └── goal-progress.blade.php
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── transactions/
│       │   ├── index.blade.php
│       │   └── create.blade.php
│       ├── loans/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── budgets/
│       │   └── index.blade.php
│       └── goals/
│           └── index.blade.php
│
├── routes/
│   └── web.php
│
└── tests/
    ├── Feature/
    │   ├── TransactionTest.php
    │   ├── LoanTest.php
    │   ├── BudgetTest.php
    │   └── DashboardTest.php
    └── Unit/
        ├── TransactionServiceTest.php
        └── DashboardServiceTest.php
```

---

## 5. MIGRATIONS POSTGRESQL

### users (étendue)

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('currency', 10)->default('XOF');
    $table->string('currency_symbol', 10)->default('FCFA');
    $table->rememberToken();
    $table->timestamps();
});
```

### transactions

```php
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('type', [
        'revenu','depense','epargne','pret','remboursement'
    ]);
    $table->decimal('montant', 15, 2);
    $table->string('categorie', 50);
    $table->date('date_transaction');
    $table->text('note')->nullable();
    $table->foreignId('loan_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['user_id', 'date_transaction']);
    $table->index(['user_id', 'type']);
    $table->index(['user_id', 'categorie']);
});
```

### loans

```php
Schema::create('loans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('personne');
    $table->decimal('montant_initial', 15, 2);
    $table->decimal('montant_rembourse', 15, 2)->default(0);
    $table->enum('statut', ['non_rembourse','partiel','rembourse'])
          ->default('non_rembourse');
    $table->date('date_pret');
    $table->date('date_echeance')->nullable();
    $table->text('note')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['user_id', 'statut']);
});
```

### budgets

```php
Schema::create('budgets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('categorie', 50);
    $table->decimal('montant_limite', 15, 2);
    $table->unsignedTinyInteger('mois');   // 1-12
    $table->unsignedSmallInteger('annee');
    $table->timestamps();

    $table->unique(['user_id', 'categorie', 'mois', 'annee']);
    $table->index(['user_id', 'mois', 'annee']);
});
```

### goals

```php
Schema::create('goals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('nom');
    $table->text('description')->nullable();
    $table->decimal('montant_cible', 15, 2);
    $table->decimal('montant_actuel', 15, 2)->default(0);
    $table->date('date_cible')->nullable();
    $table->boolean('atteint')->default(false);
    $table->timestamps();

    $table->index(['user_id', 'atteint']);
});
```

---

## 6. MODÈLES ELOQUENT

### Enums PHP 8.1

```php
// app/Domain/Transaction/Enums/TransactionType.php
enum TransactionType: string {
    case REVENU         = 'revenu';
    case DEPENSE        = 'depense';
    case EPARGNE        = 'epargne';
    case PRET           = 'pret';
    case REMBOURSEMENT  = 'remboursement';

    public function label(): string {
        return match($this) {
            self::REVENU        => 'Revenu',
            self::DEPENSE       => 'Dépense',
            self::EPARGNE       => 'Épargne',
            self::PRET          => 'Prêt accordé',
            self::REMBOURSEMENT => 'Remboursement reçu',
        };
    }

    public function isDebit(): bool {
        return in_array($this, [self::DEPENSE, self::PRET]);
    }
}

// app/Domain/Loan/Enums/LoanStatus.php
enum LoanStatus: string {
    case NON_REMBOURSE = 'non_rembourse';
    case PARTIEL       = 'partiel';
    case REMBOURSE     = 'rembourse';

    public function label(): string {
        return match($this) {
            self::NON_REMBOURSE => 'Non remboursé',
            self::PARTIEL       => 'Partiellement remboursé',
            self::REMBOURSE     => 'Remboursé',
        };
    }

    public function color(): string {
        return match($this) {
            self::NON_REMBOURSE => 'red',
            self::PARTIEL       => 'amber',
            self::REMBOURSE     => 'green',
        };
    }
}
```

### Model Transaction

```php
// app/Domain/Transaction/Models/Transaction.php
namespace App\Domain\Transaction\Models;

use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id','type','montant','categorie',
        'date_transaction','note','loan_id',
    ];

    protected $casts = [
        'type'             => TransactionType::class,
        'montant'          => 'decimal:2',
        'date_transaction' => 'date',
    ];

    protected static function booted(): void {
        static::observe(TransactionObserver::class);
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function loan() {
        return $this->belongsTo(\App\Domain\Loan\Models\Loan::class);
    }

    public function scopeForUser($query, int $userId) {
        return $query->where('user_id', $userId);
    }

    public function scopeCurrentMonth($query) {
        return $query->whereMonth('date_transaction', now()->month)
                     ->whereYear('date_transaction', now()->year);
    }

    public function scopeOfType($query, TransactionType $type) {
        return $query->where('type', $type);
    }
}
```

### Model Loan

```php
// app/Domain/Loan/Models/Loan.php
class Loan extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id','personne','montant_initial',
        'montant_rembourse','statut','date_pret',
        'date_echeance','note',
    ];

    protected $casts = [
        'statut'            => LoanStatus::class,
        'montant_initial'   => 'decimal:2',
        'montant_rembourse' => 'decimal:2',
        'date_pret'         => 'date',
        'date_echeance'     => 'date',
    ];

    protected static function booted(): void {
        static::observe(LoanObserver::class);
    }

    public function getMontantRestantAttribute(): float {
        return $this->montant_initial - $this->montant_rembourse;
    }

    public function getProgressionAttribute(): int {
        if ($this->montant_initial == 0) return 0;
        return (int) (($this->montant_rembourse / $this->montant_initial) * 100);
    }

    public function transactions() {
        return $this->hasMany(
            \App\Domain\Transaction\Models\Transaction::class
        );
    }
}
```

---

## 7. REPOSITORIES

### Interface

```php
// app/Infrastructure/Repositories/Contracts/TransactionRepositoryInterface.php
namespace App\Infrastructure\Repositories\Contracts;

interface TransactionRepositoryInterface {
    public function create(array $data): \App\Domain\Transaction\Models\Transaction;
    public function getMonthlyByType(int $userId, int $month, int $year): array;
    public function getMonthlyByCategory(int $userId, int $month, int $year): array;
    public function paginateForUser(int $userId, array $filters, int $perPage = 20);
    public function deleteById(int $id): bool;
}
```

### Implémentation Eloquent

```php
// app/Infrastructure/Repositories/TransactionRepository.php
namespace App\Infrastructure\Repositories;

use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use App\Infrastructure\Repositories\Contracts\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface {

    public function create(array $data): Transaction {
        return Transaction::create($data);
    }

    public function getMonthlyByType(int $userId, int $month, int $year): array {
        return Transaction::forUser($userId)
            ->whereMonth('date_transaction', $month)
            ->whereYear('date_transaction', $year)
            ->selectRaw('type, SUM(montant) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
    }

    public function getMonthlyByCategory(int $userId, int $month, int $year): array {
        return Transaction::forUser($userId)
            ->whereMonth('date_transaction', $month)
            ->whereYear('date_transaction', $year)
            ->where('type', TransactionType::DEPENSE)
            ->selectRaw('categorie, SUM(montant) as total')
            ->groupBy('categorie')
            ->orderByDesc('total')
            ->pluck('total', 'categorie')
            ->toArray();
    }

    public function paginateForUser(int $userId, array $filters, int $perPage = 20) {
        $query = Transaction::forUser($userId)
            ->with('loan')
            ->orderByDesc('date_transaction');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['categorie'])) {
            $query->where('categorie', $filters['categorie']);
        }
        if (!empty($filters['mois'])) {
            $query->whereMonth('date_transaction', $filters['mois']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function deleteById(int $id): bool {
        return Transaction::findOrFail($id)->delete();
    }
}
```

### Binding dans AppServiceProvider

```php
// app/Providers/AppServiceProvider.php
use App\Infrastructure\Repositories\Contracts\TransactionRepositoryInterface;
use App\Infrastructure\Repositories\TransactionRepository;
use App\Infrastructure\Repositories\Contracts\LoanRepositoryInterface;
use App\Infrastructure\Repositories\LoanRepository;

public function register(): void {
    $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);
}
```

---

## 8. DTOs (spatie/laravel-data)

```php
// app/Application/DTOs/CreateTransactionData.php
namespace App\Application\DTOs;

use App\Domain\Transaction\Enums\TransactionType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class CreateTransactionData extends Data {
    public function __construct(
        public readonly int            $user_id,
        public readonly TransactionType $type,
        public readonly float          $montant,
        public readonly string         $categorie,
        public readonly \Carbon\Carbon $date_transaction,
        public readonly ?string        $note = null,
        public readonly ?int           $loan_id = null,
    ) {}
}
```

---

## 9. SERVICES

### TransactionService

```php
// app/Application/Services/TransactionService.php
namespace App\Application\Services;

use App\Application\DTOs\CreateTransactionData;
use App\Domain\Transaction\Events\TransactionCreated;
use App\Domain\Transaction\Models\Transaction;
use App\Infrastructure\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TransactionService {

    public function __construct(
        private readonly TransactionRepositoryInterface $repository,
    ) {}

    public function create(CreateTransactionData $data): Transaction {
        $transaction = $this->repository->create($data->toArray());
        event(new TransactionCreated($transaction));
        $this->invalidateCache($data->user_id);
        return $transaction;
    }

    public function delete(int $id, int $userId): void {
        $transaction = Transaction::where('user_id', $userId)->findOrFail($id);
        $transaction->delete();
        $this->invalidateCache($userId);
    }

    private function invalidateCache(int $userId): void {
        Cache::forget("dashboard_stats_{$userId}");
        Cache::forget("budget_stats_{$userId}_" . now()->month . '_' . now()->year);
    }
}
```

### DashboardService

```php
// app/Application/Services/DashboardService.php
namespace App\Application\Services;

use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Loan\Models\Loan;
use App\Domain\Goal\Models\Goal;
use Illuminate\Support\Facades\Cache;

class DashboardService {

    public function getStats(int $userId): array {
        return Cache::remember(
            "dashboard_stats_{$userId}",
            now()->addMinutes(15),
            fn () => $this->computeStats($userId)
        );
    }

    private function computeStats(int $userId): array {
        $month = now()->month;
        $year  = now()->year;

        $revenus = Transaction::forUser($userId)
            ->currentMonth()->ofType(TransactionType::REVENU)
            ->sum('montant');

        $depenses = Transaction::forUser($userId)
            ->currentMonth()->ofType(TransactionType::DEPENSE)
            ->sum('montant');

        $epargne = Transaction::forUser($userId)
            ->ofType(TransactionType::EPARGNE)->sum('montant');

        $pretsActifs = Loan::where('user_id', $userId)
            ->whereIn('statut', ['non_rembourse', 'partiel'])
            ->sum(\DB::raw('montant_initial - montant_rembourse'));

        $depensesParCategorie = Transaction::forUser($userId)
            ->currentMonth()->ofType(TransactionType::DEPENSE)
            ->selectRaw('categorie, SUM(montant) as total')
            ->groupBy('categorie')
            ->pluck('total', 'categorie')
            ->toArray();

        $dernieresTransactions = Transaction::forUser($userId)
            ->with('loan')
            ->orderByDesc('date_transaction')
            ->limit(5)->get();

        $objectifs = Goal::where('user_id', $userId)
            ->where('atteint', false)->get();

        return compact(
            'revenus','depenses','epargne','pretsActifs',
            'depensesParCategorie','dernieresTransactions','objectifs'
        );
    }
}
```

---

## 10. OBSERVERS

```php
// app/Domain/Transaction/Observers/TransactionObserver.php
namespace App\Domain\Transaction\Observers;

use App\Domain\Transaction\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class TransactionObserver {

    public function created(Transaction $transaction): void {
        Cache::forget("dashboard_stats_{$transaction->user_id}");
    }

    public function deleted(Transaction $transaction): void {
        Cache::forget("dashboard_stats_{$transaction->user_id}");
    }
}

// app/Domain/Loan/Observers/LoanObserver.php
class LoanObserver {

    public function updated(Loan $loan): void {
        if ($loan->isDirty('montant_rembourse')) {
            $restant = $loan->montant_initial - $loan->montant_rembourse;

            $loan->statut = match(true) {
                $restant <= 0                => LoanStatus::REMBOURSE,
                $loan->montant_rembourse > 0 => LoanStatus::PARTIEL,
                default                      => LoanStatus::NON_REMBOURSE,
            };

            $loan->saveQuietly();
        }
    }
}
```

---

## 11. EVENTS & LISTENERS

### Event

```php
// app/Domain/Transaction/Events/TransactionCreated.php
namespace App\Domain\Transaction\Events;

use App\Domain\Transaction\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated {
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Transaction $transaction
    ) {}
}
```

### Listener — CheckBudgetAlert

```php
// app/Application/Listeners/CheckBudgetAlert.php
namespace App\Application\Listeners;

use App\Application\Services\BudgetService;
use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Events\TransactionCreated;
use App\Infrastructure\Notifications\BudgetDepasseNotification;

class CheckBudgetAlert {

    public function __construct(
        private readonly BudgetService $budgetService
    ) {}

    public function handle(TransactionCreated $event): void {
        $t = $event->transaction;

        if ($t->type !== TransactionType::DEPENSE) return;

        $alerte = $this->budgetService->checkAlert(
            $t->user_id, $t->categorie,
            now()->month, now()->year
        );

        if ($alerte) {
            $t->user->notify(new BudgetDepasseNotification(
                $t->categorie, $alerte['pourcentage'], $alerte['limite']
            ));
        }
    }
}
```

### EventServiceProvider

```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    \App\Domain\Transaction\Events\TransactionCreated::class => [
        \App\Application\Listeners\CheckBudgetAlert::class,
        \App\Application\Listeners\UpdateGoalProgress::class,
    ],
];
```

---

## 12. NOTIFICATIONS

```php
// app/Infrastructure/Notifications/BudgetDepasseNotification.php
namespace App\Infrastructure\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BudgetDepasseNotification extends Notification {

    public function __construct(
        private readonly string $categorie,
        private readonly int    $pourcentage,
        private readonly float  $limite,
    ) {}

    public function via(object $notifiable): array {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)
            ->subject("Alerte budget — {$this->categorie}")
            ->line("Vous avez atteint {$this->pourcentage}% de votre budget {$this->categorie}.")
            ->line("Limite fixée : " . number_format($this->limite, 0, ',', ' ') . " FCFA")
            ->action('Voir mon dashboard', url('/dashboard'));
    }

    public function toArray(object $notifiable): array {
        return [
            'message'     => "Budget {$this->categorie} à {$this->pourcentage}%",
            'categorie'   => $this->categorie,
            'pourcentage' => $this->pourcentage,
        ];
    }
}
```

---

## 13. FORM REQUESTS

```php
// app/Http/Requests/StoreTransactionRequest.php
namespace App\Http\Requests;

use App\Domain\Transaction\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTransactionRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'type'             => ['required', new Enum(TransactionType::class)],
            'montant'          => ['required', 'numeric', 'min:1', 'max:999999999'],
            'categorie'        => ['required', 'string',
                                   'in:' . implode(',', config('fintrack.transaction_categories'))],
            'date_transaction' => ['required', 'date', 'before_or_equal:today'],
            'note'             => ['nullable', 'string', 'max:500'],
            'loan_id'          => ['nullable', 'exists:loans,id'],
        ];
    }

    public function messages(): array {
        return [
            'type.required'             => 'Le type de transaction est obligatoire.',
            'montant.required'          => 'Le montant est obligatoire.',
            'montant.min'               => 'Le montant doit être positif.',
            'date_transaction.required' => 'La date est obligatoire.',
            'date_transaction.before_or_equal' => 'La date ne peut pas être dans le futur.',
        ];
    }
}
```

---

## 14. CONTROLLERS

```php
// app/Http/Controllers/TransactionController.php
namespace App\Http\Controllers;

use App\Application\DTOs\CreateTransactionData;
use App\Application\Services\TransactionService;
use App\Domain\Transaction\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Infrastructure\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class TransactionController extends Controller {

    public function __construct(
        private readonly TransactionService              $service,
        private readonly TransactionRepositoryInterface  $repository,
    ) {}

    public function index(Request $request) {
        $transactions = $this->repository->paginateForUser(
            auth()->id(),
            $request->only(['type', 'categorie', 'mois']),
        );

        return view('transactions.index', [
            'transactions' => $transactions,
            'types'        => TransactionType::cases(),
            'categories'   => config('fintrack.transaction_categories'),
        ]);
    }

    public function create() {
        return view('transactions.create', [
            'types'      => TransactionType::cases(),
            'categories' => config('fintrack.transaction_categories'),
        ]);
    }

    public function store(StoreTransactionRequest $request) {
        $data = CreateTransactionData::from([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        $this->service->create($data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction ajoutée avec succès.');
    }

    public function destroy(int $id) {
        $this->service->delete($id, auth()->id());
        return back()->with('success', 'Transaction supprimée.');
    }
}

// app/Http/Controllers/DashboardController.php
class DashboardController extends Controller {

    public function __construct(
        private readonly DashboardService $service
    ) {}

    public function index() {
        $stats = $this->service->getStats(auth()->id());
        return view('dashboard.index', $stats);
    }
}
```

---

## 15. ROUTES

```php
// routes/web.php
use App\Http\Controllers\{
    DashboardController,
    TransactionController,
    LoanController,
    BudgetController,
    GoalController,
    ExportController,
};

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])
         ->name('dashboard');

    Route::resource('transactions', TransactionController::class)
         ->only(['index', 'create', 'store', 'destroy']);

    Route::resource('loans', LoanController::class)
         ->only(['index', 'show', 'create', 'store', 'update']);

    Route::post('loans/{loan}/rembourser', [LoanController::class, 'rembourser'])
         ->name('loans.rembourser');

    Route::resource('budgets', BudgetController::class)
         ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('goals', GoalController::class)
         ->only(['index', 'store', 'update', 'destroy']);

    // Exports
    Route::get('export/transactions/excel', [ExportController::class, 'transactionsExcel'])
         ->name('export.transactions.excel');
    Route::get('export/transactions/pdf', [ExportController::class, 'transactionsPdf'])
         ->name('export.transactions.pdf');
});
```

---

## 16. VUES BLADE

### Layout principal (app.blade.php)

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FinTrack') — FinTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="bg-gray-50 font-sans antialiased">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200
                  z-30 transform transition-transform duration-200
                  lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="p-6 border-b border-gray-100">
            <h1 class="text-xl font-semibold text-blue-700">FinTrack</h1>
            <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->name }}</p>
        </div>

        <nav class="p-4 space-y-1">
            @php
                $links = [
                    ['route' => 'dashboard',          'icon' => 'chart-bar',   'label' => 'Dashboard'],
                    ['route' => 'transactions.index',  'icon' => 'arrows-exchange', 'label' => 'Transactions'],
                    ['route' => 'loans.index',         'icon' => 'hand-coin',   'label' => 'Prêts'],
                    ['route' => 'budgets.index',       'icon' => 'calculator',  'label' => 'Budgets'],
                    ['route' => 'goals.index',         'icon' => 'target',      'label' => 'Objectifs'],
                ];
            @endphp

            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs($link['route']) 
                             ? 'bg-blue-50 text-blue-700 font-medium' 
                             : 'text-gray-600 hover:bg-gray-50' }}">
                    <flux:icon name="{{ $link['icon'] }}" class="w-5 h-5" />
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Notifications --}}
        @if(auth()->user()->unreadNotifications->count())
            <div class="absolute bottom-20 left-4 right-4">
                @foreach(auth()->user()->unreadNotifications->take(3) as $notif)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-2 text-xs text-amber-800">
                        {{ $notif->data['message'] }}
                    </div>
                @endforeach
            </div>
        @endif

        <div class="absolute bottom-4 left-4 right-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left text-sm text-gray-500 hover:text-red-600 px-3 py-2">
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="lg:ml-64 min-h-screen">
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center
                       justify-between lg:hidden">
            <button @click="sidebarOpen = !sidebarOpen">
                <flux:icon name="menu-2" class="w-6 h-6 text-gray-600" />
            </button>
            <span class="font-semibold text-blue-700">FinTrack</span>
        </header>

        <main class="p-6">
            {{-- Flash messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 bg-green-50 border border-green-200 text-green-800
                            rounded-lg px-4 py-3 text-sm flex justify-between">
                    {{ session('success') }}
                    <button @click="show = false" class="ml-4 text-green-600">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
</body>
</html>
```

### Composant stat-card

```blade
{{-- resources/views/components/stat-card.blade.php --}}
@props(['label', 'value', 'color' => 'blue', 'icon' => 'chart-bar', 'sub' => null])

@php
    $colors = [
        'blue'  => 'bg-blue-50 text-blue-700 border-blue-100',
        'green' => 'bg-green-50 text-green-700 border-green-100',
        'red'   => 'bg-red-50 text-red-700 border-red-100',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-100',
    ];
@endphp

<div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3">
        <span class="text-sm text-gray-500">{{ $label }}</span>
        <span class="p-2 rounded-lg {{ $colors[$color] }}">
            <flux:icon name="{{ $icon }}" class="w-4 h-4" />
        </span>
    </div>
    <p class="text-2xl font-semibold text-gray-900">{{ $value }}</p>
    @if($sub)
        <p class="text-xs text-gray-400 mt-1">{{ $sub }}</p>
    @endif
</div>
```

### Dashboard index

```blade
{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">

    <h1 class="text-2xl font-semibold text-gray-900 mb-6">
        Bonjour, {{ auth()->user()->name }}
        <span class="text-sm font-normal text-gray-400">
            — {{ now()->translatedFormat('F Y') }}
        </span>
    </h1>

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <x-stat-card
            label="Revenus du mois"
            :value="number_format($revenus, 0, ',', ' ') . ' FCFA'"
            color="green" icon="trending-up" />

        <x-stat-card
            label="Dépenses du mois"
            :value="number_format($depenses, 0, ',', ' ') . ' FCFA'"
            color="red" icon="trending-down" />

        <x-stat-card
            label="Épargne totale"
            :value="number_format($epargne, 0, ',', ' ') . ' FCFA'"
            color="blue" icon="piggy-bank" />

        <x-stat-card
            label="Prêts non remboursés"
            :value="number_format($pretsActifs, 0, ',', ' ') . ' FCFA'"
            color="amber" icon="hand-coin" />
    </div>

    {{-- Graphique + Transactions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Graphique dépenses par catégorie --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm lg:col-span-1">
            <h2 class="text-sm font-medium text-gray-700 mb-4">Dépenses par catégorie</h2>
            <canvas id="depensesChart" height="220"></canvas>
        </div>

        {{-- Dernières transactions --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-gray-700">Dernières transactions</h2>
                <a href="{{ route('transactions.index') }}"
                   class="text-xs text-blue-600 hover:underline">Voir tout</a>
            </div>

            @forelse($dernieresTransactions as $t)
                <x-transaction-row :transaction="$t" />
            @empty
                <p class="text-sm text-gray-400 text-center py-8">Aucune transaction ce mois-ci.</p>
            @endforelse
        </div>
    </div>

    {{-- Objectifs --}}
    @if($objectifs->count())
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <h2 class="text-sm font-medium text-gray-700 mb-4">Objectifs en cours</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($objectifs as $goal)
                    <x-goal-progress :goal="$goal" />
                @endforeach
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    const ctx = document.getElementById('depensesChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($depensesParCategorie)),
            datasets: [{
                data: @json(array_values($depensesParCategorie)),
                backgroundColor: [
                    '#3B82F6','#10B981','#F59E0B',
                    '#EF4444','#8B5CF6','#06B6D4','#F97316'
                ],
                borderWidth: 0,
            }]
        },
        options: {
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
        }
    });
</script>
@endpush
@endsection
```

---

## 17. ACTIONS (cas métier isolés)

```php
// app/Application/Actions/ProcessLoanRepaymentAction.php
namespace App\Application\Actions;

use App\Domain\Loan\Models\Loan;
use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ProcessLoanRepaymentAction {

    public function execute(Loan $loan, float $montant, string $note = null): void {
        DB::transaction(function () use ($loan, $montant, $note) {

            // Créer la transaction de remboursement
            Transaction::create([
                'user_id'          => $loan->user_id,
                'type'             => TransactionType::REMBOURSEMENT,
                'montant'          => $montant,
                'categorie'        => 'autre',
                'date_transaction' => today(),
                'note'             => $note ?? "Remboursement de {$loan->personne}",
                'loan_id'          => $loan->id,
            ]);

            // Mettre à jour le prêt (l'Observer gère le statut)
            $loan->increment('montant_rembourse', $montant);
        });
    }
}
```

---

## 18. EXPORT (Excel & PDF)

```php
// app/Http/Controllers/ExportController.php
namespace App\Http\Controllers;

use App\Domain\Transaction\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller {

    public function transactionsExcel() {
        $transactions = Transaction::forUser(auth()->id())
            ->currentMonth()
            ->orderByDesc('date_transaction')
            ->get();

        return Excel::download(
            new \App\Exports\TransactionsExport($transactions),
            'transactions-' . now()->format('Y-m') . '.xlsx'
        );
    }

    public function transactionsPdf() {
        $transactions = Transaction::forUser(auth()->id())
            ->currentMonth()
            ->orderByDesc('date_transaction')
            ->get();

        $pdf = Pdf::loadView('exports.transactions-pdf', compact('transactions'));
        return $pdf->download('transactions-' . now()->format('Y-m') . '.pdf');
    }
}
```

---

## 19. POLICIES

```php
// app/Domain/Transaction/Policies/TransactionPolicy.php
namespace App\Domain\Transaction\Policies;

use App\Domain\Transaction\Models\Transaction;
use App\Models\User;

class TransactionPolicy {

    public function delete(User $user, Transaction $transaction): bool {
        return $user->id === $transaction->user_id;
    }

    public function view(User $user, Transaction $transaction): bool {
        return $user->id === $transaction->user_id;
    }
}
```

Enregistrement dans `AppServiceProvider` :

```php
Gate::policy(Transaction::class, TransactionPolicy::class);
Gate::policy(Loan::class, LoanPolicy::class);
```

---

## 20. TESTS (Pest PHP)

```php
// tests/Feature/TransactionTest.php
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Enums\TransactionType;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('peut créer une transaction de dépense', function () {
    $response = $this->post(route('transactions.store'), [
        'type'             => 'depense',
        'montant'          => 5000,
        'categorie'        => 'nourriture',
        'date_transaction' => today()->format('Y-m-d'),
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->assertDatabaseHas('transactions', [
        'user_id'  => $this->user->id,
        'montant'  => 5000,
        'categorie'=> 'nourriture',
    ]);
});

it('ne peut pas créer une transaction avec un montant négatif', function () {
    $this->post(route('transactions.store'), [
        'type'    => 'depense',
        'montant' => -100,
    ])->assertSessionHasErrors('montant');
});

it('ne peut pas voir les transactions d\'un autre utilisateur', function () {
    $autreUser = User::factory()->create();
    $transaction = Transaction::factory()->for($autreUser)->create();

    $this->get(route('transactions.index'))
         ->assertDontSee($transaction->note);
});
```

---

## 21. SEEDERS

```php
// database/seeders/DemoDataSeeder.php
class DemoDataSeeder extends Seeder {

    public function run(): void {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'demo@fintrack.app'],
            [
                'name'     => 'Utilisateur Demo',
                'password' => bcrypt('password'),
            ]
        );

        // 3 mois de transactions
        for ($mois = 3; $mois >= 1; $mois--) {
            Transaction::factory(20)->create([
                'user_id'          => $user->id,
                'date_transaction' => now()->subMonths($mois - 1)->startOfMonth()
                                          ->addDays(rand(0, 27)),
            ]);
        }

        // Prêts
        \App\Domain\Loan\Models\Loan::factory(5)->create(['user_id' => $user->id]);

        // Budgets du mois courant
        foreach (config('fintrack.transaction_categories') as $cat) {
            \App\Domain\Budget\Models\Budget::create([
                'user_id'        => $user->id,
                'categorie'      => $cat,
                'montant_limite' => rand(20000, 100000),
                'mois'           => now()->month,
                'annee'          => now()->year,
            ]);
        }

        // Objectifs
        \App\Domain\Goal\Models\Goal::factory(3)->create(['user_id' => $user->id]);
    }
}
```

---

## 22. COMMANDES ARTISAN DE DÉMARRAGE

```bash
# Base de données
php artisan migrate --seed

# Compilation assets
npm install && npm run build

# Démarrage serveur (développement)
php artisan serve
php artisan queue:work --queue=default &

# Créer les tables de notifications et sessions
php artisan notifications:table
php artisan session:table
php artisan queue:table
php artisan migrate

# Lancer les tests
./vendor/bin/pest

# Vérifier le code
./vendor/bin/pint
```

---

## 23. CHECKLIST AVANT MISE EN PRODUCTION

- [ ] `APP_ENV=production` et `APP_DEBUG=false`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan optimize`
- [ ] Configurer un supervisor pour `queue:work`
- [ ] Configurer `php artisan schedule:run` en cron (rapports mensuels)
- [ ] Activer HTTPS (certificat SSL)
- [ ] Configurer les backups PostgreSQL
- [ ] Vérifier les indexes PostgreSQL sur les tables principales
- [ ] Configurer le rate limiting sur les routes sensibles
- [ ] Activer `php artisan model:prune` pour les soft deletes anciens

---

## 24. ORDRE DE DÉVELOPPEMENT RECOMMANDÉ

1. Migrations + Modèles + Enums + Factories
2. Repositories (interfaces + implémentations)
3. DTOs + Services + Actions
4. Form Requests + Controllers + Routes
5. Layout Blade + Composants réutilisables
6. Pages : Dashboard → Transactions → Prêts → Budgets → Objectifs
7. Observers + Events + Listeners + Notifications
8. Export Excel/PDF
9. Tests Pest (Feature puis Unit)
10. Seeders de démo
11. Optimisations cache + indexes PostgreSQL
