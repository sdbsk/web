if ('edit-post' === window.pagenow) {
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.querySelector('.inline-edit-wrapper .category-checklist input');

        input.disabled = true;
        input.style.cursor = 'not-allowed';
    });
}
