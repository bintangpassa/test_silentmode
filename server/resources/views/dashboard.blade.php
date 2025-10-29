<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-600 mb-6">
                        {{ __("You're logged in!") }}
                    </p>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Tenant Activity</h3>
                        <p class="text-sm text-gray-500">Latest download status for monitored tenants.</p>
                    </div>

                    <div class="mb-6 flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label for="status-filter" class="text-sm font-medium text-gray-700">Filter</label>
                            <select id="status-filter" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option>All Status</option>
                                <option>Online</option>
                                <option>Offline</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="sort-options" class="text-sm font-medium text-gray-700">Sort</label>
                            <select id="sort-options" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option>Last Download (Newest)</option>
                                <option>Last Download (Oldest)</option>
                                <option>Last Connected (Newest)</option>
                                <option>Last Connected (Oldest)</option>
                                <option>Tenant Name (A-Z)</option>
                                <option>Tenant Name (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant Name</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Download</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Connected</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">1</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">PT Sinar Jaya</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">192.168.10.21</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Online</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">12 Oct 2024 14:32</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">12 Oct 2024 14:35</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <x-secondary-button type="button">Detail</x-secondary-button>
                                            <x-danger-button type="button">Revoke</x-danger-button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">2</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">CV Mitra Digital</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">10.0.1.45</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">Offline</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">12 Oct 2024 09:18</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">11 Oct 2024 23:02</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <x-secondary-button type="button">Detail</x-secondary-button>
                                            <x-danger-button type="button">Revoke</x-danger-button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">3</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">PT Langit Biru</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">172.16.5.88</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Online</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">11 Oct 2024 22:05</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">11 Oct 2024 22:09</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <x-secondary-button type="button">Detail</x-secondary-button>
                                            <x-danger-button type="button">Revoke</x-danger-button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">4</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">PT Nusantara Data</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">203.123.45.12</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">Offline</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">11 Oct 2024 16:47</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">10 Oct 2024 20:14</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <x-secondary-button type="button">Detail</x-secondary-button>
                                            <x-danger-button type="button">Revoke</x-danger-button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">5</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">CV Solusi Mandiri</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">192.168.20.9</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Online</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">10 Oct 2024 08:11</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">10 Oct 2024 08:12</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <x-secondary-button type="button">Detail</x-secondary-button>
                                            <x-danger-button type="button">Revoke</x-danger-button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
