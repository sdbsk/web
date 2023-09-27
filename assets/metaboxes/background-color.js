(wp => {
    if ('page' === window.pagenow) {
        wp.plugins.registerPlugin('background-color', {
            render: () => {
                const canvas = document.querySelector('.editor-styles-wrapper');
                const colors = wp.data.useSelect('core/block-editor').getSettings().colors;
                const meta = wp.data.useSelect((select) => select('core/editor').getEditedPostAttribute('meta')) ?? {};
                const {editPost} = wp.data.useDispatch('core/editor');
                const [backgroundColor, setBackgroundColor] = wp.element.useState(meta.background_color);
                const selectedBackground = colors.filter((c) => backgroundColor === c.slug)[0];
                const selectedBackgroundColor = undefined === selectedBackground ? null : selectedBackground.color;

                if (canvas instanceof Element) {
                    canvas.style.backgroundColor = selectedBackgroundColor;
                }

                wp.element.useEffect(() => {
                    editPost({meta: {...meta, background_color: backgroundColor}});
                }, [backgroundColor]);

                return wp.element.createElement(wp.editPost.PluginDocumentSettingPanel, {title: 'Farba pozadia'},
                    wp.element.createElement(wp.components.ColorPalette, {
                        colors: colors,
                        clearable: true,
                        disableCustomColors: true,
                        onChange: (value, index) => {
                            setBackgroundColor(undefined === index ? null : colors[index].slug);
                            canvas.style.backgroundColor = value ?? null;
                        },
                        value: selectedBackgroundColor
                    })
                );
            }
        });
    }
})(window.wp);
