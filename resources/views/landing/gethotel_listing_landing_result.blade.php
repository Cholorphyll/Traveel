<table class="table table-hover">
  <thead>
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Slug</th>
      <th>Meta Title</th>
      <th>Location ID</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>    
    @forelse ($listings as $key => $listing)
    <tr>
      <td>{{ $listings->firstItem() + $key }}</td>
      <td>{{ $listing->name }}</td>
      <td>{{ $listing->slug }}</td>
      <td>{{ $listing->meta_tag_title }}</td>
      <td>{{ $listing->location_id }}</td>
      <td>
        <div class="btn-group" role="group">
          <a href="{{ route('edit.hotel.listing.landing', $listing->id) }}" class="btn btn-sm btn-primary">
            <i class="ti ti-edit"></i> Edit
          </a>
          <form action="{{ route('delete.hotel.listing.landing', $listing->id) }}" method="POST"
            style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"
              onclick="return confirm('Are you sure you want to delete this listing?');">
              <i class="ti ti-trash"></i> Delete
            </button>
          </form>
        </div>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center">No landing pages found</td>
    </tr>
    @endforelse
  </tbody>
</table>