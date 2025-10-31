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
                        <h3 class="text-lg font-semibold text-gray-800">Tenant</h3>
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
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UUID</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant Name</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Download</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Connected</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($tenants as $tenant)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $tenant->uuid }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $tenant->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $tenant->ip ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $isOnline = str($tenant->status)->lower() === 'online';
                                                $badgeClasses = $isOnline
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800';
                                            @endphp
                                            <span class="inline-flex items-center rounded-full {{ $badgeClasses }} px-2.5 py-0.5 text-xs font-semibold">
                                                {{ ucfirst($tenant->status ?? 'unknown') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ optional($tenant->last_download)->format('d M Y H:i') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ optional($tenant->last_connected)->format('d M Y H:i') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 js-request-upload"
                                                    data-request-upload="{{ route('tenants.request-upload', $tenant) }}"
                                                >
                                                    Request Upload
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                            No record yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

        document.querySelectorAll('.js-request-upload').forEach((button) => {
            button.addEventListener('click', async () => {
                if (!csrfToken) {
                    console.error('CSRF token not found.');
                    return;
                }

                const url = button.dataset.requestUpload;
                button.disabled = true;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({}),
                    });

                    if (!response.ok) {
                        throw new Error(`Request failed with status ${response.status}`);
                    }

                    const data = await response.json();
                    console.info('Upload request dispatched', data);
                } catch (error) {
                    console.error('GA BERHASIL CEK DISINI!!!!', error);
                    alert('Failed to request upload');
                } finally {
                    button.disabled = false;
                }
            });
        });
    });
</script>
