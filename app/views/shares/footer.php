</div> <!-- Đóng container -->

<script>
function updateCartBadge(count) {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('d-none');
            badge.style.animation = 'none';
            setTimeout(() => badge.style.animation = 'pulse 0.3s ease', 10);
        } else {
            badge.classList.add('d-none');
        }
    }
}
</script>
</body>
</html>