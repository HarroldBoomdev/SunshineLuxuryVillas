<div class="card p-3">
    <div class="form-group">
        <label for="property_status">Property Status</label>
        <select id="property_status" name="property_status" class="form-control">
            <option value="">None</option>
            <option value="Active" {{ old('property_status', $get('property_status')) === 'Active' ? 'selected' : '' }}>Active</option>
        </select>
    </div>
</div>
