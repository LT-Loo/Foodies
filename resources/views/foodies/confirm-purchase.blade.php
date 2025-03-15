<!-- Confirm Purchase Notice -->

@extends("layouts.master")

@section("css")
    <link rel="stylesheet" type="text/css" href= "{{ asset('css/confirm-purchase.css') }}">
@endsection

@section("content")
    <!-- Open New Address Modal automatically if error received -->
    @if (count($errors) > 0)
        <script type = "text/javascript">
            $(document).ready(function() {
                $('#new-address').modal('show');
            });
        </script>
    @endif

    <!-- Display warning if attempt to add same dish into cart -->
    @if (!empty(Session::get('error')))
        <div class = "alert alert-warning border border-warning bg-opacity-0 fade show alert-dismissible position-fixed position-relative top-50 start-50 translate-middle" role = "alert">
            <div class = "lh-1">
                <b>Attempt to add dish from different restaurant</b><br><br>
                You already have dishes from <b>{{ Session::get('error') }}</b> in your cart.<br><br>
                Please make sure to empty your cart before ordering from a different restaurant.
            </div>
            <button type = "button" class = "btn-close position-absolute end-0 top-0 p-1 m-2" data-bs-dismiss = "alert"></button>
        </div>  
    @endif

    <!--  Display alert if attempt to add same dish into cart -->
    @if (!empty(Session::get('duplicate')))
        <div class = "alert alert-warning border border-warning bg-opacity-0 fade show alert-dismissible position-fixed position-relative top-50 start-50 translate-middle" role = "alert">
            <div class = "lh-1">
                <b>{{ Session::get('duplicate') }} already in cart.</b>
            </div>
            <button type = "button" class = "btn-close btn-sm position-absolute end-0 top-0 p-0 m-2" data-bs-dismiss = "alert"></button>
        </div>  
    @endif
    
    <!-- Page Content -->
    <div class = "container h-100 border-start border-end m-auto px-2" style = "padding-top: 65px;">

        @if (!$confirm)
            <h1>Order Confirmation</h1> 
        @else
            <h1>Order Placed!</h1> 
        @endif 

        <hr class = "my-0">

        <div class = "row pt-2 ps-1">
            <!-- Customer Details -->
            <div class = "col-lg border-end">
                <h3>Customer Information</h5>
                <table class = "table table-borderless">
                    <tr>
                        <th width = "30%">Name</th><td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Delivery Address</th>
                        <td>{{ $order["address"]->address }}
                        @if (!$confirm)
                            <span type = "button" class = "btn pt-1 pb-0 px-2 ms-3" data-bs-toggle = "modal" data-bs-target = "#select-address"><i class = "bi bi-house" style = "vertical-align: 0.5em;"></i></span>
                        @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Select Address Modal -->
            <div class = "modal fade" id = "select-address" tabindex = "-1">
                <div class = "modal-dialog">
                    <div class = "modal-content">
                        <div class = "modal-header py-2">
                            <h4 class = "modal-title">Choose Your Delivery Address</h4>
                            <button type = "button" class = "btn-close" data-bs-dismiss = "modal"></button>
                        </div>
                        <form method = "post" action = '{{ url("select-address") }}'>
                            @csrf
                        <div class = "modal-body">
                            <!-- Selected Address -->
                            <div class = "border border-dark rounded px-3 py-2">
                                <h5>Current Address</h5>
                                <span>{{ $order["address"]->address }}</span>
                            </div>

                            <!-- Address List -->
                            <div class = "btn-group d-flex flex-column" role = "group">
                                <hr>
                                @foreach ($user->addresses as $addr)
                                    @if ($order["address"]->id != $addr->id)
                                    @if ($loop->index != 0)
                                    <hr>
                                    @endif
                                    <div class = "my-1">
                                        <input type = "radio" class = "btn-check" name = "address" value = "{{ $addr->id }}" id = "addr{{ $addr->id }}" autocomplete = "off">
                                        <label class = "btn h-100 w-100 text-start" for = "addr{{ $addr->id }}">{{ $addr->address }}</button>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class = "modal-footer">
                            <a type = "button" class = "btn btn-primary" data-bs-toggle = "modal" data-bs-target = "#new-address">New Address</a>
                            <input type = "submit" value = "Save" class = "btn btn-primary">
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Restaurant Details -->
            <div class = "col-lg">
                <h3>Restaurant Information</h5>
                <table class = "table table-borderless">
                    <tr>
                        <th width = "20%" class = "text-capitalize">Name</th><td>{{ $order["restaurant"]->name }}</td>
                    </tr>
                    <tr>
                        <th>Address</th><td>{{ $order["restaurant"]->address }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr class = "my-0">

        <!-- Order Details -->
        <div class = "row pt-2 px-1">
            <h3>Order</h3>
            <table class = "table table-borderless border-2 border-top border-bottom">
                <thead class = "border-bottom">
                    <tr>
                        <th scope = "col" class = "text-center" width = "5%">No.</th>
                        <th scope = "col">Dish</th>
                        <th scope = "col" class = "text-center" width = "10%">Price ($)</th>
                        <th scope = "col" class = "text-center" width = "10%">Quantity</th>
                        <th scope = "col" class = "text-center" width = "10%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order["dishes"] as $dish)
                    <tr>
                        <th scope = "row" class = "text-center">{{ $loop->index + 1}}</th>
                        <td>
                            <span class = "text-capitalize">{{ $dish->name }}</span>
                            @if ($dish->promo > 0) 
                                <span class = "badge bg-danger ms-1">{{ $dish->promo }}% OFF</span>
                            @endif
                        </td>
                        <td class = "text-center">
                            {{ ceil($dish->price * (100 - $dish->promo)) / 100 }}   
                        </td>
                        <td class = "text-center">{{ $dish->quantity }}</td>
                        <td class = "text-center">{{ ceil($dish->price * (100 - $dish->promo) * $dish->quantity) / 100 }}</td>
                    </tr>
                    @endforeach
                    <tr class = "border-top">
                        <th colspan = "4" class = "text-end">Total</th>
                        <th class = "text-center">{{ $order["total"] }}</th>
                    </tr>    
                </tbody> 
            </table>
        </div>

        <!-- Buttons -->
        <div class = "text-end pb-3"> 
            @if (!$confirm) 
                <a type = "button" class = "btn btn-secondary rounded me-2" href = '{{ url("add-cart") }}'>Add More Dishes</a>
                <a type = "button" class = "btn btn-primary rounded" href = '{{ url("purchase") }}'>Place Order</a>
            @else
                <a type = "button" class = "btn btn-primary" href = '{{ url("/") }}'>Back to Home</a>
            @endif
        </div>
    </div>

    <!-- New Address Modal -->
    <div class = "modal fade" id = "new-address">
        <div class = "modal-dialog">
            <div class = "modal-content">
                <div class = "modal-header py-2">
                    <h4 class = "modal-title">Add New Address</h4>
                    <button type = "button" class = "btn-close" data-bs-dismiss = "modal"></button>
                </div>
                <div class = "modal-body">
                    <!-- New Address Form -->
                    <form method = "post" action = '{{ url("new-address") }}'>
                        @csrf
                        <div class = "mb-3">
                            <label class = "form-label">Address</label>
                            <textarea type = "text" class = "form-control" name = "address" placeholder = "Address" rows = "3">{{ old('address') }}</textarea>
                            @if ($errors->get('address'))
                                <span class = "text-danger fw-light">** {{ $errors->first('address') }}</span>
                            @endif
                        </div>
                        <div class = "form-check mb-3">
                            <input class = "form-check-input" type = "checkbox" name = "default" id = "set-as-default" value = "true">
                            <label class = "form-check-label" for = "set-as-default">Set as Default Address</label>
                        </div>
                        <div class = "row">
                            <div class = "col">
                                <button type = "button" class = "btn btn-secondary" data-bs-toggle = "modal" data-bs-target = "#select-address">Back</button>
                            </div>
                            <div class = "col text-end">
                                <input type = "submit" class = "btn btn-primary" value = "Save">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection