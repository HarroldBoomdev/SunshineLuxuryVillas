@extends('layouts.app')

@section('title', 'Inbox — View')

@php
  $fromName   = $submission->name ?? '—';
  $fromEmail  = $submission->email ?? '';
  $phone      = $submission->phone ?? '—';
  $ref        = $submission->reference ?? ($submission->payload['property_reference'] ?? null);
  $propTitle  = $submission->payload['property_title'] ?? null;
  $propUrl    = $submission->payload['url'] ?? null;
  $created    = optional($submission->created_at)->format('Y-m-d H:i');
  $formKey    = $submission->form_key;

  $baseSubject = trim(($ref ? "[{$ref}] " : '') . ($propTitle ?: ucfirst(str_replace('_',' ', $formKey))));
@endphp

@section('content')
<div class="mx-auto max-w-5xl px-4 py-6">
    <a href="{{ route('inbox.index', ['type' => $submission->form_key]) }}"
        onclick="if (document.referrer) { event.preventDefault(); history.back(); }"
        class="text-sm text-amber-700 hover:underline">
        &larr; Back to Inbox
    </a>

  <div class="mt-3 bg-white rounded-xl border border-gray-200 overflow-hidden">
    {{-- HEADER (Subject + actions) --}}
    <div class="px-5 py-4 border-b">
      <div class="flex items-start justify-between gap-3">
        <div>
          <h1 class="text-2xl font-semibold leading-tight">
            {{ $baseSubject ?: 'Website submission' }}
          </h1>
          <div class="mt-1 text-sm text-gray-500">
            {{ $created }}
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button type="button"
                  class="inline-flex items-center gap-2 rounded-md border px-3 py-1.5 text-sm hover:bg-gray-50"
                  onclick="SLV.openCompose('reply')"
                  title="Reply">
            {{-- icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M7.707 3.293a1 1 0 00-1.414 1.414L8.586 7H7a7 7 0 00-7 7 1 1 0 102 0 5 5 0 015-5h1.586l-2.293 2.293a1 1 0 001.414 1.414l4-4a1 1 0 000-1.414l-4-4z" /></svg>
            Reply
          </button>

          <button type="button"
                  class="inline-flex items-center gap-2 rounded-md border px-3 py-1.5 text-sm hover:bg-gray-50"
                  onclick="SLV.openCompose('reply_all')"
                  title="Reply all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10 5.586L6.707 8.879a1 1 0 11-1.414-1.414l4-4a.997.997 0 011.414 0l4 4A1 1 0 0113.293 8.88L10 5.586z"/><path d="M5 13a6 6 0 016-6h2V5l4 4-4 4v-2h-2a4 4 0 00-4 4v2H5v-2z"/></svg>
            Reply all
          </button>

          <button type="button"
                  class="inline-flex items-center gap-2 rounded-md border px-3 py-1.5 text-sm hover:bg-gray-50"
                  onclick="SLV.openCompose('forward')"
                  title="Forward">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M12.293 2.293a1 1 0 011.414 0l4 4a.999.999 0 010 1.414l-4 4a1 1 0 01-1.707-.707V9H9a5 5 0 00-5 5 1 1 0 11-2 0 7 7 0 017-7h3V3a1 1 0 011.293-.707z"/></svg>
            Forward
          </button>

          <button onclick="window.print()"
                  class="inline-flex items-center gap-2 rounded-md border px-3 py-1.5 text-sm hover:bg-gray-50"
                  title="Print">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6 2a2 2 0 00-2 2v2h12V4a2 2 0 00-2-2H6z"/><path fill-rule="evenodd" d="M4 8a2 2 0 00-2 2v4h4v-2h8v2h4v-4a2 2 0 00-2-2H4zm3 8h6v2H7v-2z" clip-rule="evenodd"/></svg>
            Print
          </button>

          <form action="{{ route('inbox.destroy', $submission->id) }}"
                method="POST"
                onsubmit="return confirm('Delete this submission?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-md border border-red-200 text-red-600 px-3 py-1.5 text-sm hover:bg-red-50"
                    title="Delete">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-1 1v1H5a1 1 0 000 2h.293l.853 10.236A2 2 0 008.14 19h3.72a2 2 0 001.994-2.764L14.707 6H15a1 1 0 100-2h-3V3a1 1 0 00-1-1H9zM8 8a1 1 0 012 0v7a1 1 0 01-2 0V8zm4 0a1 1 0 012 0v7a1 1 0 01-2 0V8z" clip-rule="evenodd"/></svg>
              Delete
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- ENVELOPE HEADER --}}
    <div class="px-5 py-4 border-b">
      <div class="flex items-start gap-3">
        <div class="h-10 w-10 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-semibold">
          {{ strtoupper(substr($fromName,0,1)) }}
        </div>
        <div class="flex-1">
          <div class="text-sm">
            <span class="font-medium">{{ $fromName }}</span>
            @if($fromEmail)
              &lt;<a href="mailto:{{ $fromEmail }}" class="text-amber-700 hover:underline">{{ $fromEmail }}</a>&gt;
            @endif
          </div>
          <div class="mt-1 text-xs text-gray-500">
            Form: <span class="font-mono">{{ $formKey }}</span>
            @if($ref) • Reference: <span class="font-mono">{{ $ref }}</span>@endif
            @if($phone && $phone !== '—') • Phone: {{ $phone }} @endif
          </div>
          @if($propTitle)
            <div class="mt-1 text-xs">
              Property: <span class="font-medium">{{ $propTitle }}</span>
              @if($propUrl)
                — <a href="{{ $propUrl }}" target="_blank" class="text-amber-700 hover:underline">Open on site</a>
              @endif
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- BODY --}}
    <div class="px-5 py-6 space-y-6">
      <section>
        <h2 class="text-sm font-semibold text-gray-600 mb-2">Message</h2>
        <div class="p-4 rounded-lg border bg-gray-50 text-sm whitespace-pre-wrap">
          {{ $submission->payload['message'] ?? '—' }}
        </div>
      </section>

      <section>
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold text-gray-600">Payload (JSON)</h2>
          <button type="button" onclick="SLV.togglePayload()"
                  class="text-xs text-amber-700 hover:underline">Show/Hide</button>
        </div>
        <pre id="payloadBox"
             class="mt-2 p-4 rounded-lg border bg-gray-50 text-xs overflow-x-auto hidden">
@json($submission->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
        </pre>
      </section>
    </div>
  </div>
</div>

{{-- =========================
     COMPOSE MODAL (Reply / Reply-all / Forward)
========================= --}}
<div id="composeModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[85vh] overflow-hidden">
    <div class="flex items-center justify-between border-b px-4 py-3">
      <h3 class="text-lg font-semibold" id="composeTitle">Compose</h3>
      <button type="button" class="text-gray-500 hover:text-gray-700" onclick="SLV.closeCompose()">&times;</button>
    </div>

    <form id="composeForm" action="{{ route('inbox.send', $submission->id) }}" method="POST">
      @csrf
      <input type="hidden" name="action" id="composeAction" value="reply">

      <div class="px-4 py-3 space-y-3 overflow-y-auto" style="max-height: calc(85vh - 108px);">
        <div class="grid grid-cols-1 md:grid-cols-8 gap-3">
          <label class="md:col-span-1 text-sm text-gray-500 mt-2">To</label>
          <div class="md:col-span-7">
            <input name="to" id="composeTo" type="text" class="w-full border rounded px-3 py-2 text-sm"
                   placeholder="recipient@example.com, another@example.com" required>
          </div>

          <label class="md:col-span-1 text-sm text-gray-500 mt-2">Cc</label>
          <div class="md:col-span-7">
            <input name="cc" id="composeCc" type="text" class="w-full border rounded px-3 py-2 text-sm"
                   placeholder="optional, comma separated">
          </div>

          <label class="md:col-span-1 text-sm text-gray-500 mt-2">Bcc</label>
          <div class="md:col-span-7">
            <input name="bcc" id="composeBcc" type="text" class="w-full border rounded px-3 py-2 text-sm"
                   placeholder="optional, comma separated">
          </div>

          <label class="md:col-span-1 text-sm text-gray-500 mt-2">Subject</label>
          <div class="md:col-span-7">
            <input name="subject" id="composeSubject" type="text" class="w-full border rounded px-3 py-2 text-sm" required>
          </div>
        </div>

        <div>
          <textarea name="body" id="composeBody" class="w-full border rounded px-3 py-2 text-sm"
                    rows="12" required></textarea>
        </div>
      </div>

      <div class="border-t px-4 py-3 flex items-center justify-end gap-2">
        <button type="button" class="px-4 py-2 text-sm rounded border hover:bg-gray-50" onclick="SLV.closeCompose()">Cancel</button>
        <button type="submit" class="px-4 py-2 text-sm rounded bg-amber-600 text-white hover:bg-amber-700">Send</button>
      </div>
    </form>
  </div>
</div>

{{-- Helpers --}}
<script>
  window.SLV = window.SLV || {};

  SLV.togglePayload = function () {
    const el = document.getElementById('payloadBox');
    el.classList.toggle('hidden');
  };

  (function () {
    const modal    = document.getElementById('composeModal');
    const titleEl  = document.getElementById('composeTitle');
    const actionEl = document.getElementById('composeAction');
    const toEl     = document.getElementById('composeTo');
    const ccEl     = document.getElementById('composeCc');
    const bccEl    = document.getElementById('composeBcc');
    const subjEl   = document.getElementById('composeSubject');
    const bodyEl   = document.getElementById('composeBody');

    // server data
    const fromName  = @json($fromName);
    const fromEmail = @json($fromEmail);
    const created   = @json($created);
    const formKey   = @json($formKey);
    const ref       = @json($ref);
    const propTitle = @json($propTitle);
    const propUrl   = @json($propUrl);
    const message   = @json($submission->payload['message'] ?? '(no message)');

    const baseSubject = ((ref ? `[${ref}] ` : '') + (propTitle || (formKey.replaceAll('_',' ')))).trim();

    function quotedBody() {
      let lines = [];
      lines.push(`On ${created}, ${fromName} <${fromEmail}> wrote:`);
      lines.push('');
      lines.push(message);
      lines.push('');
      lines.push('—');
      lines.push(`Form: ${formKey}`);
      if (ref)       lines.push(`Reference: ${ref}`);
      if (propTitle) lines.push(`Property: ${propTitle}`);
      if (propUrl)   lines.push(`Link: ${propUrl}`);
      return lines.join('\n');
    }

    function show() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function hide() { modal.classList.add('hidden');   modal.classList.remove('flex'); }

    SLV.openCompose = function(kind) {
      actionEl.value = kind; // reply | reply_all | forward
      titleEl.textContent = (kind === 'forward') ? 'Forward' : (kind === 'reply_all' ? 'Reply all' : 'Reply');

      if (kind === 'forward') {
        toEl.value  = '';
        ccEl.value  = '';
        bccEl.value = '';
        subjEl.value = `Fwd: ${baseSubject}`;
        bodyEl.value = `\n\n${quotedBody()}`;
      } else {
        toEl.value  = fromEmail || '';
        ccEl.value  = '';  // fill with team defaults if you want
        bccEl.value = '';
        subjEl.value = `Re: ${baseSubject}`;
        bodyEl.value = `\n\n${quotedBody()}`;
      }
      show();
      toEl.focus();
    };

    SLV.closeCompose = hide;

    // close on ESC / backdrop click
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });
    modal.addEventListener('click', (e) => { if (e.target === modal) hide(); });
  })();
</script>
@endsection
