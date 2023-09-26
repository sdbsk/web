(wp => {
    if ('page' === window.pagenow) {
        wp.plugins.registerPlugin('background-color', {
            render: () => {
                const colors = wp.data.useSelect('core/block-editor').getSettings().colors;
                const meta = wp.data.useSelect((select) => select('core/editor').getEditedPostAttribute('meta')) ?? {};
                const {editPost} = wp.data.useDispatch('core/editor');
                const [backgroundColor, setBackgroundColor] = wp.element.useState(meta.background_color);
                const selectedBackgroundColor = colors.filter((c) => backgroundColor === c.slug)[0];

                wp.element.useEffect(() => {
                    editPost({meta: {...meta, background_color: backgroundColor}});
                }, [backgroundColor]);

                return wp.element.createElement(wp.editPost.PluginDocumentSettingPanel, {title: 'Farba pozadia'},
                    wp.element.createElement(wp.components.ColorPalette, {
                        colors: colors,
                        clearable: true,
                        disableCustomColors: true,
                        onChange: (value, index) => setBackgroundColor(undefined === index ? '' : colors[index].slug),
                        value: undefined === selectedBackgroundColor ? '' : selectedBackgroundColor.color
                    })
                );
            }
        });
    }
})(window.wp);
