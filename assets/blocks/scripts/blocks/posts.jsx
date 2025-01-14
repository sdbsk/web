import {postList} from '@wordpress/icons';

const {InspectorControls} = window.wp.blockEditor;
const {PanelBody, SelectControl} = window.wp.components;
const {createHigherOrderComponent} = window.wp.compose;
const {registerBlockVariation} = window.wp.blocks;
const {useSelect} = window.wp.data;

const VARIATION_NAME = 'saleziani/posts';

registerBlockVariation('core/query', {
    allowedControls: ['taxQuery'],
    attributes: {
        className: 'wp-block-saleziani-posts',
        namespace: VARIATION_NAME,
        query: {
            author: '',
            exclude: [],
            inherit: false,
            offset: 0,
            order: 'desc',
            orderBy: 'date',
            pages: 0,
            perPage: 6,
            postType: 'post',
            search: '',
            sticky: ''
        }
    },
    description: 'Zobrazí zoznam článkov',
    icon: postList,
    isActive: ({namespace, query}) => (VARIATION_NAME === namespace && 'post' === query.postType),
    isDefault: true,
    innerBlocks: [
        [
            'core/post-template',
            {
                className: 'wp-block-post'
            },
            [
                [
                    'core/group', {className: 'row'}, [
                    [
                        'core/group', {className: 'col-md-5 col-lg-4 order-md-1'}, [
                        ['core/post-featured-image', {isLink: true, sizeSlug: 'medium'}]
                    ]
                    ],
                    [
                        'core/group', {className: 'col-md-7 col-lg-8'}, [
                        ['core/post-title', {isLink: true}],
                        ['core/post-excerpt', {moreText: 'Čítať viac', lock: {move: true, remove: true}}]
                    ]
                    ]
                ]
                ]
            ]
        ],
        ['core/query-pagination']
    ],
    name: VARIATION_NAME,
    scope: ['inserter'],
    title: 'Zoznam článkov'
});

wp.hooks.addFilter('blocks.registerBlockType', 'saleziani/query-with-menu-category-attribute', (settings, name) => {
    if ('core/query' === name) {
        settings.attributes.menuCategory = {
            type: 'number',
            default: 0
        };
    }

    return settings;
});

wp.hooks.addFilter('editor.BlockEdit', 'saleziani/query-with-menu-category-dropdown', createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        if ('core/query' !== props.name) {
            return <BlockEdit {...props} />;
        }

        const categories = useSelect((select) => select('core').getEntityRecords('taxonomy', 'category', {_fields: 'id,name', per_page: -1}), []);

        return (
            <>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody title={'Cukríkové menu'}>
                        <SelectControl
                            label={'Kategória'}
                            value={props.attributes.menuCategory}
                            options={[
                                {label: '', value: 0},
                                ...(categories ? categories.map((category) => ({
                                    label: category.name,
                                    value: category.id
                                })) : [])
                            ]}
                            onChange={(value) => props.setAttributes({menuCategory: parseInt(value)})}
                        />
                    </PanelBody>
                </InspectorControls>
            </>
        );
    };
}, 'withMenuCategoryDropdown'));
