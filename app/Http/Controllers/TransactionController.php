<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{


    public function index()
    {
        $responsePayload = [
            'message' => 'Request Successful',
            'errors' => false,
            'payload' => Transaction::all(),
        ];


        return response($responsePayload, 200);
    }


    public function create(Request $request)
    {
        $validatedInputs = request()->validate([
            'sender_id' => ['integer', 'exists:accounts,id', Rule::when(!$request['deposit'], ['required'])],
            'recipient_id' => ['integer', 'exists:accounts,id', Rule::when(!$request['withdraw'], ['required'])],
            'amount' => ['required', 'numeric'],
            'employee_id' => ['required', 'integer'],
            'withdraw' => ['boolean'],
            'deposit' => ['boolean'],
            'comments' => ['string', 'max:500']
        ]);


        if (
            isset($validatedInputs['withdraw']) && $validatedInputs['withdraw']
            && isset($validatedInputs['deposit']) && $validatedInputs['deposit']
        ) {
            $errorMsg = "Both 'withdraw' & 'deposit' flags cannot be true simultaniously";
            return response([
                'message' => $errorMsg,
                'errors' => [
                    'withdraw' => [$errorMsg],
                    'deposit' =>  [$errorMsg],
                ],
            ], 422);
        }

        $result = $this->attemptTransaction($validatedInputs);

        $newTransaction = new Transaction();
        $newTransaction->sender_id = $validatedInputs['sender_id'] ?? null;
        $newTransaction->recipient_id = $validatedInputs['recipient_id'] ?? null;
        $newTransaction->amount = $validatedInputs['amount'];
        $newTransaction->status = $result['success'] ? 'ACCEPTED' : 'DENIED';
        $newTransaction->employee_id = $validatedInputs['employee_id'];
        $newTransaction->details = ($validatedInputs['comments'] ?? '') . '. ' . $result['msg'];
        $newTransaction->save();


        if (!$result['success']) {
            return response([
                'message' => 'Transaction Denied',
                'errors' => ['amount' => [$result['msg']]]
            ], 409);
        } else {
            return response([
                'message' => 'Transaction accepted',
                'errors' => false,
                'payload' => $newTransaction
            ], 201);
        }
    }

    public function attemptTransaction($validatedInputs)
    {
        $result = [
            'success' => true,
            'msg' => '',
        ];

        if (isset($validatedInputs['withdraw']) && $validatedInputs['withdraw']) {
            $result['msg'] = '[WITHDRAW]';
        } elseif (isset($validatedInputs['deposit']) && $validatedInputs['deposit']) {
            $result['msg'] = '[DEPOSIT]';
        }

        if (isset($validatedInputs['sender_id'])) {
            $sender = Account::findOrFail($validatedInputs['sender_id']);

            if (
                $sender->acc_balance < ($sender->min_balance + $validatedInputs['amount'])
            ) {
                $result['success'] = false;
                $result['msg'] .= ' Transactions Failed Due to Insufficient Fund';
                return $result;
            } else {
                $sender->acc_balance -= $validatedInputs['amount'];
                $sender->save();
            }
        }

        if (isset($validatedInputs['recipient_id'])) {
            $recipient = Account::findOrFail($validatedInputs['recipient_id']);
            $recipient->acc_balance += $validatedInputs['amount'];
            $recipient->save();
        }


        return $result;
    }
}
