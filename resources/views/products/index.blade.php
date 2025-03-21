@extends('layouts.app')

@section('content')



<div class="container">
    <h1 class="mb-4">商品一覧画面</h1>

    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">商品新規登録</a>

     <div class="search mt-5">
    
    
    <form action="{{ route('products.index') }}" method="GET" class="row g-3">
        @csrf
        <div class="col-sm-12 col-md-4">
            <input type="text" name="search" class="form-control" placeholder="検索キーワード" value="{{ request('search') }}">
        </div>

        <div class="col-sm-12 col-md-4">
                <select class="form-select" name="search-company" value="{{ request('searchCompany') }}" placeholder="メーカーを選択">
                    <option value="" selected>メーカー名</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                    @endforeach
                </select>    
        </div>

        <div class="w-100"></div>


        <div class="col-sm-12 col-md-2">
            <input type="number" name="min_price" class="form-control" placeholder="最小価格" value="{{ request('min_price') }}">
        </div>

        <div class="col-sm-12 col-md-2">
            <input type="number" name="max_price" class="form-control" placeholder="最大価格" value="{{ request('max_price') }}">
        </div>

        <div class="col-sm-12 col-md-2">
            <input type="number" name="min_stock" class="form-control" placeholder="最小在庫" value="{{ request('min_stock') }}">
        </div>

        <div class="col-sm-12 col-md-2">
            <input type="number" name="max_stock" class="form-control" placeholder="最大在庫" value="{{ request('max_stock') }}">
        </div>


        <div class="col-sm-12 col-md-1">
            <button class="btn btn-outline-secondary" type="submit">検索</button>
        </div>
        </form>
        </form>
</div>

<script>
        $(document).ready(function() {
            $('#search').on('input', function() {
                e.preventDefault();

                let searchKeyword = $('#search-keyword').val();
                let searchCompany = $('#search-company').val();
                let minPrice = $('#min-price').val();
                let maxPrice = $('#max-price').val();

                $.ajax({
                    url: '{{ route('search') }}',
                    method: 'GET',
                    data: {
                        search: searchKeyword,
                        search_company: searchCompany,
                        min_price: minPrice,
                        max_price: maxPrice,},
                        dataType: 'json',
                        success: function (response) {
                            let resultsHtml = '';
                            if (response.length > 0) {
                                response.forEach(function (product) {
                                    resultsHtml += `
                                    <tr>
                                    <td>${product.id}</td>
                                    <td><img src="${product.img_path}" alt="${product.product_name}" width="100"></td>
                                    <td>${product.product_name}</td>
                                    <td>${product.price}</td>
                                    <td>${product.stock}</td>
                                    <td>${product.company_name}</td>
                                    </tr>
                                    `;
                                });
                            } else {
                                resultsHtml = '<tr><td colspan="6">該当する商品がありません。</td></tr>';
                            }
                            $('#search-results').html(resultsHtml);
                        },
                        error: function () {
                            alert('検索に失敗しました');
                        }
                });
            });
        });
    </script>


    <div class="products mt-5">
        <h2>商品情報</h2>
        <table class="table table-striped">
            <thead>
            <tr>
            <th>ID</th>
            <th>商品画像</th>
            <th>商品名</th>
            <th>価格
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => 'asc']) }}">↑</a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => 'desc']) }}">↓</a>
            </th>
            <th>在庫数
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock', 'direction' => 'asc']) }}">↑</a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock', 'direction' => 'desc']) }}">↓</a>
            </th>
            <th>メーカー名</th>
            </tr>
            </thead>
            <tbody id="search-results">
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>
                    @if ($product->img_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->img_path))
                    <img src="{{ asset('storage/' . $product->img_path) }}" alt="{{ $product->product_name }}" width="100">
                    @else
                    <img src="{{ asset($product->img_path) }}" alt="商品画像" width="100">
                    @endif
                    </td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->company->company_name }}</td>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細</a>                       
                         <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm mx-1">削除</button>
                        </form>
                        
                        <form class="sale-form mt-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control d-inline-block w-25">
                            <button type="submit" class="btn btn-success btn-sm mx-1 sale-button" data-product-id="{{ $product->id }}">購入</button>
                            </form>
                    </td>
                </tr>
            @endforeach

            <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll(".sale-form").forEach((form) => {
                    form.addEventListener("submit", function (e) {
                        e.preventDefault();
                        
                        let formData = new FormData(this);
                        let productId = formData.get("product_id");
                        let quantity = formData.get("quantity");
                        
                        fetch('api/sales', {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                            },
                            
                            body: JSON.stringify({
                                product_id: productId,
                                quantity: quantity
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            if (data.message === "購入が完了しました。") {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            alert("購入に失敗しました");
                            console.error("エラー:", error);
                        });
                    });
                });
                });
                </script>

            <script>
            $(document).ready(function() {
                $('.delete-btn').on('click', function() {
                    var productId = $(this).data('id');
                    var row = $(this).closest('div');
                    
                    if (confirm('本当に削除しますか？')) {
                        $.ajax({
                            url: '/product/' + productId,
                            method: 'DELETE',
                            success: function(response) {
                                row.remove();
                                alert(response.message); 
                            }
                            error: function() {
                                alert('削除に失敗しました。');
                            }
                        });
                    }
                });
            });
            </script>
            
            <script>
            fetch('api/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    "Accept": "application/json",
                },
                body: JSON.stringify({
                    product_id: 1,
                    quantity: 2
                })
            })
            .then(response => response.json())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
            
            fetch('api/sales')
            .then(response => response.json())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
            
            fetch('api/sales', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            })
            .then(response => response.json())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
            </script>

            </tbody>
        </table>
    </div>
    
    
    {{ $products->appends(request()->query())->links() }}
</div>
@endsection