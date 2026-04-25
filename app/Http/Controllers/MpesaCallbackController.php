<?php

namespace App\Http\Controllers;

use App\Models\MpesaDeposit;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MpesaCallbackController extends Controller
{
    public function stk(Request $request, WalletService $walletService): JsonResponse
    {
        Log::info('M-Pesa callback received', [
            'path' => $request->path(),
            'has_body' => !empty($request->all()),
            'payload' => $request->all(),
        ]);
        $payload = $request->all();

        $stk = data_get($payload, 'Body.stkCallback', []);
        $checkoutRequestId = data_get($stk, 'CheckoutRequestID');
       
        $merchantRequestId = data_get($stk, 'MerchantRequestID');
        $resultCode = (string) data_get($stk, 'ResultCode');
        $resultDesc = (string) data_get($stk, 'ResultDesc');

        if (! $checkoutRequestId) {
            Log::warning('M-Pesa callback missing checkout request id', [
                'payload' => $payload,
            ]);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $deposit = MpesaDeposit::where('checkout_request_id', $checkoutRequestId)->first();

        if (! $deposit) {
            Log::warning('M-Pesa callback deposit not found', [
                'checkout_request_id' => $checkoutRequestId,
            ]);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        try {
            DB::transaction(function () use ($deposit, $stk, $merchantRequestId, $resultCode, $resultDesc, $walletService): void {
                $lockedDeposit = MpesaDeposit::whereKey($deposit->id)->lockForUpdate()->firstOrFail();

                if ($lockedDeposit->status === 'completed') {
                    return;
                }

                $lockedDeposit->merchant_request_id = $merchantRequestId;
                $lockedDeposit->result_code = $resultCode;
                $lockedDeposit->result_desc = $resultDesc;
                $lockedDeposit->callback_payload = $stk;

                if ($resultCode === '0') {
                    $amount = (float) $this->callbackItem($stk, 'Amount', $lockedDeposit->amount);
                    $receipt = (string) $this->callbackItem($stk, 'MpesaReceiptNumber', '');
                    Log::info('M-Pesa callback crediting wallet', [
                        'deposit_id' => $lockedDeposit->id,
                        'user_id' => $lockedDeposit->user_id,
                        'amount' => $amount,
                        'receipt' => $receipt,
                        'checkout_request_id' => $lockedDeposit->checkout_request_id,
                    ]);
                    $walletService->creditDeposit(
                        $lockedDeposit->user_id,
                        $amount,
                        $receipt !== '' ? $receipt : null,
                        $lockedDeposit->checkout_request_id,
                        ['callback' => $stk]
                    );

                    $lockedDeposit->status = 'completed';
                    $lockedDeposit->mpesa_receipt_number = $receipt !== '' ? $receipt : null;
                    $lockedDeposit->completed_at = now();
                } else {
                    $lockedDeposit->status = 'failed';
                }

                $lockedDeposit->save();
            });
        } catch (Throwable $e) {
            Log::error('M-Pesa callback processing failed', [
                'error' => $e->getMessage(),
                'checkout_request_id' => $checkoutRequestId,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    private function callbackItem(array $stkCallback, string $key, mixed $default = null): mixed
    {
        $items = data_get($stkCallback, 'CallbackMetadata.Item', []);

        foreach ($items as $item) {
            if (data_get($item, 'Name') === $key) {
                return data_get($item, 'Value', $default);
            }
        }

        return $default;
    }
}
