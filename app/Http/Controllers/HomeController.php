<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer; // Import model Customer
use App\Purchase; // Import model Purchase
use App\Transaction; // Import model Transaction

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Ambil data customer dari database
        $customer = Customer::count();

        $totalPurchasePrice = Purchase::sum('purchase_price');

        // Ambil total grand_total dari tabel transactions
        $totalTransaction = Transaction::sum('total');

        // Hitung profit dengan mengurangkan totalTransaction dari totalPurchasePrice
        $profit = $totalTransaction - $totalPurchasePrice;

        // Definisi variabel totalTransaction (atau ambil dari mana Anda mendapatkannya)
        $jumlahTransaction = Transaction::count(); // Contoh saja, gantilah dengan nilai sesuai kebutuhan Anda

        $jumlahPurchases = Purchase::count();

        // Render view home.blade.php dan kirim data customer, profit, dan totalTransaction ke dalam view
        return view('home', ['customer' => $customer, 'profit' => $profit, 'totalTransaction' => $totalTransaction, 'jumlahTransaction' => $jumlahTransaction, 'jumlahPurchases' => $jumlahPurchases]);
    }
}