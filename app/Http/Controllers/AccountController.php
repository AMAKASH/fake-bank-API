<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $responsePayload = [
            'message' => 'Request Successful',
            'errors' => false,
            'payload' => Account::all(),
        ];


        return response($responsePayload, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedParams = request()->validate([
            'name' => ['required', 'string', 'max:50'],
            'initial_balance' => ['required', 'numeric'],
            'type' => ['required', 'string', 'max:10'],
            'minimum_balance' => ['required', 'numeric'],
            'customers' => ['required', 'array'],
            'customers.*' => ['required', 'int', 'distinct', 'exists:customers,id'],
            'employee_id' => ['required', 'integer'],
        ]);

        $latestId = Account::latest()->select('id')->limit(1)->get();

        if (count($latestId) > 0) {
            $newAccountNumber = 'ACCBANKPREFIX' . $latestId[0]['id'] + 1;
        } else {
            $newAccountNumber = 'ACCBANKPREFIX' .  1;
        }


        $newAcc = new Account();
        $newAcc->acc_name = $validatedParams['name'];
        $newAcc->acc_type = $validatedParams['type'];
        $newAcc->acc_number  = $newAccountNumber;
        $newAcc->acc_balance = $validatedParams['initial_balance'];
        $newAcc->min_balance = $validatedParams['minimum_balance'];
        $newAcc->save();

        $newAcc->customers()->sync($validatedParams['customers']);

        $responsePayload = [
            'message' => 'Account Created',
            'errors' => false,
            'payload' => $newAcc->details()
        ];
        return response($responsePayload, 201);
    }

    /**
     * Display the specified account.
     */
    public function show($id)
    {

        $account = Account::find($id);

        if (!$account) {
            $responsePayload = [
                'message' => 'Account not found',
                'errors' => [
                    'id' => ['An account with the current ID is not present']
                ],
            ];
            return response($responsePayload, 404);
        }

        $account['customers'] = $account->customers;
        $responsePayload = [
            'message' => 'Request Successful',
            'errors' => false,
            'payload' => $account
        ];
        return response($responsePayload, 200);
    }

    /**
     * Display the specified account balance.
     */
    public function showBalance($id)
    {
        $account = Account::find($id);

        if (!$account) {
            $responsePayload = [
                'message' => 'Account not found',
                'errors' => [
                    'id' => ['An account with the current ID is not present']
                ],
            ];
            return response($responsePayload, 404);
        }

        $account['customers'] = $account->customers;
        $payload['id'] = $account->id;
        $payload['acc_number'] = $account->acc_number;
        $payload['acc_balance'] = $account->acc_balance;
        $payload['min_balance'] = $account->min_balance;

        $responsePayload = [
            'message' => 'Request Successful',
            'errors' => false,
            'payload' => $payload
        ];


        return response($responsePayload, 200);
    }

    public function transferHistory($id)
    {

        $account = Account::find($id);

        if (!$account) {
            $responsePayload = [
                'message' => 'Account not found',
                'errors' => [
                    'id' => ['An account with the current ID is not present']
                ],
            ];
            return response($responsePayload, 404);
        }


        $history = $account->transfer_history();
        $formatedHistory = [];
        $balance = $account->acc_balance;

        for ($i = count($history) - 1; $i > 0; $i--) {
            $formatedTransaction = [
                'amount' => $history[$i]->amount,
                'status' => $history[$i]->status,
                'date' => $history[$i]->created_at,
                'balance' => $balance,
            ];

            if ($account->id == $history[$i]->sender_id) {
                $formatedTransaction['type'] = 'DEBIT';
                if ($history[$i]->status == 'ACCEPTED') {
                    $balance += $history[$i]->amount;
                }
                $formatedTransaction['details'] =
                    "Transfered To {$history[$i]->recipient_id} - {$history[$i]->details}";
            } elseif ($account->id == $history[$i]->recipient_id) {
                $formatedTransaction['type'] = 'CREDIT';
                if ($history[$i]->status == 'ACCEPTED') {
                    $balance -= $history[$i]->amount;
                }
                $formatedTransaction['details'] = "Recieved From {$history[$i]->sender_id} - {$history[$i]->details}";
            }
            array_unshift($formatedHistory, $formatedTransaction);
        }
        //Initial Deposit Transaction
        $formatedTransaction = [
            'amount' => $balance,
            'status' => 'APPROVED',
            'date' => $account->created_at,
            'balance' => $balance,
            'type' => "CREDIT",
            'details' => 'Initial Deposit'
        ];
        array_unshift($formatedHistory, $formatedTransaction);


        $responsePayload = [
            'message' => 'Request Successful',
            'errors' => false,
            'payload' => $formatedHistory
        ];


        return response($responsePayload, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        //
    }
}
