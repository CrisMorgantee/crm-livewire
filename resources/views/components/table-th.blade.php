@props([
    'name',
    'header'
])

<div wire:click="sortBy( '{{$name}}', '{{$header['sortDirection'] === 'asc' ? 'desc' : 'asc'}}')"
     class="cursor-pointer">
    {{$header['label']}}
    @if($header['sortColumnBy'] === $name)
        <x-icon :name="$header['sortDirection'] === 'asc' ? 'o-chevron-up' : 'o-chevron-down'"
                class="ml-2 w-4"/>
    @endif
</div>
