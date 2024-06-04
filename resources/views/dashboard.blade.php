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
                    <index-expenses></index-expenses>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-4">
                <reciept-index class="col-span-4 p-5 rounded-lg mt-10 shadow-xl overflow-clip bg-white"></reciept-index>
                <reciept-details
                    class="col-span-8 p-5 rounded-lg mt-10 shadow-xl overflow-clip bg-white"></reciept-details>
            </div>


        </div>
    </div>
</x-app-layout>
