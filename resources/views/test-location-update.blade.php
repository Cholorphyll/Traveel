<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Location Content Update</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; }
        pre { white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Location Content Update</h1>
        
        <div class="form-group">
            <label for="location_id">Location ID:</label>
            <input type="number" id="location_id" name="location_id" required>
        </div>
        
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="5" required>This is test content for the location.</textarea>
        </div>
        
        <div class="form-group">
            <button id="submitBtn">Update Location Content</button>
        </div>
        
        <div class="result" style="display: none;">
            <h3>Response:</h3>
            <pre id="response"></pre>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            // Get the CSRF token from the meta tag
            const token = $('meta[name="csrf-token"]').attr('content');
            
            $('#submitBtn').click(function() {
                const locationId = $('#location_id').val();
                const content = $('#content').val();
                
                if (!locationId) {
                    alert('Please enter a Location ID');
                    return;
                }
                
                // Show loading state
                const btn = $(this);
                const originalText = btn.text();
                btn.prop('disabled', true).text('Sending...');
                
                // Log what we're sending
                console.log('Sending data:', {
                    location_id: locationId,
                    content: content,
                    _token: token
                });
                
                // Method 1: Using FormData with fetch
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('location_id', locationId);
                formData.append('content', content);
                
                fetch('{{ route("update_location_content") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response:', data);
                    $('#response').text(JSON.stringify(data, null, 2));
                    $('.result').show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#response').text('Error: ' + error.message);
                    $('.result').show();
                })
                .finally(() => {
                    btn.prop('disabled', false).text(originalText);
                });
            });
        });
    </script>
</body>
</html>
