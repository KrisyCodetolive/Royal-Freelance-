{{-- Columns Block - Multi-column layout --}}
@php
    $columnsLayout = $block->columns_layout ?? $content['columns_layout'] ?? '1/2,1/2';
    $columns = explode(',', $columnsLayout);
    $gap = $styles['gap'] ?? '24px';

    // Calculate grid template columns
    $gridCols = count($columns);
@endphp

<div class="w-full grid gap-6" style="grid-template-columns: repeat({{ $gridCols }}, minmax(0, 1fr)); gap: {{ $gap }};">
    @foreach($columns as $index => $width)
        <div class="column-{{ $index + 1 }}">
            {{-- Column content will be rendered from children blocks with matching column_index --}}
            <div class="min-h-[100px] border border-dashed border-gray-600 rounded-lg p-4 text-center text-gray-400">
                Colonne {{ $index + 1 }}
            </div>
        </div>
    @endforeach
</div>