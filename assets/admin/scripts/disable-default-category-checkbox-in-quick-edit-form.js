if ('edit-post' === window.pagenow) {
    const input = document.querySelector('.category-checklist > li > label > input');

    input.disabled = true;
    input.style.cursor = 'not-allowed';
}
