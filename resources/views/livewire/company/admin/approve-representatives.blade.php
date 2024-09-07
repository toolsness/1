<div>
    <div class="container mx-auto mt-8">
        <h2 class="text-2xl font-bold mb-4 text-center">Approve Company Representatives</h2>
        @if($pendingRepresentatives->isEmpty())
            <p class="text-gray-600 text-center">No pending representatives to approve.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b text-center">Name</th>
                            <th class="py-2 px-4 border-b text-center">Email</th>
                            <th class="py-2 px-4 border-b text-center">Phone</th>
                            <th class="py-2 px-4 border-b text-center">Application Date</th>
                            <th class="py-2 px-4 border-b text-center">Application Time</th>
                            <th class="py-2 px-4 border-b text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRepresentatives as $representative)
                            <tr>
                                <td class="py-2 px-4 border-b text-center">{{ $representative->name }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $representative->email }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $representative->companyRepresentative->contact_phone_number }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ ($representative->companyRepresentative->created_at)->format('d/m/Y') }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ ($representative->companyRepresentative->created_at)->format('H:i a') }}</td>
                                {{-- <td class="py-2 px-4 border-b text-center">
                                    {{ $representative->companyRepresentative->company->name ?? 'No Company Assigned' }}
                                </td> --}}
                                <td class="py-2 px-4 border-b text-center">
                                    <button wire:click="approve({{ $representative->id }})"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded mr-2">
                                        Approve
                                    </button>
                                    <button wire:click="reject({{ $representative->id }})"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                        Reject
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
