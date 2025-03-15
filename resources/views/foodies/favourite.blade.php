<!-- Favourite Dishes List Page -->

@extends("layouts.master")

@section("css")
    <link rel="stylesheet" type="text/css" href= "{{ asset('css/restaurant.css') }}">
@endsection

@section("content")
    <div class = "container m-auto" style = "padding-top: 65px;">
        <h1 class = "title-font">My Favourite</h1>

        <div class = "row row-cols-2 row-cols-lg-3 justify-content-center">
            @forelse ($favourites as $dish)
            <div class = "col mb-3">
                <div class = "card position-relative h-100">
                    <!-- Promo Label -->
                    @if ($dish->promo > 0)
                        <h4 class = "promo position-absolute start-0 top-0 text-danger mt-2 ms-2">{{ $dish->promo }}% OFF</h4>
                    @endif

                    <!-- Dish Photo -->
                    <img src = '{{ asset("$dish->pfp") }}' class = "card-img-top rounded mx-auto w-75 mt-2" height = "160"> 

                    <!-- Dish Info -->
                    <div class = "card-body position-relative">
                        <div class = "row">
                            <div class = "col">
                                <h5 class = "title-card text-capitalize text-break mb-0">{{ $dish->name }}</h5>
                                <a class = "card-btn mb-1 fw-semibold text-decoration-none text-dark position-relative" href = '{{ url("restaurant/$dish->restaurant_id") }}'>{{ $dish->restaurant->name }}</a> 
                            </div>
                            <div class = "card-text col-4 col-lg-3 pe-2 text-end">
                                @if ($dish->promo == 0)
                                <h6><b>${{ $dish->price }}</b></h6>
                                @else
                                <p class = "m-0 lh-1">
                                    <b>${{ ceil($dish->price * (100 - $dish->promo)) / 100 }}</b>
                                    <s class = "text-danger" style = "font-size: 13px;">${{ $dish->price }}</s>
                                </p>
                                @endif
                            </div>
                        </div>
                        <div class = "row">
                            @if ($dish->desc)
                                <p class = "card-text text-secondary mb-1" style = "font-size: 15px;">{{ $dish->desc }}</p>
                            @else
                                <p class = "card-text text-secondary mb-1" style = "font-size: 15px;">No Description.</p>
                            @endif
                        </div>
                        <div class = "position-absolute bottom-0 end-0 mb-2 me-3 pe-1">
                            <a class = "card-btn position-relative" href = '{{ url("dislike/$dish->id") }}'><i class = "bi bi-heart-fill text-danger fs-4"></i></a>
                        </div>     
                    </div> 
                    <a class = "stretched-link" href = '{{ url("dish/$dish->id") }}'></a>       
                </div> 
            </div>
            @empty
                <p class = "text-secondary fs-5 ms-1 w-100">No dish added to the list yet.</p>
            @endforelse
        </div>

        <!-- Pagination Links -->
        <div class = "mt-3 pb-3">{{ $favourites->links('vendor.pagination.default') }}</div>
    </div>
@endsection