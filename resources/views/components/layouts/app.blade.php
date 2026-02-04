<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>

<body class="min-h-screen font-sans antialiased bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            <x-app-brand class="px-5 pt-4" />

            <x-menu-separator />

            {{-- MENU --}}
            <x-menu activate-by-route>
                <x-menu-item title="Menu Utama" icon="o-user" link="/" />
                <x-menu-item title="Korong" icon="o-map-pin" :link="route('korong.index')" />
                <x-menu-item title="Produk" icon="o-shopping-bag" :link="route('products.index')" />
                <x-menu-item title="UMKM" icon="o-building-storefront" :link="route('business.index')" />
                {{-- User --}}
                @if ($user = auth()->user())

                    <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />
                    @if ($user->hasVerifiedEmail())
                        {{-- Dashboard (requires verified email) --}}

                        {{-- Admin only menu items (requires verified email) --}}
                        @role('admin')
                            <x-menu-sub title="Administration" icon="o-cog">
                                <x-menu-item title="Pengguna" icon="o-users" link="/admin/users" />
                                <x-menu-item title="Peran" icon="o-user-group" link="/admin/roles" />
                                <x-menu-item title="Izin" icon="o-key" link="/admin/permissions" />
                            </x-menu-sub>
                        @endrole
                    @else
                        {{-- Verification reminder --}}
                        <div class="p-4 mt-2 text-sm bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                            <p>Please verify your email to access all features.</p>
                            <a href="{{ route('verification.notice') }}" class="text-blue-600 hover:underline">Verify
                                Now</a>
                        </div>
                    @endif
                    {{-- Profile page (always accessible) --}}
                    <x-menu-item title="Profile" icon="o-user" link="/profile" />
                    <x-menu-item title="Log out" icon="o-arrow-right-start-on-rectangle" :link="route('logout')" />
                @else
                    <div class="-mx-2 !-my-2 rounded p-2">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Tamu
                            </div>
                            <div class="flex items-center gap-2">
                                <x-theme-toggle class="btn btn-circle btn-ghost btn-sm" />
                                <x-button icon="o-arrow-right-start-on-rectangle" class="btn-circle btn-ghost btn-xs"
                                    tooltip-left="Log-in" no-wire-navigate link="/login" />
                            </div>
                        </div>
                    </div>
                @endif

                <x-menu-separator />

                {{-- Check if user exists --}}
                @if ($user = auth()->user())
                    {{-- User is logged in - show full profile with theme toggle and logout --}}
                    <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover
                        class="-mx-2 !-my-2 rounded">
                        <x-slot:actions>
                            <div class="flex items-center gap-2">
                                <x-theme-toggle class="btn btn-circle btn-ghost btn-sm" />
                                <x-button icon="o-arrow-right-start-on-rectangle" class="btn-circle btn-ghost btn-xs"
                                    tooltip-left="Log-out" no-wire-navigate link="/logout" />
                            </div>
                        </x-slot:actions>
                    </x-list-item>
                @else
                    {{-- User is NOT logged in - only show theme toggle and login --}}
                @endif



            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
    <footer class="pt-12 border-t border-gray-800 text-center text-gray-500 text-sm">
        <p>© {{ date('Y') }} Nagari Toboh Gadang Timur. Semua data bersifat informatif.</p>
        <p class="mt-2">Sumber data: Pemerintahan Nagari Toboh Gadang Timur</p>
    </footer>
</body>

</html>
