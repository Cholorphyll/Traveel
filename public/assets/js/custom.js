// Function to convert input text to slug format
function convertToSlug(text) {
    return text
        .toLowerCase()                   // Convert to lowercase
        .trim()                         // Remove whitespace from both ends
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word characters
        .replace(/\-\-+/g, '-');        // Replace multiple - with single -
}

// Add event listener when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Get the name input element
    const nameInput = document.getElementById('name');
    // Get the slug input element (assuming you have one with id="slug")
    const slugInput = document.getElementById('slug');

    // Add input event listener to name field
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            // Convert name to slug and set it as slug input value
            slugInput.value = convertToSlug(this.value);
        });
    }
});
