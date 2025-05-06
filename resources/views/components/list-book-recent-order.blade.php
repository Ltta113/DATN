<div class="col-span-12 xl:col-span-7">
    <!-- ====== Table One Start -->
    <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pt-4 pb-3 sm:px-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Sách được đặt hàng nhiều nhất
                </h3>
            </div>
        </div>

        <div class="max-w-full overflow-hidden">
            <table class="min-w-full">
                <!-- table header start -->
                <thead class="border-gray-100 border-y dark:border-gray-800">
                    <tr>
                        <th class="px-6 py-3 whitespace-nowrap first:pl-0">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Sách
                                </p>
                            </div>
                        </th>

                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Số lượng bán
                                </p>
                            </div>
                        </th>
                    </tr>
                </thead>
                <!-- table header end -->

                <!-- table body start -->
                <tbody class="py-3 divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($books as $book)
                        <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer rounded-md">
                            <td class="px-6 py-3 whitespace-nowrap first:pl-0 rounded-l-2xl">
                                <a href="{{ route('admin.books.show', $book->id) }}" class="flex items-center pl-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                                            <img src="{{ $book->cover_image }}" alt="Product">
                                        </div>
                                        <div class="w-48 overflow-hidden">
                                            <p
                                                class="font-medium text-gray-800 text-theme-sm dark:text-white/90 text-ellipsis whitespace-nowrap">
                                                {{ $book->title }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap rounded-r-2xl">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ $book->sold }}
                                </p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <!-- table body end -->
            </table>
        </div>
    </div>
    <!-- ====== Table One End -->
</div>
