@props([
    'title' => null,
    'action' => null,
    'method' => 'POST',
    'multipart' => false,
])

<div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
    
    {{-- Title --}}
    @if ($title)
        <h2 class="text-xl font-semibold text-primary-900 mb-6">
            {{ $title }}
        </h2>
    @endif

    {{-- Form Wrapper --}}
    <form 
        method="{{ $method !== 'GET' ? 'POST' : 'GET' }}"
        action="{{ $action }}"
        @if($multipart) enctype="multipart/form-data" @endif
        class="space-y-10"
    >
        @csrf

        {{-- PUT/PATCH/DELETE --}}
        @if (!in_array($method, ['GET', 'POST']))
            @method($method)
        @endif

        {{ $slot }}

        {{-- Footer Buttons --}}
        @if(isset($footer))
            <div class="flex justify-end space-x-4">
                {{ $footer }}
            </div>
        @endif

    </form>
</div>
