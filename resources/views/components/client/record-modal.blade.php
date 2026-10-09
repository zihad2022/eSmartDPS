@props([
    'id',
    'title',
    'fields' => [],
    'actions' => [],
])

<div class="modal-overlay record-view-modal" id="{{ $id }}" role="dialog" aria-modal="true"
    aria-labelledby="{{ $id }}-title" onclick="if(event.target===this) closeModal('{{ $id }}')">
    <div class="modal-content record-view-card">
        <h3 class="record-view-title" id="{{ $id }}-title">{{ $title }}</h3>

        <div class="record-view-grid">
            @foreach ($fields as $field)
                <div class="record-view-item {{ !empty($field['full']) ? 'full' : '' }}">
                    <span>{{ $field['label'] ?? '' }}</span>
                    <strong>{{ $field['value'] ?? '—' }}</strong>
                </div>
            @endforeach
        </div>

        <div class="record-view-actions {{ count($actions) > 0 ? 'has-actions' : '' }}">
            <button type="button" class="record-view-btn record-view-btn-light"
                onclick="closeModal('{{ $id }}')">Close</button>
            @foreach ($actions as $action)
                <a href="{{ $action['url'] ?? '#' }}"
                    class="record-view-btn {{ $action['class'] ?? 'record-view-btn-primary' }}">
                    {{ $action['label'] ?? 'Open' }}
                </a>
            @endforeach
        </div>
    </div>
</div>
