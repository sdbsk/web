export default selector => new Promise(resolve => {
    const element = document.querySelector(selector);

    if (element) {
        return resolve(element);
    }

    const observer = new MutationObserver(() => {
        const element = document.querySelector(selector);

        if (element) {
            resolve(element);
            observer.disconnect();
        }
    });

    observer.observe(document.body, {subtree: true, childList: true});
});
