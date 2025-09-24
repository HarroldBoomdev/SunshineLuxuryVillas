@php($s = $submission ?? $submission ?? null)
@php($s = $s ?: $submission ?? null)
@php($s = $s ?: $submission ?? null)
{{-- Simpler: use the injected $submission --}}
<h2 style="margin:0 0 12px;">New Website Submission</h2>

<p><strong>Form:</strong> {{ $submission->form_key }} ({{ $submission->type }})</p>

<ul>
  <li><strong>Name:</strong> {{ $submission->name ?? '—' }}</li>
  <li><strong>Email:</strong> {{ $submission->email ?? '—' }}</li>
  <li><strong>Phone:</strong> {{ $submission->phone ?? '—' }}</li>
  <li><strong>Reference:</strong> {{ $submission->reference ?? '—' }}</li>
  <li><strong>Submitted at:</strong> {{ $submission->created_at->format('Y-m-d H:i') }}</li>
</ul>

@if(is_array($submission->payload) && count($submission->payload))
  <p><strong>Payload</strong></p>
  <pre style="background:#f6f6f6;padding:10px;border:1px solid #eee;border-radius:6px;">
{{ json_encode($submission->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}
  </pre>
@endif
