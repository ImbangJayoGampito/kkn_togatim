    <a href="/" wire:navigate>
        <!-- Hidden when collapsed -->
        <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
            <div class="flex items-center gap-2">
                <x-icon name="o-cube" class="w-6 -mb-1.5 text-purple-500" />
                <span class="font-bold text-3xl me-3   ">
                    TOGATI
                </span>
            </div>
        </div>

        <!-- Display when collapsed -->
        <div class="display-when-collapsed hidden mx-5 mt-5 mb-1 h-[28px]">
            <x-icon name="s-cube" class="w-6 -mb-1.5 text-purple-500" />
        </div>
    </a>