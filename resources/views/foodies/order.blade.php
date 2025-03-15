<!-- Order List Page -->

@extends("layouts.master")

@section("content")
    <div class = "container h-100 m-auto" style = "padding-top: 65px;">

        @if ($user->userType == 1)
            <h1 class = "title-font">Order History</h1>
        @else
            <h1 class = "title-font">Customer Orders</h1>
        @endif

        <!-- Order List -->
        <div class = "row row-cols-2 row-cols-lg-4">
            @forelse ($purchases as $purchase)
                <div class = "col mb-3">
                    <div class = "border rounded p-2 h-100 d-flex flex-column" type = "button" data-bs-toggle = "modal" data-bs-target = "#purchase{{ $purchase->id }}">
                        @if ($user->userType == 1)
                        <h4 class = "text-capitalize">{{ $purchase->restaurant->name }}</h4>
                        @else
                        <h4>{{ $purchase->customer->name}}</h4>
                        @endif
                        <h6 class = "mb-0">Delivery Address</h6>
                        <p>{{ $purchase->address }}</p>
                        <div class = "d-table flex-grow-1">
                            <p class = "d-table-cell align-bottom text-secondary mb-1">{{ $purchase->created_at->format("j M Y, g:ia") }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Details Modal -->
                <div class = "modal fade" id = "purchase{{$purchase->id}}">
                    <div class = "modal-dialog modal-lg">
                        <div class = "modal-content">
                            <div class = "modal-header py-2">
                                <h4 class = "modal-title ms-1">Order Details</h4>
                                <button type = "button" class = "btn-close" data-bs-dismiss = "modal"></button>
                            </div>
                            <div class = "modal-body">
                                @if ($user->userType == 1)
                                <h4 class = "mx-2 text-capitalize">{{ $purchase->restaurant->name }}</h4>
                                @else
                                <h4 class = "mx-2">{{ $purchase->customer->name}}</h4>
                                @endif
                                <p class = "mx-2 text-secondary mt-0 mb-1">{{ $purchase->created_at->format("j M Y, g:ia") }}</p>
                                <h6 class = "mx-2 mb-0">Delivery Address</h6>
                                <p class = "mx-2">{{ $purchase->address}}</p>
                                <hr class = "mb-0">
                                <table class = "table table-borderless my-0">
                                    <thead class = "border-bottom">
                                        <tr>
                                            <th scole = "col" width = "5%">No.</th>
                                            <th scole = "col">Dish</th>
                                            <th scope = "col" class = "text-center" width = "12%">Price($)</th>
                                            <th scope = "col" class = "text-center" width = "15%">Quantity</th>
                                            <th scope = "col" class = "text-center" width = "12%">Total($)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchase->dishes as $dish)
                                            <tr>
                                                <th scope = "row">{{ $loop->index + 1 }}</th>
                                                <td>
                                                    <span class = "text-capitalize">{{ $dish->name }}</span>
                                                    @if ($dish->pivot->promo > 0)
                                                        <span class = "badge bg-danger ms-1">{{ $dish->pivot->promo }}% OFF</span>
                                                    @endif
                                                </td>
                                                <td class = "text-center">
                                                    {{ ceil($dish->pivot->price * (100 - $dish->pivot->promo)) / 100 }}
                                                </td>
                                                <td class = "text-center">{{ $dish->pivot->quantity }}</td>
                                                <td class = "text-center">{{ ceil($dish->pivot->price * (100 - $dish->pivot->promo) * $dish->pivot->quantity) / 100 }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class = "border-top">
                                            <th colspan = "4" class = "text-end">Total</th>
                                            <th class = "text-center">{{ $purchase->total }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class = "modal-footer py-1">
                                <button type = "button" class = "btn btn-primary" data-bs-dismiss = "modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class = "text-secondary fs-5 ms-1">No order.</p>
            @endforelse
        </div>
    </div>
@endsection