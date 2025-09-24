@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="container mt-4">
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Deals</h4>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newDealModal">+ New</button>
                        <button class="btn btn-secondary">List</button>
                        <button class="btn btn-secondary active">Pipeline</button>
                    </div>
                </div>

                <!-- Pipeline View -->
                <div class="pipeline-container d-flex overflow-auto">

                    <!-- Loop through each stage -->
                    @foreach(['New Lead', 'Interest', 'Follow Up', 'Viewing', 'Negotiation', 'Sales Agreement', 'Land Reg.'] as $stage)
                        <div class="pipeline-stage">
                            <h5>{{ $stage }}</h5>
                            <div class="pipeline-cards" data-stage="{{ $stage }}">
                                @foreach($deals->where('stage', $stage) as $deal)
                                    <div class="card" draggable="true" data-id="{{ $deal->id }}">
                                        <p class="mb-1"><strong>{{ $deal->title }}</strong></p>
                                        <p class="mb-1">{{ $deal->client_name }}</p>
                                        <p class="mb-1 text-muted" style="font-size: 12px;">by {{ $deal->user->name ?? 'Unknown' }}</p>
                                        <p>€{{ number_format($deal->amount, 0) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form to Create New Deal -->
<div class="modal fade" id="newDealModal" tabindex="-1" aria-labelledby="newDealModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newDealModalLabel">Create New Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newDealForm">
                    <div class="mb-3">
                        <label for="dealTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="dealTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="clientName" class="form-label">Client Name</label>
                        <input type="text" class="form-control" id="clientName" required>
                    </div>
                    <div class="mb-3">
                        <label for="dealAmount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="dealAmount" required>
                    </div>
                    <div class="mb-3">
                        <label for="dealStage" class="form-label">Stage</label>
                        <select class="form-select" id="dealStage" required>
                            <option value="New Lead">New Lead</option>
                            <option value="Interest">Interest</option>
                            <option value="Follow Up">Follow Up</option>
                            <option value="Viewing">Viewing</option>
                            <option value="Negotiation">Negotiation</option>
                            <option value="Sales Agreement">Sales Agreement</option>
                            <option value="Land Reg.">Land Reg.</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Deal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    // Handle dragging of cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', card.dataset.id);
            e.dataTransfer.effectAllowed = 'move';
        });
    });

    // Allow dropping into pipeline stages
    document.querySelectorAll('.pipeline-cards').forEach(stage => {
        stage.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });

        stage.addEventListener('drop', (e) => {
            e.preventDefault();
            const cardId = e.dataTransfer.getData('text/plain');
            const card = document.querySelector(`[data-id="${cardId}"]`);
            const newStage = stage.dataset.stage;

            if (card) {
                stage.appendChild(card);

                fetch('/update-deal-stage', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        deal_id: cardId,
                        stage: newStage
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => console.log('Stage Update Success:', data))
                .catch(error => console.error('Stage Update Error:', error));
            }
        });
    });

    // Handle new deal form submission
    document.getElementById('newDealForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const title = document.getElementById('dealTitle').value;
        const clientName = document.getElementById('clientName').value;
        const amount = document.getElementById('dealAmount').value;
        const stage = document.getElementById('dealStage').value;

        fetch('/deals', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                title: title,
                client_name: clientName,
                amount: amount,
                stage: stage
            })
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text();
            }
        })
        .then(data => {
            console.log('Server Response:', data);
            if (data.success) {
                const newCard = document.createElement('div');
                newCard.classList.add('card');
                newCard.setAttribute('draggable', 'true');
                newCard.setAttribute('data-id', data.deal.id);

                newCard.innerHTML = `
                    <p class="mb-1"><strong>${data.deal.title}</strong></p>
                    <p class="mb-1">${data.deal.client_name}</p>
                    <p>€${data.deal.amount}</p>
                `;

                document.querySelector(`[data-stage="${data.deal.stage}"]`).appendChild(newCard);

                // Attach drag event listener to the new card
                newCard.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/plain', newCard.dataset.id);
                    e.dataTransfer.effectAllowed = 'move';
                });

                document.getElementById('newDealForm').reset();
                var modal = bootstrap.Modal.getInstance(document.getElementById('newDealModal'));
                modal.hide();
            }
        })
        .catch(error => console.error('Form Submission Error:', error));
    });

</script>

@endsection