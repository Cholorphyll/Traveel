<x-app-layout>
  <div class="pc-container">
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page">Add User</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Add User</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
              <form action="{{ route('store_user') }}" method="post">
                @csrf
                <div class="row">
                  <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group mt-3">
                      <strong>Name</strong>
                      <input type="text" name="name" class="form-control rounded-3" required>
                    </div>
                    <div class="form-group mt-3">
                      <strong>Username</strong>
                      <input type="text" name="username" class="form-control rounded-3" required>
                    </div>
                    <div class="form-group mt-3">
                      <strong>Email</strong>
                      <input type="email" name="email" class="form-control rounded-3" required>
                    </div>
                    <div class="form-group mt-3">
                      <strong>Role</strong>
                      <select name="role" class="form-control rounded-3" required>
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group mt-3">
                  <button type="submit" class="btn btn-outline-dark">Save</button>
                  <a href="{{ route('users') }}" class="btn btn-outline-dark margin-l">Cancel</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>