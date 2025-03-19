wp.domReady && wp.richText && wp.domReady(() => {
    [
        'core/code',
        'core/image',
        'core/keyboard',
        'core/language',
        'core/strikethrough',
        'core/subscript',
        'core/superscript',
        'core/text-color'
    ].forEach(format => {
        wp.richText.unregisterFormatType(format);
    });
});

