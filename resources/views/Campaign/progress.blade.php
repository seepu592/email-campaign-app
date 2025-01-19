<div class="container">
    <h1>Campaign: {{ $campaign->name }}</h1>
    <p>Status: {{ ucfirst($campaign->status) }}</p>
    <p>Processed Contacts: <span id="processedContacts">{{ $processedContacts }}</span> / {{ $campaign->total_contacts }}</p>
    <p>Remaining Contacts: <span id="remainingContacts">{{ $remainingContacts }}</span></p>

    <h2>Contacts:</h2>
    <ul>
        @foreach ($campaign->contacts as $contact)
            <li>{{ $contact->name }} - {{ $contact->email }}</li>
        @endforeach
    </ul>
</div>

<script>
    // Poll the server every 5 seconds to update the progress
    setInterval(function() {
    fetch("{{ route('campaign.progress.update', ['campaign' => $campaign->id]) }}")
        .then(response => response.json())
        .then(data => {
            Inertia.update({
                processedContacts: data.processedContacts,
                remainingContacts: data.remainingContacts,
            });
        });
}, 5000);

</script>
