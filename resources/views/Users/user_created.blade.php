<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="card">
                <div class="card-header">
                    <h2>User Created Successfully</h2>
                </div>
                <div class="card-body">
                    @if(session()->has('username') && session()->has('password'))
                        <div class="alert alert-success">
                            <p><strong>Username:</strong> {{ session('username') }}</p>
                            <p><strong>Password:</strong> {{ session('password') }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No user data found in session.
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="/users/add" class="btn btn-primary">Back to Add User</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
