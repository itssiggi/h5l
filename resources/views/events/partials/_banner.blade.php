<div>
    <a href="{{ route('events.show', $event->id) }}">
        <div class="w-4/5 mx-auto flag flex bg-primary hover:bg-blue-900 px-10 py-10 rounded-lg text-gray-100 shadow-lg mb-5 justify-between">
            <div class="w-full lg:w-4/5 flex">
                <div class="flex flex-col pr-10 border-r-4 border-blue-400 w-1/4 text-center">
                    <div class="text-xl lg:text-4xl">
                        {{ date('d. M', strtotime($event->planned_start)) }}
                    </div>
                    <div class="text-sm tracking-wider text-center uppercase text-gray-600">    {{ date('H:i', strtotime($event->planned_start)) }} Uhr
                    </div>
                </div>
                <div class="pl-10 flex flex-col content-start">
                    <div class="text-sm mt-2 uppercase text-gray-600 content-start">

                        @if ($event->regular_event)
                        (Gewertetes Rennen) 
                        @else
                        (Ungewertetes Rennen)
                        @endif
                    </div>
                    <div class="text-3xl self-start">{{ $event->name }}</div>

                </div>
            </div>
            <div class="hidden lg:flex mr-10">
                <img class="w-20 self-center opacity-50" src="#" alt="">
            </div>

        </div>
    </a>
</div>