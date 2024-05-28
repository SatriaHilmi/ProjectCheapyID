@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ $title }}</h1>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('purchases.store') }}" method="POST">
                        @csrf
                        <!-- <div class="form-group">
                            <label for="purchaseCode">Purchase Code</label>
                            <input type="text" name="purchase_code" id="purchaseCode" class="form-control" value="{{ $purchaseCode }}" required>
                        </div> -->
                        <div class="form-group">
                            <label for="supplier">Supplier</label>
                            <select name="supplier_id" id="supplier" class="form-control" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product">Product</label>
                            <select name="product_id" id="product" class="form-control" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="purchasePrice">Purchase Price</label>
                            <input type="number" name="purchase_price" id="purchasePrice" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection