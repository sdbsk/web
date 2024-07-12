const cssClassPrefix = 'wp-template-';
const defaultTemplate = 'post' === window.pagenow ? 'narrow' : 'page';
const selectors = [
    '.editor-styles-wrapper .edit-post-visual-editor__post-title-wrapper',
    '.editor-styles-wrapper .wp-block-post-content'
];

wp.data && wp.data.subscribe(() => {
    let template = wp.data.select('core/editor').getEditedPostAttribute('template');

    if (undefined === template || 0 === template.length) {
        template = defaultTemplate;
    }

    selectors.forEach((selector) => {
        const element = document.querySelector(selector);

        if (element instanceof Element) {
            element.classList.forEach((templateClass) => {
                if (templateClass.startsWith(cssClassPrefix)) {
                    element.classList.remove(templateClass);
                }
            });

            element.classList.add(cssClassPrefix + template);
        }
    });
});
