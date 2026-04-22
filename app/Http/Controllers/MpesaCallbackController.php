<?php

namespace App\Http\Controllers;

use App\Models\MpesaDeposit;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class MpesaCallbackController extends Controller
{
    public function stk(Request $request, WalletService $walletService): JsonResponse
    {
        $payload = $request->all();

        $stk = data_get($payload, 'Body.stkCallback', []);
        $checkoutRequestId = data_get($stk, 'CheckoutRequestID');
        $merchantRequestId = data_get($stk, 'MerchantRequestID');
        $resultCode = (string) data_get($stk, 'ResultCode');
        $resultDesc = (string) data_get($stk, 'ResultDesc');

        if (! $checkoutRequestId) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $deposit = MpesaDeposit::where('checkout_request_id', $checkoutRequestId)->first();

        if (! $deposit) {
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
            report($e);
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