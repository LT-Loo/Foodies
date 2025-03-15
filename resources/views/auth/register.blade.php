<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <div class = "text-center">
                <h1 class = "app-name"><i class = "bi bi-house-fill pe-2"></i>Register</h1>
            </div>
        </x-slot>

        <!-- Register Type -->
        <form method="POST" action="{{ url('registerType') }}" class = "text-center">
            @csrf
            @if ($registerType == "customer")
                <input type = "submit" class = "btn btn-primary text-capitalize opacity-100 me-5" value = "customer" name = "registerType" disabled>
                <input type = "submit" class = "btn btn-outline-primary text-capitalize opacity-100" value = "restaurant" name = "registerType">
            @else
                <input type = "submit" class = "btn btn-outline-primary text-capitalize opacity-100 me-5" value = "customer" name = "registerType">
                <input type = "submit" class = "btn btn-primary text-capitalize opacity-100" value = "restaurant" name = "registerType" disabled>
            @endif
        </form>

        <!-- Register Form -->
        <form method="POST" action='{{ url("register/$registerType") }}' enctype = "multipart/form-data">
            @csrf

            <input type = "hidden" name = "registerType" value = "$registerType">

            <div class = "row">
                <div class = "col-md">
                    <!-- Name -->
                    <div class="mt-3">
                        <x-input-label for="name" :value="__('Username')" />

                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autofocus />

                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-3">
                        <x-input-label for="email" :value="__('Email')" />

                        <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')"/>

                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    
                    <!-- Password -->
                    <div class="mt-3">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-3">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                        type="password"
                                        name="password_confirmation"/>

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div> 
   
                </div>
                <div class = "col-md">
                    <!-- Address -->
                    <div class="mt-3">
                        <x-input-label for="address" :value="__('Address')" />

                        <textarea id="address" class="form-control block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="address" :value="old('address')" rows="2">{{ old('address') }}</textarea>

                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div> 
                    
                    @if ($registerType == "restaurant")
                        <!-- Restaurant Type -->
                        <div class="mt-3">
                            <x-input-label for="restType" :value="__('Restaurant Type (Optional)')" />

                            <x-text-input id="restType" class="block mt-1 w-full" type="text" name="restType" :value="old('restType')"/>

                            <x-input-error :messages="$errors->get('restType')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-3">
                            <x-input-label for="desc" :value="__('Description (Optional)')" />

                            <textarea id="desc" class="form-control block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="desc" :value="old('desc')" rows="2">{{ old('desc') }}</textarea>

                            <x-input-error :messages="$errors->get('desc')" class="mt-2" />
                        </div>
                        
                        <!-- Profile Picture -->
                        <div class = "mt-3">
                            <x-input-label for="pfp" :value="__('Profile Picture')" />

                            <input id = "pfp" type = "file" name = "pfp" :value="old('pfp')" class="form-control border rounded border-grey-600 py-1 shadow-sm">

                            <x-input-error :messages="$errors->get('pfp')" class="mt-2" />
                        </div>
                    @endif
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ml-4 bg-primary">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
