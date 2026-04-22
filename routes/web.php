<?php
use App\Http\Controllers\MpesaCallbackController;
use App\Livewire\Categories;
use App\Livewire\BudgetList;
use App\Livewire\BudgetForm;
use App\Livewire\ExpenseList;
use App\Livewire\ExpenseForm;
use App\Livewire\RecurringExpense;
use App\Livewire\Wallet\WalletIndex;
use App\Livewire\Wallet\DepositFunds;
use App\Livewire\Wallet\SavingsGoals;
use App\Livewire\Wallet\WithdrawFunds;
use App\Livewire\Wallet\TransactionHistory;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('categories', Categories::class)->name('categories.index');
    Route::get('budgets',BudgetList::class)->name('budget.index');
    Route::get('budgets/create', BudgetForm::class)->name('budget.create');
    Route::get('budgets/{budgetId}/edit', BudgetForm::class)->name('budget.edit');
    
    //expenses
    Route::get('expenses', ExpenseList::class)->name('expenses.index');
    Route::get('/expenses/create',ExpenseForm::class)->name('expenses.create');
    Route::get('expenses/{expenseId}/edit',ExpenseForm::class)->name('expenses.edit');
    Route::get('recurring-expenses',RecurringExpense::class)->name('recurring-expenses.index');


    //wallet
    Route::get('wallet', WalletIndex::class)->name('wallet.index');
    Route::get('wallet/deposit', DepositFunds::class)->name('wallet.deposit');
    Route::get('wallet/goals', SavingsGoals::class)->name('wallet.goals');
    Route::get('wallet/withdraw', WithdrawFunds::class)->name('wallet.withdraw');
    Route::get('wallet/transactions', TransactionHistory::class)->name('wallet.transactions');
    Route::post('mpesa/callback/stk', [MpesaCallbackController::class, 'stk'])
    ->name('mpesa.callback.stk');

    });

require __DIR__.'/settings.php';
