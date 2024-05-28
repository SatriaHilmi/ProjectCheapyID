<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Sale;
use App\Coupon;
use App\Customer;
use App\Transaction;
use App\User;
use App\CompanyProfile;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\SaleRequest;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Transaction List";

        $items = Transaction::with(['detailTransactions', 'user'])
            ->orderByRaw("FIELD(status, 'Belum Bayar', 'Pembayaran Berhasil', 'Pesanan Dikirim', 'Pesanan Ditolak')")
            ->get();

        return view('pages.transaction.index', [
            'title' => $title,
            'items' => $items
        ]);
    }
    public function showDetails($transaction_code)
    {
        $transaction = Transaction::with(['detailTransactions.product', 'user'])
            ->where('transaction_code', $transaction_code)
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }
    public function updateStatus(Request $request, $transaction_code)
    {
        $transaction = Transaction::where('transaction_code', $transaction_code)->first();

        if (!$transaction) {
            return redirect()->back()->with('error', 'Transaction not found.');
        }

        $transaction->status = $request->input('status');
        $transaction->save();

        return redirect()->back()->with('success', 'Transaction status updated successfully.');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($transactionCode)
    {
        if (is_null($transactionCode)) {
            abort(404);
        }

        $title = "Create Transaction";

        $sales = Sale::with([
            'product'
        ])->where('transaction_code', $transactionCode);
        $items = $sales->get();
        $subTotal = $sales->sum('total_price');

        $customers = Customer::all();

        return view('pages.transaction.create', [
            'title' => $title,
            'transactionCode' => $transactionCode,
            'items' => $items,
            'customers' => $customers,
            'subTotal' => $subTotal
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request)
    {
        $request = $request->all();

        if ($request['coupon_code']) {
            $kupon = Coupon::where('coupon_code', $request['coupon_code'])->first();
            $data['coupon_id'] = $kupon->id;
        } else {
            $data['coupon_id'] = null;
        }

        $data['user_id'] = Auth::user()->id;
        $data['customer_id'] = $request['customer_id'];
        $data['discount'] = $request['discount'];
        $data['sub_total'] = str_replace(',', '', $request['sub_total']);
        $data['discount_price'] = str_replace(',', '', $request['discount_price']);
        $data['grand_total'] = str_replace(',', '', $request['grand_total']);
        $data['paid'] = str_replace(',', '', $request['paid']);
        $data['change'] = str_replace(',', '', $request['change']);
        $data['valid'] = TRUE;

        $transactionCode = now()->format('dmyHis') . Transaction::all()->count() . Auth::user()->id;

        Transaction::where('transaction_code', $request['transaction_code'])
            ->update($data);
        return redirect()->route('transaction.create', $transactionCode)->with(['success' => 'Transaksi berhasil disimpan!', 'transactionCode' => $request['transaction_code']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($transactionCode)
    {
        $title = "Transaction";

        $sales = Sale::with([
            'product'
        ])->where('transaction_code', $transactionCode);
        $items = $sales->get();
        $subTotal = $sales->sum('total_price');

        $customers = Customer::all();

        $transaction = Transaction::with([
            'customer',
            'user'
        ])
            ->where('transaction_code', $transactionCode)
            ->where('valid', TRUE)
            ->first();

        $user = User::findOrFail($transaction['user_id'])->name;

        $data = [
            'date' => $transaction->created_at->toDateTimeString(),
            'couponCode' => $transaction->coupon_id ? Coupon::find($transaction->coupon_id)->first()->coupon_code : '',
            'customerId' => $transaction->customer_id,
            'discount' => $transaction->discount,
            'paid' => $transaction->paid,
            'change' => $transaction->change,
            'user' => $user
        ];

        return view('pages.transaction.show', [
            'title' => $title,
            'transactionCode' => $transactionCode,
            'items' => $items,
            'customers' => $customers,
            'subTotal' => $subTotal,
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    /**
     * Show a transaction report by date in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $title = "Transaction Report";

        $data = $request->all();
        $date = explode(' - ', $data['date']);

        $fromDate = Carbon::parse($date[0])
            ->startOfDay()
            ->toDateTimeString();
        $toDate = Carbon::parse($date[1])
            ->endOfDay()
            ->toDateTimeString();

        $items = Transaction::whereBetween('created_at', [new Carbon($fromDate), new Carbon($toDate)])
            ->where('valid', TRUE)->get();

        return view('pages.transaction.report', [
            'title' => $title,
            'items' => $items
        ]);
    }

    public function struk($transactionCode)
    {
        $sales = Sale::with([
            'product'
        ])->where('transaction_code', $transactionCode);
        $items = $sales->get();
        $subTotal = $sales->sum('total_price');

        $transaction = Transaction::with([
            'customer',
            'user'
        ])
            ->where('transaction_code', $transactionCode)
            ->where('valid', TRUE)
            ->first();

        $customer = Customer::where('id', $transaction->customer_id)->first();
        $user = User::findOrFail($transaction['user_id'])->name;

        $companyProfile = CompanyProfile::find(1);

        $data = [
            'date' => $transaction->created_at->toDateTimeString(),
            'couponCode' => $transaction->coupon_id ? Coupon::find($transaction->coupon_id)->first()->coupon_code : '',
            'discount' => $transaction->discount,
            'paid' => $transaction->paid,
            'change' => $transaction->change,
            'grandTotal' => $transaction->grand_total,
            'user' => $user,
            'companyProfile' => $companyProfile
        ];

        return view('pages.transaction.struk', [
            'transactionCode' => $transactionCode,
            'items' => $items,
            'customer' => $customer,
            'subTotal' => $subTotal,
            'data' => $data
        ]);
    }
}
