(wp => {
    const tags = [];

    load();

    return wp.blocks.registerBlockType('saleziani/latest-posts', {
        attributes: {
            count: {
                default: 3,
                type: 'integer'
            },
            tag: {
                default: 0,
                type: 'integer'
            }
        },
        category: 'theme',
        description: 'Zobrazte najnovšie články.',
        example: {},
        icon: 'text-page',
        title: 'Najnovšie články',
        edit: props => wp.element.createElement('div', wp.blockEditor.useBlockProps(), [
                wp.element.createElement(wp.blockEditor.InspectorControls, {key: 'inspector-controls'},
                    wp.element.createElement(wp.components.PanelBody, {key: 'target-page', title: 'Nastavenia'}, [
                            wp.element.createElement(wp.components.RangeControl, {
                                key: 'count',
                                label: 'Počet článkov',
                                max: 12,
                                min: 1,
                                onChange: (value) => props.setAttributes({count: value}),
                                value: props.attributes.count
                            }),
                            wp.element.createElement(wp.components.SelectControl, {
                                key: 'tag',
                                label: 'Tag',
                                onChange: (value) => props.setAttributes({tag: value}),
                                options: tags,
                                value: props.attributes.tag
                            })
                        ]
                    )
                ),
                wp.element.createElement(wp.serverSideRender, {attributes: props.attributes, block: props.name, key: 'preview'})
            ]
        )
    });

    function load(page = 1) {
        if (1 === page) {
            tags.push({label: 'Všetky', value: 0});
        } else {
            tags.pop();
        }

        fetch(window.location.origin + '/wp-json/wp/v2/tags/?_fields=id,name&order=asc&per_page=100&page=' + page).then(response => response.json().then(json => {
            json.forEach(tag => tags.push({label: tag.name, value: tag.id}));

            if (response.headers.get('x-wp-totalpages') > page) {
                load(++page);
            }
        }));
    }
})(window.wp);
