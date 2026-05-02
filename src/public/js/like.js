function toggleLike(itemId) {
    // Check if user is authenticated
    if (window.currentUserId === null) {
        window.location.href = '/login';
        return;
    }
    
    const btn = document.getElementById(`like-btn-${itemId}`);
    
    fetch(`/item/${itemId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.textContent = `いいね (${data.likes_count})`;
    })
    .catch(error => console.error('Error:', error));
}