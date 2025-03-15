<!-- Home Page -->

@extends("layouts.master")

@section("css")
    <link rel="stylesheet" type="text/css" href= "{{ asset('css/home.css') }}">
@endsection

@section("content")
    <div class = "container m-auto" style = "padding-top: 65px;">
        <!-- Recommendation List -->
        @if (count($recommend) > 0)
            <h1 class = "title-font">Recommendations</h1>
            <div class = "row mb-3">
                @foreach ($recommend as $dish)
                <div class = "col mb-2">
                    <div class = "recommend card h-100 border border-0">
                        <img src = '{{ asset($dish->pfp) }}' class = "card-img-top px-2 pt-2 pb-0" height = "130px">
                        <div class = "card-body py-2">
                            <h5 class = "card-title my-0 text-capitalize">{{ $dish->name }}</h5>
                            <p class = "mb-0 text-capitalize">{{ $dish->restaurant->name }}</p>
                        </div>
                        <a class = "stretched-link" href = '{{ url("dish/$dish->id")}}'></a>
                    </div>
                </div>
                @endforeach
            </div>
            <hr>
        @endif

        <!-- Restaurant List -->
        <h1 class = "title-font mb-0">Restaurants</h1>
        <div class = "row">
            @foreach ($restaurants as $restaurant)
                <div class = "col-12 col-lg-6 p-2">
                    <div class = "card h-100 border">
                        <div class = "row g-0 h-100">
                            <div class = "restaurant-pfp col-4">
                                <img src = '{{ asset("$restaurant->pfp") }}' class = "rounded-start w-100 d-inline-block" height = "160">
                            </div>
                            <div class = "col">
                                <div class = "card-body h-100 rounded-end">
                                    <h5 class = "card-title text-capitalize">{{ $restaurant->name }}</h5>
                                    @if ($restaurant->desc)
                                        <p class = "card-text">{{ $restaurant->desc }}</p>
                                    @else
                                        <p class = "card-text text-secondary">No description.</p>
                                    @endif
                                </div>        
                            </div>
                        </div>
                        <a href = '{{ url("restaurant/$restaurant->id") }}' class = "stretched-link"></a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination Links -->
        <div class = "mt-3 pb-3">{{ $restaurants->links('vendor.pagination.default') }}</div>
    </div>
@endsection