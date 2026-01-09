@push('styles')
<style>
  #recommendationsModal { --bs-modal-width: 98vw; }

  #recommendationsModal .rec-line{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display:block;
  }

  #recommendationsModal .rec-list{
    max-height: 520px;
    overflow:auto;
  }

  #recommendationsModal .rec-card{
    min-height: 220px;
  }

  /* 🔥 Hover image preview */
  .rec-hover-wrap{
    position: relative;
    cursor: pointer;
  }

  .rec-hover-img{
    position: absolute;
    left: 0;
    top: 100%;
    margin-top: 6px;
    width: 260px;
    height: 170px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,.25);
    display: none;
    z-index: 9999;
    background:#fff;
  }

  .rec-hover-wrap:hover .rec-hover-img{
    display: block;
  }
</style>
@endpush


<div class="modal fade" id="recommendationsModal" tabindex="-1" aria-labelledby="recommendationsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="recommendationsModalLabel">Property Recommendations</h5>
          <div class="small text-muted">Search properties → add to staging → add client emails → send</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">

          {{-- LEFT --}}
          <div class="col-12 col-lg-7">
            <div class="d-flex gap-2 mb-2">
              <input type="text" class="form-control" id="recPropertySearch"
                     placeholder="Search by reference, title, type, or location">
              <button type="button" class="btn btn-outline-secondary" id="recPropertyClear">Clear</button>
            </div>

            <div class="border rounded p-2">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="small text-muted">Results</div>
                <div class="small text-muted" id="recPropertyMode">Suggested</div>
              </div>

              <ul class="list-group list-group-flush rec-list" id="recPropertyResults">
                <li class="list-group-item text-muted">Loading...</li>
              </ul>
            </div>
          </div>

          {{-- RIGHT --}}
          <div class="col-12 col-lg-5">

            {{-- Staging --}}
            <div class="border rounded p-2 mb-3 rec-card">
              <div class="d-flex align-items-center justify-content-between">
                <div class="fw-semibold">Staging (<span id="recStageCount">0</span>)</div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="recStageClear" disabled>Clear</button>
              </div>
              <div class="small text-muted mt-1">Selected properties will appear here.</div>

              <ul class="list-group list-group-flush mt-2 rec-list" id="recStageList" style="max-height:180px;">
                <li class="list-group-item text-muted" id="recStageEmpty">No properties added yet.</li>
              </ul>
            </div>

            {{-- Clients --}}
            <div class="border rounded p-2 rec-card">
              <div class="d-flex align-items-center justify-content-between">
                <div class="fw-semibold">Client recipients (<span id="recClientCount">0</span>)</div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="recClientClear" disabled>Clear</button>
              </div>

              <input type="text" class="form-control my-2" id="recClientSearch"
                     placeholder="Search client by name or email">

              <div class="small text-muted mb-2">Suggestions</div>
              <ul class="list-group list-group-flush rec-list" id="recClientResults" style="max-height:140px;">
                <li class="list-group-item text-muted">Start typing (3+ chars)...</li>
              </ul>

              <hr>

              <div class="small text-muted mb-2">Selected recipients</div>
              <ul class="list-group list-group-flush rec-list" id="recClientSelected" style="max-height:120px;">
                <li class="list-group-item text-muted" id="recClientEmpty">No clients selected yet.</li>
              </ul>

              <button type="button" class="btn btn-success w-100 mt-3" id="recSendBtn" disabled>
                Send Recommendations
              </button>

              <div class="small text-muted mt-2" id="recSendHint">
                Add at least 1 property + 1 client to enable sending.
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
@push('scripts')
<script>
(() => {
  const $ = (id) => document.getElementById(id);
  const token = document.querySelector('meta[name="csrf-token"]')?.content;

  const propSearchUrl   = "/admin/recommendations/properties/search";
  const clientSearchUrl = "/admin/recommendations/clients/search";
  const sendUrl         = "/admin/recommendations/send";

  const stage = new Map();
  const clients = new Map();

  const propInput = $("recPropertySearch");
  const propClear = $("recPropertyClear");
  const propMode  = $("recPropertyMode");
  const propList  = $("recPropertyResults");

  const stageList = $("recStageList");
  const stageEmpty = $("recStageEmpty");
  const stageCount = $("recStageCount");
  const stageClear = $("recStageClear");

  const clientInput = $("recClientSearch");
  const clientResults = $("recClientResults");
  const clientSelected = $("recClientSelected");
  const clientEmpty = $("recClientEmpty");
  const clientCount = $("recClientCount");
  const clientClear = $("recClientClear");

  const sendBtn = $("recSendBtn");
  const sendHint = $("recSendHint");

  const esc = s => String(s ?? '').replace(/[&<>"']/g, m =>
    ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])
  );

  const money = v => v ? "€" + Number(v).toLocaleString() : "N/A";

  function setSendState(){
    const ok = stage.size && clients.size;
    sendBtn.disabled = !ok;
    sendHint.textContent = ok
      ? "Ready to send."
      : "Add at least 1 property + 1 client to enable sending.";
  }

  function renderStage(){
    stageCount.textContent = stage.size;
    stageClear.disabled = !stage.size;
    stageList.innerHTML = "";

    if (!stage.size){
      stageEmpty.classList.remove("d-none");
      stageList.appendChild(stageEmpty);
      return;
    }

    stageEmpty.classList.add("d-none");

    stage.forEach((p,id)=>{
      const li = document.createElement("li");
      li.className = "list-group-item d-flex justify-content-between gap-2";
      li.innerHTML = `
        <div class="flex-fill">
          <div class="fw-semibold rec-line">#${esc(p.reference)} — ${esc(p.title)}</div>
          <div class="small text-muted rec-line">
            ${esc(p.location)} • ${esc(p.property_type)} • ${p.bedrooms ?? "N/A"} bed • ${money(p.price)}
          </div>
        </div>
        <button class="btn btn-sm btn-outline-danger">&times;</button>
      `;
      li.querySelector("button").onclick = () => {
        stage.delete(id);
        renderStage();
        renderProperties(lastProps,lastMode);
        setSendState();
      };
      stageList.appendChild(li);
    });
  }

  let lastProps = [], lastMode = "random";

  function renderProperties(items, mode){
    lastProps = items;
    lastMode = mode;
    propList.innerHTML = "";
    propMode.textContent = mode === "search" ? "Results" : "Suggested";

    if (!items.length){
      propList.innerHTML = `<li class="list-group-item text-muted">No results found.</li>`;
      return;
    }

    items.forEach(p=>{
      const li = document.createElement("li");
      li.className = "list-group-item d-flex justify-content-between gap-2";
      li.dataset.id = p.id;

      const added = stage.has(String(p.id));

      li.innerHTML = `
        <div class="flex-fill rec-hover-wrap">
          <div class="fw-semibold rec-line">
            #${esc(p.reference)} — ${esc(p.title)}
          </div>
          <div class="small text-muted rec-line">
            ${esc(p.location)} • ${esc(p.property_type)} • ${p.bedrooms ?? "N/A"} bed • ${money(p.price)}
          </div>
          ${p.thumb ? `<img src="${esc(p.thumb)}" class="rec-hover-img" onerror="this.remove()">` : ``}
        </div>
        <button class="btn btn-sm ${added ? 'btn-outline-secondary' : 'btn-primary'}" ${added?'disabled':''}>
          ${added ? 'Added' : 'Add'}
        </button>
      `;

      li.querySelector("button").onclick = () => {
        stage.set(String(p.id), p);
        renderStage();
        renderProperties(lastProps,lastMode);
        setSendState();
      };

      propList.appendChild(li);
    });
  }

  async function fetchProperties(q=""){
    const r = await fetch(`${propSearchUrl}?q=${encodeURIComponent(q)}&limit=15`);
    const j = await r.json();
    renderProperties(j.items || [], j.mode || "random");
  }

  async function fetchClients(q){
    const r = await fetch(`${clientSearchUrl}?q=${encodeURIComponent(q)}&limit=10`);
    const j = await r.json();
    clientResults.innerHTML = "";

    if (!j.items?.length){
      clientResults.innerHTML = `<li class="list-group-item text-muted">No client results.</li>`;
      return;
    }

    j.items.forEach(c=>{
      const li = document.createElement("li");
      li.className = "list-group-item d-flex justify-content-between gap-2";
      li.innerHTML = `
        <div class="flex-fill">
          <div class="fw-semibold rec-line">${esc(c.name)}</div>
          <div class="small text-muted rec-line">${esc(c.email)}</div>
        </div>
        <button class="btn btn-sm btn-primary">Add</button>
      `;
      li.querySelector("button").onclick = ()=>{
        if (!c.email) return;
        clients.set(String(c.id), c);
        renderClients();
        setSendState();
      };
      clientResults.appendChild(li);
    });
  }

  function renderClients(){
    clientCount.textContent = clients.size;
    clientClear.disabled = !clients.size;
    clientSelected.innerHTML = "";

    if (!clients.size){
      clientEmpty.classList.remove("d-none");
      clientSelected.appendChild(clientEmpty);
      return;
    }

    clientEmpty.classList.add("d-none");

    clients.forEach((c,id)=>{
      const li = document.createElement("li");
      li.className = "list-group-item d-flex justify-content-between";
      li.innerHTML = `
        <div>
          <div class="fw-semibold rec-line">${esc(c.name)}</div>
          <div class="small text-muted rec-line">${esc(c.email)}</div>
        </div>
        <button class="btn btn-sm btn-outline-danger">&times;</button>
      `;
      li.querySelector("button").onclick = ()=>{
        clients.delete(id);
        renderClients();
        setSendState();
      };
      clientSelected.appendChild(li);
    });
  }

  $("recommendationsModal").addEventListener("shown.bs.modal",()=>{
    fetchProperties("");
    propInput.focus();
  });

  let t1=null,t2=null;
  propInput.oninput=()=>{ clearTimeout(t1); t1=setTimeout(()=>{
    propInput.value.length>=3?fetchProperties(propInput.value):fetchProperties("");
  },300); };

  clientInput.oninput=()=>{ clearTimeout(t2); t2=setTimeout(()=>{
    clientInput.value.length>=3?fetchClients(clientInput.value):clientResults.innerHTML='<li class="list-group-item text-muted">Start typing (3+ chars)...</li>';
  },300); };

  propClear.onclick=()=>{ propInput.value=""; fetchProperties(""); };
  stageClear.onclick=()=>{ stage.clear(); renderStage(); renderProperties(lastProps,lastMode); setSendState(); };
  clientClear.onclick=()=>{ clients.clear(); renderClients(); setSendState(); };

  sendBtn.onclick=async()=>{
    if (sendBtn.disabled) return;
    sendBtn.disabled=true; sendBtn.textContent="Sending...";
    try{
      const r=await fetch(sendUrl,{
        method:"POST",
        headers:{
          "Content-Type":"application/json",
          "X-CSRF-TOKEN":token
        },
        body:JSON.stringify({
          property_ids:[...stage.keys()].map(Number),
          client_ids:[...clients.keys()].map(Number)
        })
      });
      const j=await r.json();
      alert(`Sent! Emails: ${j.sent}`);
      stage.clear(); clients.clear();
      renderStage(); renderClients(); setSendState();
    }catch(e){
      alert("Send failed");
    }finally{
      sendBtn.textContent="Send Recommendations";
    }
  };

  renderStage();
  renderClients();
  setSendState();
})();
</script>
@endpush
