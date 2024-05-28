<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Purchase;
use App\Supplier;
use App\Product;
use App\CompanyProfile;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\PurchaseRequest;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Purchase List";

        $items = Purchase::with([
            'supplier',
            'product'
        ])->get();

        return view('pages.purchases.index', [
            'title' => $title,
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($purchaseCode = null)
    {
        $title = "Create Purchase";

        $suppliers = Supplier::all();
        $products = Product::all();

        return view('pages.purchases.create', [
            'title' => $title,
            'purchaseCode' => $purchaseCode,
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->route('purchases.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::user()->id;
        $data['purchase_code'] = now()->format('dmyHis') . Purchase::count() . Auth::user()->id;

        Purchase::create($data);

        return redirect()->route('purchases.index')->with('success', 'Purchase successfully created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $purchaseCode
     * @return \Illuminate\Http\Response
     */
    public function show($purchaseCode)
    {
        $title = "Purchase";

        $purchase = Purchase::with([
            'supplier',
            'product'
        ])->where('purchase_code', $purchaseCode)->firstOrFail();

        $data = [
            'date' => $purchase->created_at->toDateTimeString(),
            'supplier' => $purchase->supplier->name,
            'product' => $purchase->product->name,
            'quantity' => $purchase->quantity,
            'purchase_price' => $purchase->purchase_price,
            'total_price' => $purchase->quantity * $purchase->purchase_price
        ];

        return view('pages.purchases.show', [
            'title' => $title,
            'purchaseCode' => $purchaseCode,
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = "Edit Purchase";
        $purchase = Purchase::findOrFail($id);
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('pages.purchases.edit', compact('title', 'purchase', 'suppliers', 'products'));
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
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->route('purchases.edit', $id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $purchase = Purchase::findOrFail($id);
        $purchase->update($validator->validated());

        return redirect()->route('purchases.index')
                        ->with('success', 'Purchase successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     *
* @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();

        return redirect()->route('purchases.index')
                        ->with('success', 'Purchase successfully deleted!');
    }

    /**
     * Show a purchase report by date in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $title = "Purchase Report";

        $data = $request->all();
        $date = explode(' - ', $data['date']);

        $fromDate   = Carbon::parse($date[0])
            ->startOfDay()
            ->toDateTimeString();
        $toDate     = Carbon::parse($date[1])
            ->endOfDay()
            ->toDateTimeString();

        $items = Purchase::whereBetween('created_at', [new Carbon($fromDate), new Carbon($toDate)])
            ->get();

        return view('pages.purchases.report', [
            'title' => $title,
            'items' => $items
        ]);
    }

    public function receipt($purchaseCode)
    {
        $purchase = Purchase::with([
            'supplier',
            'product'
        ])->where('purchase_code', $purchaseCode)->firstOrFail();

        $supplier = $purchase->supplier;
        $product = $purchase->product;

        $companyProfile = CompanyProfile::find(1);

        $data = [
            'date' => $purchase->created_at->toDateTimeString(),
            'supplier' => $supplier->name,
            'product' => $product->name,
            'quantity' => $purchase->quantity,
            'purchase_price' => $purchase->purchase_price,
            'total_price' => $purchase->quantity * $purchase->purchase_price,
            'companyProfile' => $companyProfile
        ];

        return view('pages.purchases.receipt', [
            'purchaseCode' => $purchaseCode,
            'data' => $data
        ]);
    }
}
