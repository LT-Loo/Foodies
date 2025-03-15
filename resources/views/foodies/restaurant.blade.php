<!-- Restaurant Details Page -->

@extends("layouts.master")

@section("css")
    <link rel="stylesheet" type="text/css" href= "{{ asset('css/restaurant.css') }}">
@endsection

@section("content")

    <!-- Open New Dish Modal automatically if errors received -->
    @if (count($errors) > 0)
        <script type = "text/javascript">
            $(document).ready(function() {
                $('#new-dish').modal('show');
            });
        </script>
    @endif

    <!-- Page Content -->
    <div class = "container m-auto px-0" style = "padding-top: 65px;">
        <!-- Restaurant Details -->
        <div id = "restaurant-details" class = "row rounded w-100 mx-0">
            <div class = "col-4 col-lg-3">
                <img src = '{{ asset("$restaurant->pfp") }}' class = "img-fluid rounded my-2 d-block m-auto">    
            </div>
            <div class = "col py-1">
                <h1 class = "mb-0 text-capitalize">{{ $restaurant->name }}</h1>
                <h5 class = "text-capitalize">{{ $restaurant->restType }}</h5>
                @if ($restaurant->desc)
                    <p>{{ $restaurant->desc }}</p>
                @else
                    <p class = "text-secondary">No description.</p>
                @endif
            </div>     
        </div>

        <!-- Menu -->
        <div class = "row">
            <div class = "col">
                <h1 class = "ms-2 mt-3 title-font text-dark">Menu</h1>
            </div>

            <!-- Create New Dish Button -->
            @if ($user->userType == 2 && $user->id == $restaurant->id)
            <div class = "col text-end">
                <button type = "button" class = "btn btn-primary mt-4" data-bs-toggle = "modal" data-bs-target = "#new-dish">New Dish</button>
            </div>
            @endif

        </div>

        <!-- Alert for yet to be approved restaurant -->
        @if ($user->userType == 3 && $user->id == $restaurant->id)
            <p class = "alert alert-info" role = "alert">
                <b>Registered Restaurant Waiting to be Approved.</b><br>
                You will be able to update your menu after your registration is approved by the admin.
            </p>
        @endif

        <!-- Menu List -->
        <div class = "row vh-100">
            <!-- Category Tab -->
            <div class = "col-4 col-lg-2 h-100">
                <ul class = "nav nav-pills flex-column ms-2">
                    @forelse ($restaurant->categories as $category)
                        <li class = "nav-item h-100 pb-1">
                            @if ($category->order == 1)
                            <a class = "nav-link active me-4 text-capitalize" data-bs-toggle = "tab" href = "#{{ str_replace(' ', '', $category->name) }}">{{ $category->name }}</a>
                            @else
                            <a class = "nav-link me-4 text-capitalize" data-bs-toggle = "tab" href = "#{{ str_replace(' ', '', $category->name) }}">{{ $category->name }}</a>
                            @endif
                        </li>
                    @empty
                        <p class = "text-secondary fs-5 text-nowrap">No menu available.</p>
                    @endforelse
                </ul>
            </div>

            <!-- Dishes for each category -->
            <div class = "col ps-0">
                <div class = "tab-content">
                    @foreach ($restaurant->categories as $category)
                        @if ($category->order == 1)
                            <div class = "tab-pane active" id = "{{ str_replace(' ', '', $category->name) }}">
                        @else
                            <div class = "tab-pane fade" id = "{{ str_replace(' ', '', $category->name) }}">
                        @endif
                            <div class = "row row-cols-1 row-cols-lg-3" id = "dish-list">
                                @foreach ($category->dishes as $dish)
                                    <div class = "col mb-3 position-relative" id = "dish{{$dish->id}}">
                                        <div class = "card position-relative h-100">
                                            @if ($dish->promo > 0)
                                            <h4 class = "promo position-absolute top-0 start-0 text-danger ms-2 mt-2">{{ $dish->promo }}% OFF</h4>
                                            @endif
                                            <img src = '{{ asset("$dish->pfp") }}' class = "card-img-top mx-auto w-75 mt-2 mb-0 rounded" height = "160"> 
                                            <div class = "card-body border-top py-2">
                                                <div class = "row">
                                                    <div class = "col">
                                                        <h5 class = "title-card text-break text-capitalize mb-0">{{ $dish->name }}</h5>    
                                                    </div>
                                                    <div class = "card-text col-3 pe-2 text-end">
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
                                                <div class = "dish-desc row mt-1">
                                                    @if ($dish->desc)
                                                    <p class = "card-text text-secondary" style = "font-size: 15px;">{{ $dish->desc }}</p>
                                                    @else
                                                    <p class = "card-text text-secondary" style = "font-size: 15px;">No descirption.</p>
                                                    @endif
                                                </div>     
                                            </div>  
                                            <a class = "stretched-link" href = '{{ url("dish/$dish->id") }}'></a>  
                                        </div> 

                                        <!-- Favourite button for customer -->
                                        @if ($user->userType == 1)
                                            <div class = "position-absolute bottom-0 end-0 mb-2 me-4 pe-1">
                                                @if (in_array($dish->id, $favourites)) 
                                                    <a href = '{{ url("dislike/$dish->id") }}' class = "card-btn position-relative"><i class = "bi bi-heart-fill text-danger fs-4"></i></a>
                                                @else
                                                    <a href = '{{ url("like/$dish->id") }}' class = "card-btn position-relative"><i class = "bi bi-heart text-danger fs-4"></i></a>
                                                @endif
                                            </div>   
                                        @endif
                                    </div>
                                @endforeach
                            </div>   
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- New Dish Modal -->
    <div class = "modal fade" id = "new-dish" tabindex = "-1">
        <div class = "modal-dialog modal-lg">
            <div class = "modal-content">
                <div class = "modal-header py-2">
                    <h4 class = "modal-title">Create New Dish</h4>
                    <button type = "button" class = "btn-close" data-bs-dismiss = "modal"></button>
                </div>
                <!-- New Dish Form -->
                <form method = "post" action = '{{ route("restaurant.store") }}' enctype = "multipart/form-data">
                    @csrf
                    <div class = "modal-body">
                        <!-- Dish Name -->
                        <div class = "mb-3">
                            <label class = "form-label">Name</label>
                            <input type = "text" class = "form-control" placeholder = "Dish Name" name = "name" value = "{{ old('name') }}">
                            @if ($errors->get("name"))
                                <p class = "text-danger fw-light">** {{ $errors->first("name") }}</p>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class = "mb-3">
                            <label class = "form-label">Price</label>
                            <input type = "text" class = "form-control" placeholder = "Price" name = "price" value = "{{ old('price') }}">
                            @if ($errors->get("price"))
                                <p class = "text-danger fw-light">** {{ $errors->first("price") }}</p>
                            @endif
                        </div>

                        <!-- Promotion -->
                        <div class = "mb-3">
                            <label class = "form-label">Promotion</label>
                            <input type = "text" class = "form-control" placeholder = "Discount percentage" name = "promo" value = "{{ old('promo') }}">
                            @if ($errors->get("promo"))
                                <p class = "text-danger fw-light">** {{ $errors->first("promo") }}</p>
                            @endif
                        </div>

                        <!-- Dish Category Selection List -->
                        <div class = "row mb-3">
                            <label class = "col-2 form-label">Category</label>
                            <div class = "col">
                                <select class = "form-select w-50" name = "category">
                                    @if (old('category') == "")
                                    <option selected value = "">New Category</option>
                                    @else
                                    <option value = "">New Category</option>
                                    @endif
                                    @foreach ($restaurant->categories as $option)
                                        @if (old('category') == $option->id)
                                        <option selected value = "{{ $option->id }}">{{ $option->name }}</option>
                                        @else
                                        <option value = "{{ $option->id }}">{{ $option->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->get("category"))
                                <p class = "text-danger fw-light">** {{ $errors->first("category") }}</p>
                            @endif
                        </div>

                        <!-- New Category -->
                        <div class = "mb-3">
                            <label class = "form-label">New Category<span class = "fw-light ms-1" style = "font-size: 13px;">(Only required if no existing category is selected)</span></label>
                            <input type = "text" class = "form-control" placeholder = "Category Name" name = "new_category" value = "{{ old('new_category') }}">
                            @if ($errors->get("new_category"))
                                <p class = "text-danger fw-light">** {{ $errors->first("new_category") }}</p>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class = "mb-3">
                            <label class = "form-label">Description</label>
                            <textarea type = "text" class = "form-control" placeholder = "Describe your dish..." name = "desc">{{ old('desc') }}</textarea>
                            @if ($errors->get("desc"))
                                <p class = "text-danger fw-light">** {{ $errors->first("desc") }}</p>
                            @endif
                        </div>

                        <!-- Photos -->
                        <div class = "mb-3">
                            <label class = "form-label d-inline-block">Photos</label>
                            <p class = "fw-light d-inline-block mb-0" style = "font-size: 14px;">(Note: First photo is automatically selected as thumbnail.)</p>
                            <input type = "file" class = "form-control" name = "images[]" multiple>
                            @if ($errors->get("images"))
                                <p class = "text-danger fw-light">** {{ $errors->first("images") }}</p>
                            @elseif ($errors->get("images.*"))
                                <p class = "text-danger fw-light">** {{ $errors->first("images.*") }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class = "modal-footer justify-content-between">
                        <button type = "button" class = "btn btn-secondary" data-bs-dismiss = "modal">Back</button>
                        <input type = "submit" class = "btn btn-primary" value = "Add">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection