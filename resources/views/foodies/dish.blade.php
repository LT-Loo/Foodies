<!-- Dish Details Page -->

@extends("layouts.master")

@section("css")
    <link rel="stylesheet" type="text/css" href= "{{ asset('css/dish.css') }}">
@endsection

@section("content")

    <!-- Open Edit Dish Modal or Post Review Modal automatically if error received -->
    @if (count($errors) > 0 && !$errors->get('quantity') && !$errors->get('comment'))
        <script type = "text/javascript">
            $(document).ready(function() {
                $('#edit-dish').modal('show');
            });
        </script>
    @elseif (count($errors) > 0 && ($errors->get('r_images.*') || $errors->get('r_images') || !$errors->get('comment')))
        <script type = "text/javascript">
            $(document).ready(function() {
                $('#post-review').modal('show');
            });
        </script>
    @endif

    <!-- Display alert if attempt to add dish from different restaurant -->
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
    <div class = "container border-start border-end h-100 m-auto" style = "padding-top: 65px;">
        <!-- Restaurant -->
        <h1><a type = "button" class = "title-font text-decoration-none text-dark text-capitalize" href = '{{ url("restaurant/$dish->restaurant_id") }}'>{{ $dish->restaurant->name }}</a></h1>

        <!-- Dish details -->
        <div class = "row dish-details">
            <!-- Dish photos -->
            <div class = "dish-photos col-lg-4 px-0">
                <div id = "dish-details" class = "carousel slide carousel-fade carousel-dark w-100 h-100 border-top border-bottom" data-bs-interval = "false">
                    <div class = "carousel-indicators mb-0">
                        @for ($i = 0; $i < count($dish->images); $i++)
                        <button type = "button" data-bs-target = "#dish-details" data-bs-slide-to = "{{ $i }}" class = "@if ($i == 0) active @endif"></button>
                        @endfor
                    </div>
                    <div class = "carousel-inner">
                        @forelse ($dish->images as $image)
                        <div class = "carousel-item @if ($loop->index == 0) active @endif">
                            <img src = '{{ asset("$image->img") }}' class = "dish-img">
                        </div>
                        @empty
                        <div class = "carousel-item active">
                            <img src = '{{ asset("dish_img/default.png") }}' class = "dish-img mx-auto d-block">
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Dish info -->
            <div class = "col py-2 d-flex flex-column">
                <div class = "row pt-2">
                    <h3 class = "text-capitalize mb-0">{{ $dish->name }}
                        @if ($dish->promo > 0)
                        <span class = "badge bg-danger py-1 px-2" style = "vertical-align: 0.125em;">{{ $dish->promo }}% OFF</span>
                        @endif
                    </h3>

                    @if ($dish->desc)
                        <p>{{ $dish->desc }}</p>
                    @else
                        <p class = "text-secondary">No Description.</p>
                    @endif

                    @if ($dish->promo == 0)
                        <h4 class = "mb-0">${{ $dish->price }}</h4>
                    @else
                        <h4 class = "d-inline-block me-1 mb-0">${{ ceil($dish->price * (100 - $dish->promo)) / 100 }}
                        <s class = "text-danger d-inline-block fw-normal" style = "font-size: 20px;">${{ $dish->price }}</s></h4>
                    @endif
                </div>

                <div class = "row mt-3 flex-grow-1 position-relative">
                    <!-- Order and cart buttons for customer and guest -->
                    @if ($user->userType == 1 || $user->userType == -1)
                        <form method = "post" action = "{{ url('order-or-cart') }}" class = "position-absolute bottom-0">
                            @csrf
                            <input type = "hidden" name = "dish" value = "{{ $dish->id }}">
                            <div class = "row">
                                <label class = "col-2 col-lg-1 col-form-label">Quantity</label>  
                                <div class = "col-2 ms-2">
                                    @if ($errors->get('quantity'))
                                        <input type = "text" class = "form-control" style = "text-align: center;" name = "quantity" value = "{{ old('quantity') }}">
                                        <p class = "text-danger text-nowrap fw-light">**{{ $errors->first('quantity') }}</p>
                                    @else
                                        <input type = "text" class = "form-control" style = "text-align: center;" name = "quantity" value = "1">
                                    @endif        
                                </div> 
                                <div class = "btn-group border mt-3">
                                    <!-- Favourite button for customer -->
                                    @if ($user->userType == 1)
                                        @if ($fave) 
                                            <a href = '{{ url("dislike/$dish->id") }}' class = "btn btn-danger me-2 rounded p-1">Remove from Favourite</a>
                                        @else
                                            <a href = '{{ url("like/$dish->id") }}' class = "btn btn-outline-danger me-2 rounded p-1">Add to Favourite</a>
                                        @endif
                                    @endif
                                    <button type = "submit" class = "btn btn-primary pt-1 pb-2 rounded me-2" value = "cart" name = "action">Add to Cart</button>  
                                    <button type = "submit" class = "btn btn-primary pt-1 pb-2 rounded" value = "order" name = "action">Order Now</button>  
                                </div>
                            </div>  
                        </form>
                        @endif

                        <!-- Edit and delete button for restaurant owner -->
                        @if ($user->userType == 2 && $user->id == $dish->restaurant_id)
                        <form method = "post" action = '{{ route("restaurant.destroy", [$dish->id]) }}' class = "text-end position-absolute bottom-0">
                            @csrf
                            @method("delete")
                            <button type = "button" class = "btn btn-primary" data-bs-toggle = "modal" data-bs-target = "#edit-dish">Edit</button>
                            <input type = "submit" class = "btn btn-danger pt-1 pb-2" value = "Delete">
                        </form> 
                    @endif
                </div>
            </div>
        </div>

        <!-- Review Section -->
        <div class = "row mt-3">
            <div class = "col">
                <h3 class = "title-font text-dark">Review</h3>
            </div>

           @auth <!-- Only logged in user can post review -->
            <div class = "col text-end">
                <button type = "button" class = "btn btn-primary" data-bs-toggle = "modal" data-bs-target = "#post-review">Post Review</button>
            </div>
            @endauth       

            <hr class = "mt-0 mb-1">

            <!-- Review List -->
            <div class = "row row-cols-2 row-cols-lg-4">
                @forelse ($reviews as $review)
                    <div class = "col mb-3">
                        <div class = "card h-100">
                            <!-- Review Photos -->
                            <div id = "review-photos-{{ $review->id }}" class = "carousel slide carousel-dark" data-interval = "false">
                                <div class = "carousel-indicators mb-0">
                                    @foreach ($review->photos as $photo)
                                        <button type = "button" data-bs-target = "#review-photos-{{ $review->id }}" data-bs-slide-to = "{{ $loop->index }}" class = "@if ($loop->index == 0) active @endif"></button>
                                    @endforeach
                                </div>
                                <div class = "carousel-inner">
                                    @forelse ($review->photos as $photo)
                                    <div class = "carousel-item @if ($loop->index == 0) active @endif">
                                        <img src = '{{ asset("$photo->img") }}' class = "w-100 rounded-top" height = "150"> 
                                    </div>
                                    @empty
                                    <div class = "text-center w-100 text-secondary fs-4 mt-5 pt-2">No Photo</div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Uploader, datetime and comment -->
                            <div class = "card-body pt-2 pb-1 d-table">
                                <div class = "d-table-cell align-bottom">
                                    <h5 class = "my-0">{{ $review->uploader->name }}</h5>
                                    <p class = "text-secondary fw-light my-0" style = "font-size: 14px;">{{ $review->created_at->format("j M Y, g:ia") }}</p>
                                    @if ($review->comment)
                                    <p class = "mb-1">{{ $review->comment }}</p>
                                    @else
                                    <p class = "mb-1 text-secondary">No comment.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class = "text-secondary fs-5">No reviews.</p>
                @endforelse
            </div>            
        </div>
    </div>

    <!-- Edit Dish Modal -->
    <div class = "modal fade" id = "edit-dish" tabindex = "-1">
        <div class = "modal-dialog modal-lg">
            <div class = "modal-content">
                <div class = "modal-header py-2">
                    <h4 class = "modal-title text-capitalize">Edit {{ $dish->name }}</h4>
                    <button type = "button" class = "btn-close" data-bs-dismiss = "modal"></button>
                </div>

                <!-- Edit Dish Form -->
                <form method = "post" action = '{{ route("restaurant.update", [$dish->id]) }}' enctype = "multipart/form-data">
                    @csrf
                    @method("put")
                    <div class = "modal-body">
                        <!-- Price -->
                        <div class = "mb-3">
                            <label class = "form-label">Price</label>
                            @if ($errors->get("price"))
                                <input type = "text" class = "form-control" placeholder = "Price" name = "price" value = "{{ old('price') }}">
                                <p class = "text-danger fw-light">** {{ $errors->first("price") }}</p>
                            @else
                                <input type = "text" class = "form-control" placeholder = "Price" name = "price" value = "{{ $dish->price }}">
                            @endif
                        </div>

                        <!-- Promotion (Percentage Discount) -->
                        <div class = "mb-3">
                            <label class = "form-label">Promotion</label>
                            @if ($errors->get("promo"))
                                <input type = "text" class = "form-control" placeholder = "Promotion" name = "promo" value = "{{ old('promo') }}">
                                <p class = "text-danger fw-light">** {{ $errors->first("promo") }}</p>
                            @else
                                <input type = "text" class = "form-control" placeholder = "Promotion" name = "promo" value = "{{ $dish->promo }}">
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
                                    @foreach ($dish->restaurant->categories as $option)
                                        @if ($dish->category_id == $option->id)
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
                            @if ($errors->get("desc"))
                                <textarea type = "text" class = "form-control" placeholder = "Describe your dish..." name = "desc">{{ old('desc') }}</textarea>
                                <p class = "text-danger fw-light">** {{ $errors->first("desc") }}</p>
                            @else
                                <textarea type = "text" class = "form-control" placeholder = "Describe your dish..." name = "desc">{{ $dish->desc }}</textarea>
                            @endif
                        </div>

                        <!-- Dish Photos (Can choose to delete) -->
                        @if (count($dish->images) > 0)
                            <label class = "form-label mb-0">Current Photos<span class = "fw-light ms-1" style = "font-size: 13px;">(Check box to remove photos)</span></label>

                            <!-- Display error message if provided -->
                            @if ($errors->get("delete_images"))
                                <p class = "text-danger fw-light my-0">** {{ $errors->first("delete_images") }}</p>
                            @endif

                            <!-- Photos -->
                            <div class = "mb-3 row row-cols-5">
                                @foreach ($dish->images as $image)
                                <div class = "col">
                                    <div class = "image-checkbox">
                                        <input type = "checkbox" name = "delete_images[]" value = "{{ $image->id }}" id = "image{{ $image->id }}">
                                        <label for = "image{{ $image->id }}">
                                            <img src = '{{ asset("$image->img") }}' class = "w-100" height = "150px">
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Upload New Photos -->
                        <div class = "mb-3">
                            <label class = "form-label d-inline-block">Upload Photos</label>
                            <input type = "file" class = "form-control" name = "images[]" multiple>
                            @if ($errors->get("images"))
                                <p class = "text-danger fw-light">** {{ $errors->first("images") }}</p>
                            @endif
                            @if ($errors->get("images.*"))
                                <p class = "text-danger fw-light">** {{ $errors->first("images.*") }}</p>
                            @endif
                        </div>
                    </div>

                    <div class = "modal-footer justify-content-between">
                        <button type = "button" class = "btn btn-secondary" data-bs-dismiss = "modal">Back</button>
                        <input type = "submit" class = "btn btn-primary" value = "Save Change">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class = "modal fade" id = "post-review" tabindex = "-1">
        <div class = "modal-dialog">
            <div class = "modal-content">
                <div class = "modal-header py-2">
                    <h4 class = "modal-title">Post Review</h4>
                    <button type = "button" class = "btn-close" data-bs-dismiss = "modal"></button>
                </div>
                <!-- Review Form -->
                <form method = "post" action = '{{ url("post-review/$dish->id") }}' enctype = "multipart/form-data">
                    @csrf
                    <div class = "modal-body">
                        <!-- Comment -->
                        <div class = "mb-3">
                            <label class = "form-label">Comment</label>
                            @if ($errors->get('comment'))
                                <input type = "text" class = "form-control" placeholder = "Write your comment" name = "quantity" value = "{{ old('comment') }}">
                                <p class = "text-danger text-nowrap fw-light">**{{ $errors->first('comment') }}</p>
                            @else
                                <input type = "text" class = "form-control" placeholder = "Write your comment" name = "comment">
                            @endif   
                        </div>

                        <!-- Upload Photos -->
                        <div class = "mb-3">
                            <label class = "form-label">Photo</label>
                            @if ($errors->get('r_images'))
                                <input type = "file" class = "form-control" name = "r_images[]" value = "{{ old('r_images') }}" multiple>
                                <p class = "text-danger text-nowrap fw-light">**{{ $errors->first('r_images') }}</p>
                            @elseif ($errors->get('r_images.*'))
                                <input type = "file" class = "form-control" name = "r_images[]" value = "{{ old('r_images') }}" multiple>
                                <p class = "text-danger text-nowrap fw-light">**{{ $errors->first('r_images.*') }}</p>
                            @else
                                <input type = "file" class = "form-control" name = "r_images[]" multiple>
                            @endif 
                        </div>
                    </div>
                    <div class = "modal-footer">
                        <button type = "button" class = "btn btn-secondary" data-bs-dismiss = "modal">Cancel</button>
                        <input type = "submit" class = "btn btn-primary" value = "Post">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection