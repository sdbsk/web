if ('post-php' === window.adminpage) {
    const input = document.querySelector('[aria-label=Kategórie] input');

    if (input instanceof Element) {
        input.disabled = true;
        input.style.cursor = 'not-allowed';
    } else {
        if (Number.isInteger(timeoutId)) {
            clearTimeout(timeoutId);
        }

        timeoutId = setTimeout(() => execute(), 32);
    }
}
