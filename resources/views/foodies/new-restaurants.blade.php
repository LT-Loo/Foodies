<!-- New Restaurants List Page -->

@extends("layouts.master")

@section("content")
    <div class = "container m-auto" style = "padding-top: 65px;">
        <h1 class = "title-font">New Restaurants</h1>

        <!-- New Restaurants List -->
        <div class = "accordion" id = "new-restaurants">
            @foreach ($restaurants as $restaurant)
                <div class = "accordion-item">
                    <!-- Restaurant Name -->
                    <h2 class = "accordion-header">
                        <button class = "accordion-button collapsed text-capitalize" type = "button" data-bs-toggle = "collapse" data-bs-target = "#restaurant{{ $restaurant->id }}">
                            {{ $restaurant->name }}
                        </button>
                    </h2>

                    <!-- Restaurant Details -->
                    <div id = "restaurant{{ $restaurant->id }}" class = "accordion-collapse collapse" data-bs-parent = "#new-restaurants">
                        <div class = "accordion-body">
                            <div class = "row">
                                <div class = "col-4 col-lg-2">
                                    <img src = "{{ asset('restaurant_pfp/default.png') }}" class = "img-thumbnail">
                                </div>
                                <div class = "col">
                                    <table class = "table table-borderless">
                                        <tr>
                                            <th width = "10%">Address</th><td>{{ $restaurant->address }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type</th>
                                            <td>
                                                @if ($restaurant->restType) 
                                                    {{ $restaurant->restType }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>
                                                @if ($restaurant->desc) 
                                                    {{ $restaurant->desc }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Approve Button -->
                                    <a type = "button" class = "btn btn-success" href = '{{ url("approve/$restaurant->id") }}'>Approve</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection