<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Beneficiary;
use App\Models\Card;
use App\Models\CardPayment;
use App\Models\CardRequest;
use App\Models\Investment;
use App\Models\ManagerUser;
use App\Models\Messages;
use App\Models\Permission;
use App\Models\Pos;
use App\Models\PosPayment;
use App\Models\Project;
use App\Models\RecurringDebitMandate;
use App\Models\Role;
use App\Models\Token;
use App\Models\TransactionEntry;
use App\Models\User;
use App\Options\DefaultRoles;
use App\Policies\AccountPolicy;
use App\Policies\BeneficiaryPolicy;
use App\Policies\CardPaymentPolicy;
use App\Policies\CardRequestPolicy;
use App\Policies\CardsPolicy;
use App\Policies\InvestmentPolicy;
use App\Policies\ManagerPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PosPaymentPolicy;
use App\Policies\PosPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RecurringDebitMandatePolicy;
use App\Policies\RolePolicy;
use App\Policies\TokenPolicy;
use App\Policies\TransactionEntryPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Project::class           => ProjectPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();
    }

}
