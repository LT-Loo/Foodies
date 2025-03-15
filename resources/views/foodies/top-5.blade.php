<!-- Top 5 Popular Dishes -->

@extends("layouts.master")

@section("css")
    <link rel="stylesheet" type="text/css" href= "{{ asset('css/restaurant.css') }}">
@endsection

@section("content")
    <div class = "container m-auto" style = "padding-top: 65px;">
        <h1 class = "title-font">Top 5 Popular Dishes</h1>
        <!-- Top 5 Popular List -->
        <div class = "row row-cols-2 row-cols-lg-3 justify-content-center">
            @foreach ($top5 as $dish)
            <div class = "col mb-3">
                <div class = "card position-relative h-100">
                    <!-- Promo Label -->
                    @if ($dish->promo > 0)
                        <h4 class = "promo position-absolute start-0 top-0 text-danger mt-2 ps-2">{{ $dish->promo }}% OFF</h4>
                    @endif

                    <!-- Dish Photo -->
                    <img src = '{{ asset("$dish->pfp") }}' class = "card-img-top mx-auto w-75 mt-2 rounded" height = "160"> 

                    <!-- Dish Details -->
                    <div class = "card-body position-relative d-flex flex-column">
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
                            <p class = "card-text text-secondary mb-1" style = "font-size: 15px;">{{ $dish->desc }}</p>
                        </div> 
                        
                        <!-- Number of Order -->
                        <div class = "row flex-grow-1 d-table">
                            <p class = "card-text5 mb-0 d-table-cell align-bottom"><b>{{ $dish->count }} Orders</b></p>
                        </div>

                        <!-- Favourite button for customer -->
                        @if ($user->userType == 1)
                            <div class = "position-absolute end-0 bottom-0 me-3 mb-3">
                                @if (in_array($dish->id, $favourites)) 
                                    <a href = '{{ url("dislike/$dish->id") }}' class = "card-btn"><i class = "bi bi-heart-fill text-danger fs-4"></i></a>
                                @else
                                    <a href = '{{ url("like/$dish->id") }}' class = "card-btn"><i class = "bi bi-heart text-danger fs-4"></i></a>
                                @endif
                            </div> 
                        @endif  
                    </div> 
                    <a class = "stretched-link" href = '{{ url("dish/$dish->id") }}'></a>       
                </div>  
            </div>
            @endforeach
        </div>
    </div>
@endsection