<?php

namespace App\Livewire\Wallet;
use App\Services\MpesaService;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Services\WalletService;
use RuntimeException;

#[Title('Deposit Funds - ExpenseApp')]
class DepositFunds extends Component
{
    public string $phone_number = '';
    public string $amount = '';
    public ?string $statusMessage = null;

    protected function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'min:10', 'max:20'],
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function mount(): void
    {
        $this->phone_number = (string) (Auth::user()->phone_number ?? '');
    }

    public function initiateDeposit(WalletService $walletService, MpesaService $mpesaService): void
{
    $this->validate();

    try {
        $deposit = $walletService->createInitiatedMpesaDeposit(
            Auth::id(),
            $this->phone_number,
            (float) $this->amount,
            ['source' => 'livewire.deposit-funds']
        );

        $response = $mpesaService->stkPush(
            $this->phone_number,
            (float) $this->amount,
            'Wallet-' . $deposit->id,
            'Wallet deposit'
        );

        $deposit->update([
            'merchant_request_id' => data_get($response, 'MerchantRequestID'),
            'checkout_request_id' => data_get($response, 'CheckoutRequestID'),
            'result_code' => (string) data_get($response, 'ResponseCode'),
            'result_desc' => (string) data_get($response, 'ResponseDescription'),
            'status' => 'pending',
            'request_payload' => $response,
        ]);

        session()->flash('message', 'STK push sent. Complete payment on your phone.');
        $this->reset('amount');
    } catch (RuntimeException $e) {
        $this->addError('amount', $e->getMessage());
    }
}

    public function render()
    {
        return view('livewire.deposit-funds');
    }
}