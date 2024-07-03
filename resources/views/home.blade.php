@extends('layouts.app')

@section('content')
<main class="py-4 container">
    <div class="container">
        <h2>Product</h2>
        <div class="row">
            @foreach($allProducts as $product)

                <div class="card">
                    <img class="card-img-top" src="{{ asset('images/no_image.jpg') }}" alt="Card image cap">
                    <div class="card-body">
                        <h4 class="card-title">aaa</h4>
                        <p class="card-text"></p>
                    </div>
                    <div class="card-body">
                        <a href="" class="card-link">Add to Cart</a>
                    </div>
                </div>

            @endforeach   
        </div>
        
    </div>
</main>
@endsection

