<div>
    <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4 mx-auto pt-10 ">
        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded ">
            <div class="rounded-t mb-0 px-4 py-3 border-0">
                @if (session('success'))
                    <div class="bg-green-100 border-t border-b border-green-500 text-green-700 px-4 py-3" role="alert">
                        <p class="font-bold">Success</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                @elseif (session('error'))
                    <div class="bg-red-100 border-t border-b border-red-500 text-red-700 px-4 py-3 mb-5" role="alert">
                        <p class="font-bold">Error</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                @endif
                <div class="flex flex-wrap items-center">
                    <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                        <h2 class="font-semibold text-base text-blueGray-700">Areas</h2>
                    </div>
                    <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                        <button x-on:click="$wire.openModal()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"> New Area </button>
                    </div>
                </div>
            </div>

            <div class=" px-4">
                <input wire:model.live="search" type="text" placeholder="Search..." class="rounded-md border-0 py-1.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-center placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
            <div class="block w-full overflow-x-auto px-4 pb-10">
                <table class="bg-transparent min-w-full text-left text-sm font-light text-surface dark:text-white">
                    <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                        <tr>
                            <th scope="col" class="px-6 py-4">Name</th>
                            <th scope="col" class="px-6 py-4">Category</th>
                            <th scope="col" class="px-6 py-4">Start</th>
                            <th scope="col" class="px-6 py-4">End</th>
                            <th scope="col" class="px-6 py-4">Area Details (File)</th>
                            <th scope="col" class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse( $areas as $area)
                            <tr class="border-b border-neutral-200 transition duration-300 ease-in-out hover:bg-neutral-100 dark:border-white/10 dark:hover:bg-neutral-600">
                                <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $area->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $area->category }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ \Carbon\Carbon::parse($area->start)->format('d-m-Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ \Carbon\Carbon::parse($area->end)->format('d-m-Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 flex">
                                    <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                        {{ $area->filename }}
                                    </div>
                                    <div wire:click="downloadAreaFile({{ $area->id }})" class="cursor-pointer relative w-full px-4 max-w-full flex-grow flex-1">
                                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <button wire:click="openModal({{$area->id}})" class="bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm py-2 px-4 rounded inline-flex items-center">
                                        <span>Edit</span>
                                    </button>
                                </td>
                            </tr>

                        @empty
                           <tr>
                               <td colspan="6" class="text-center text-xl text-gray-700 p-6">
                                   No areas found...
                               </td>
                           </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- component -->
    <div class="flex items-center justify-center h-screen">
        <div x-data="{}">
            <!-- Background overlay -->
            <div x-show="$wire.showModal" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- Modal -->
            <div x-show="$wire.showModal" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="fixed z-10 inset-0 overflow-y-auto" >
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Modal panel -->
                    <div class="w-full inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <form wire:submit="save">
                            <div class="bg-gray-50 px-4 py-3 sm:px-6">
                                <h3>{{ $modalTitle }}</h3>
                            </div>
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <!-- Modal content -->
                                <div class="sm:flex sm:items-start">
                                    <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <div>
                                            <label for="area-name" class="block text-sm font-medium leading-6 text-gray-900">Area Name</label>
                                            <div class="mt-2">
                                                <input wire:model="areaName" type="text" id="area-name" name="area-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                @error('areaName') <em class="text-sm text-rose-500">{{ $message }}</em> @enderror
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label for="category" class="block text-sm font-medium leading-6 text-gray-900">Category</label>
                                            <div class="mt-2">
                                                <select wire:model="category" id="category" name="category" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                                    <option value="">- Please Select -</option>
                                                    <option value="Category 1">Category 1</option>
                                                    <option value="Category 2">Category 2</option>
                                                    <option value="Category 3">Category 3</option>
                                                    <option value="Category 4">Category 4</option>
                                                    <option value="Category 5">Category 5</option>
                                                </select>
                                                @error('category') <em class="text-sm text-rose-500">{{ $message }}</em> @enderror
                                            </div>
                                        </div>

                                        <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                            <div class="sm:col-span-3">
                                                <label for="start-date" class="block text-sm font-medium leading-6 text-gray-900">Start Date</label>
                                                <div class="mt-2">
                                                    <input wire:model="startDate" type="date" min="{{ \Carbon\Carbon::today()->format('Y-m-d')  }}" id="start-date" name="start-date" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                    @error('startDate') <em class="text-sm text-rose-500">{{ $message }}</em> @enderror
                                                </div>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="end-date" class="block text-sm font-medium leading-6 text-gray-900">End Date</label>
                                                <div class="mt-2">
                                                    <input wire:model="endDate" type="date" min="{{ \Carbon\Carbon::today()->format('Y-m-d')  }}" id="end-date" name="end-date" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                    @error('endDate') <em class="text-sm text-rose-500">{{ $message }}</em> @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <label for="file-name" class="block text-sm font-medium leading-6 text-gray-900">Filename</label>
                                            <div class="mt-2">
                                                <input wire:model="filename" type="text" id="file-name" name="file-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                @error('filename') <em class="text-sm text-rose-500">{{ $message }}</em> @enderror
                                            </div>
                                            <em class="text-sm">Name of the file to store area details.</em>
                                        </div>
                                        <div wire:ignore class="mt-4">
                                            <div  id="areas" style="width: 100%; height: 300px;"></div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <!-- Add/Save button -->
                                <button wire:click="save()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"> {{ $editMode ? 'Save' : 'Add' }} </button>
                                <!-- Cancel button -->
                                <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"> Cancel </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script type="module">
        const map = L.map('areas').setView([50.71586144923261, -1.9877819485567874], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const areaSelection = new DrawAreaSelection({
            onPolygonReady: (polygon) => {
                const data = JSON.stringify(polygon.toGeoJSON(3), undefined, 2);
                Livewire.dispatch('polygon-updated', { data });
            },
            onButtonDeactivate: (polygon) => {

                const data = `${polygon ? JSON.stringify(polygon.toGeoJSON(3), undefined, 2) : null}`;
                Livewire.dispatch('polygon-updated', { data });
            },
            position: 'topleft',
            active: true
        });

        map.addControl(areaSelection);
    </script>
</div>
