<!-- Cart Modal -->

<!-- Show cart automatically when requested -->
@if (!empty(Session::get('show-cart')))
    <script type = "text/javascript">
        $(document).ready(function() {
            $('#cart').offcanvas('show');
        });
    </script>
@endif

<!-- Cart only for logged in customer -->
@auth
    @if (Auth::user()->userType == 1)
        <div class = "offcanvas offcanvas-end" data-bs-scroll = "true" id = "cart" tabindex = "-1">
            <div class = "offcanvas-header border-bottom pb-1 pt-2">
                <h4>My Cart</h4>
                <button type = "button" class = "btn-close" data-bs-dismiss = "offcanvas"></button>
            </div>
            <div class = "offcanvas-body overflow-hidden d-flex flex-column h-100 pb-2">
                @if (!$cart["empty"]) <!-- If cart not empty -->
                    <div class = "row">
                        <h5 class = "text-capitalize">{{ $cart["restaurant"]->name }}</h5>
                        <p>{{ $cart["restaurant"]->address }}</p>
                    </div>

                    <!-- Dish List -->
                    <ul class = "list-group border-top border-bottom rounded-0 overflow-auto">
                        @foreach ($cart["dishes"] as $dish)
                            <li style = "--bs-bg-opacity: .04" class = "cart-item list-group-item bg-secondary border rounded-0 border-start-0 border-end-0 border-bottom-0 border-1 py-2 @if ($loop->index == 0) border-top-0 @else border-top @endif">
                                <div class = "row align-items-center">
                                    <div class = "col-3 p-1 border rounded">
                                        <img src = "{{ asset('dish_img/default.png') }}" class = "mx-auto d-block" height = "65px">
                                    </div>
                                    <div class = "col">
                                        <h6 class = "m-0 p-0 text-capitalize">{{ $dish->name }}</h6>
                                    </div>
                                    <div class = "col-4 text-center">
                                        <h6 class = "m-0 p-0">{{ $dish->quantity }} x ${{ ceil($dish->price * (100 - $dish->promo)) / 100 }}</h6>
                                    </div>
                                    <div class = "col-1 me-2">
                                        <a type = "button" class = "btn btn-outline-danger border-0 py-0 px-1" href = '{{ url("remove-dish/$dish->id") }}'><i class = "bi bi-x"></i></a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Total Price -->
                    <div class = "row text-end mb-0 flex-grow-1 d-table my-2">
                        <h4 class = "d-table-cell align-bottom">Total ${{ cartTotal() }}</h4>
                    </div>
                @else <!-- If cart empty -->
                    <div class = "text-secondary text-center">
                        Cart Empty
                    </div>
                @endif   
            </div>
            <div class = "offcanvas-footer border p-2 btn-group">
                <a type = "button" class = "btn btn-secondary me-2 rounded" href = "{{ url('empty-cart') }}">Empty Cart</a>
                <a type = "button" class = "btn btn-primary rounded" href = "{{ url('order-from-cart') }}">Check Out</a>
            </div>
        </div>
    @endif
@endauth