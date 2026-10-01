const cssClassPrefix = 'wp-template-';
const defaultTemplate = 'post' === window.pagenow ? 'narrow' : 'page';
const selectors = [
    '.editor-styles-wrapper .edit-post-visual-editor__post-title-wrapper',
    '.editor-styles-wrapper .wp-block-post-content'
];

wp.data && wp.data.subscribe(() => {
    const editor = wp.data.select('core/editor');

    // Not every block editor registers core/editor — the site editor does not.
    if (undefined === editor) {
        return;
    }

    let template = editor.getEditedPostAttribute('template');

    if (undefined === template || 0 === template.length) {
        template = defaultTemplate;
    }

    // Since WP 7 the post editor canvas is always an iframe, so the elements live
    // in its document rather than in the admin page.
    const canvas = document.querySelector('iframe[name="editor-canvas"]');
    const canvasDocument = canvas?.contentDocument ?? document;

    selectors.forEach((selector) => {
        const element = canvasDocument.querySelector(selector);

        if (element instanceof canvasDocument.defaultView.Element) {
            element.classList.forEach((templateClass) => {
                if (templateClass.startsWith(cssClassPrefix)) {
                    element.classList.remove(templateClass);
                }
            });

            element.classList.add(cssClassPrefix + template);
        }
    });
});
