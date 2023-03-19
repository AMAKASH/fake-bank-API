<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Return a list of all customers.
     */
    public function index()
    {
        $customers =  Customer::all();
        $responsePayload = [
            'message' => 'Request Successful',
            'errors' => false,
            'payload' => $customers
        ];
        return response($responsePayload, 200);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        //
    }


    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
