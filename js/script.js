// Search functionality
const searchInput = document.getElementById('searchInput');
const links = document.querySelectorAll('.link');

searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();

    links.forEach(link => {
        const linkText = link.textContent.toLowerCase();

        if (linkText.includes(searchTerm)) {
            link.style.display = 'block';
        } else {
            link.style.display = 'none';
        }
    });
});

// Undo/Redo functionality
const undoButton = document.getElementById('undoButton');
const redoButton = document.getElementById('redoButton');

// Add your undo/redo logic here

// Edit/Delete functionality
const editButtons = document.querySelectorAll('.edit-btn');
const deleteButtons = document.querySelectorAll('.delete-btn');

editButtons.forEach(button => {
    button.addEventListener('click', function() {
        const linkId = this.dataset.id;
        // Add your edit logic here
    });
});

deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const linkId = this.dataset.id;
        const confirmDelete = confirm('Are you sure you want to delete this link?');

        if (confirmDelete) {
            // Add your delete logic here
        }
    });
});