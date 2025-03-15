<!-- Navigation Bar -->

<nav class="navbar fixed-top navbar-expand-sm border-bottom bg-white p-0">
  <div class="container-fluid mx-4">

    <a class="title-font navbar-brand" href="{{ url('/') }}"><h2 class = "m-auto text-primary"><b>FOODIES</b></h2></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <!-- My Restaurant (Only for restaurant owner) -->
        @auth
          @if (Auth::user()->userType > 1)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('restaurant.index') }}">My Restaurant</a>
            </li>
          @endif
        @endauth

        <!-- Top 5 Popular Dishes (For any user) -->
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/top-5') }}">Top 5</a>
        </li>

        <!-- New Restaurant (Only for Admin) -->
        @auth
          @if (Auth::user()->userType == 0)
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/new-restaurants') }}">New Restaurants</a>
          </li>
          @endif
        @endauth

        <!-- Documentation Page -->
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/doc') }}">Doc</a>
        </li>
      </ul>
    </div>

    <div class = "navbar text-dark">
      <ul class = "navbar-nav">
        <!-- Only for logged in user -->
        @auth
          <!-- Cart, Favourite and Order History Buttons for customer -->
          @if (Auth::user()->userType == 1)
            <li><button type = "button" class = "btn me-1 rounded-circle" data-bs-toggle = "offcanvas" data-bs-target = "#cart"><i class="bi bi-cart2 text-dark"></i></button></li>
            <li><a type = "button" class = "btn me-1 rounded-circle" href = "{{ url('favourite') }}"><i class="bi bi-heart text-dark"></i></a></li>
            <li><a type = "button" class = "btn me-1 rounded-circle" href = "{{ url('order-history') }}"><i class="bi bi-list-task text-dark"></i></a></li>
          @endif

          <!-- Order List for restaurant -->
          @if (Auth::user()->userType > 1)
            <li><a type = "button" class = "btn me-1 rounded-circle" href = "{{ url('orders') }}"><i class="bi bi-list-task text-dark"></i></a></li>
          @endif

          <!-- Display username and user type for logged in user -->
          <li>
            <div class="dropdown">
              <button class="btn border border-0 text-dark pt-2 dropdown-toggle text-capitalize" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ Auth::user()->name }}
                @if (Auth::user()->userType == 0) (Admin)
                @elseif (Auth::user()->userType == 1) (Customer)
                @elseif (Auth::user()->userType > 1) (Restaurant)
                @endif
              </button>

              <!-- Logout Button -->
              <ul class="dropdown-menu dropdown-menu-end py-1 text-secondary">
                <li>
                  <form method = "POST" action = "{{ url('/logout') }}" class = "dropdown-item p-0">
                    {{ csrf_field() }}
                    <input type = "submit" class = "btn border border-0 fw-bold ps-3 py-1" value = "Logout">
                  </form>
                </li>
              </ul>
            </div>
          </li>

        <!-- Login and Register buttons for guest -->
        @else  
          <a class = "btn rounded-pill pt-1 me-1" href = "{{ route('login') }}">Login</a>
          <a class = "btn btn-secondary rounded-pill pt-1" href = "{{ url('register/customer') }}">Register</a>
        @endauth
      </ul>
    </div>
  </div>
</nav>

@include("layouts.cart")