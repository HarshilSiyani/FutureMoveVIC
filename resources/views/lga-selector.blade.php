<div class="mb-6">
    <label for="lga-select" class="block text-sm font-medium text-gray-700 mb-2">Select LGA</label>
    <select id="lga-select" name="lga" class="block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
        @foreach($lgaNames as $lga)
            <option value="{{ $lga }}" @if($lga === $selectedLga) selected @endif>{{ $lga }}</option>
        @endforeach
    </select>
</div>

<script>
    document.getElementById('lga-select').addEventListener('change', function() {
        window.location.href = '{{ route('dashboard') }}?lga=' + this.value;
    });
</script>