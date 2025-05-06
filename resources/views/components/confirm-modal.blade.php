<div id="{{ $id }}"
    class="min-w-screen h-screen fixed left-0 top-0 flex justify-center items-center z-50 bg-no-repeat bg-center bg-cover hidden">

    <div class="absolute bg-black opacity-80 inset-0 z-0" onclick="hideModal('{{ $id }}')"></div>

    <div class="w-full max-w-lg p-5 relative mx-auto my-auto rounded-xl shadow-lg bg-white z-10">
        <!-- Body -->
        <div class="text-center p-5 flex-auto justify-center">
            <!-- Icon và Màu sắc thay đổi tùy vào action -->
            @if ($action == 'delete')
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-500 mx-auto mb-2" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <h2 class="text-xl font-bold py-4">{{ $title }}</h2>
                <p class="text-sm text-gray-500 px-8">{{ $message }}</p>
            @elseif($action == 'edit')
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-16 h-16 text-yellow-500 mx-auto mb-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 3.487a2.25 2.25 0 113.182 3.182L6.75 19.963 3 21l1.037-3.75 12.825-13.763z" />
                </svg>
                <h2 class="text-xl font-bold py-4">{{ $title }}</h2>
                <p class="text-sm text-gray-500 px-8">{{ $message }}</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="p-3 mt-2 text-center space-x-4 md:block">
            <button
                class="mb-2 md:mb-0 bg-white cursor-pointer px-5 py-2 text-sm shadow-sm font-medium tracking-wider border text-gray-600 rounded-full hover:shadow-lg hover:bg-gray-100"
                onclick="hideModal('{{ $id }}'); return false;">
                {{ $cancelText }}
            </button>

            <!-- Thay đổi màu sắc tùy vào action -->
            <button onclick="event.preventDefault(); document.getElementById('{{ $formId }}').submit();"
                class="mb-2 md:mb-0 cursor-pointer
    @if ($action == 'delete') bg-red-500 border border-red-500 hover:bg-red-600
    @elseif($action == 'edit') bg-yellow-500 border border-yellow-500 hover:bg-yellow-600 @endif
    px-5 py-2 text-sm shadow-sm font-medium tracking-wider text-white rounded-full hover:shadow-lg">
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>

<script>
    function hideModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            const modal = document.getElementById('{{ $id }}');
            if (modal && !modal.classList.contains('hidden')) {
                hideModal('{{ $id }}');
            }
        }
    });
</script>
