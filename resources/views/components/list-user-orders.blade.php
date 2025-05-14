<div class="col-span-12 xl:col-span-7">
    <!-- ====== Table One Start -->
    <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pt-4 pb-3 sm:px-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Người đặt hàng nhiều nhất
                </h3>
            </div>
        </div>

        <!-- Không dùng overflow-x-auto để tránh scroll x -->
        <div class="max-w-full overflow-hidden">
            <!-- Dùng w-full thay vì min-w-full -->
            <table class="w-full table-auto">
                <thead class="border-gray-100 border-y dark:border-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                Người dùng
                            </p>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                Số đơn hàng
                            </p>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                Tổng chi tiêu
                            </p>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($topUsers as $user)
                        <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer rounded-md">
                            <!-- Avatar + Tên -->
                            <td class="px-4 py-3 rounded-l-2xl">
                                <a href="{{ route('admin.users.show', $user->id) }}">
                                    <div class="flex items-center gap-3 max-w-[220px]">
                                        <div class="h-[50px] w-[50px] overflow-hidden rounded-md shrink-0">
                                            <img class="object-cover w-full h-full"
                                                src="{{ $user->avatar ?: 'https://res.cloudinary.com/dswj1rtvu/image/upload/v1745051027/BookStore/Authors/istockphoto-1451587807-612x612_f8h3fr.jpg' }}"
                                                alt="{{ $user->full_name }}">
                                        </div>
                                        <p class="truncate font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $user->full_name }}
                                        </p>
                                    </div>
                                </a>
                            </td>

                            <!-- Số đơn hàng -->
                            <td class="px-4 py-3">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ $user->valid_orders_count }}
                                </p>
                            </td>

                            <!-- Tổng chi tiêu -->
                            <td class="px-4 py-3 rounded-r-2xl">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ number_format($user->orders->sum('total_amount'), 0, ',', '.') }} đ
                                </p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- ====== Table One End -->
</div>
