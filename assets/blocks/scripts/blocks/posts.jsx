import {postList} from '@wordpress/icons';

const {registerBlockVariation, unregisterBlockVariation} = window.wp.blocks;

const VARIATION_NAME = 'saleziani/posts';

registerBlockVariation('core/query', {
        name: VARIATION_NAME,
        title: 'Zoznam článkov',
        description: 'Zobrazí zoznam článkov',
        isActive: ({namespace, query}) => {
            return (
                namespace === VARIATION_NAME
                && query.postType === 'post'
            );
        },
        icon: postList,
        isDefault: true,
        innerBlocks: [
            [
                'core/post-template',
                {
                    className: 'wp-block-post',
                },
                [
                    ['core/group', {className: 'row'}, [
                        ['core/group', {className: 'col-md-5 col-lg-4 order-md-1'}, [
                            ['core/post-featured-image', {isLink: true, sizeSlug: 'medium'}]]
                        ],
                        ['core/group', {className: 'col-md-7 col-lg-8'}, [
                            ['core/post-terms', {term: "category", separator: ""}],
                            ['core/post-title', {isLink: true}],
                            ['core/post-excerpt', {moreText: "Čítať viac", lock: {move: true, remove: true}}],
                        ]],
                    ],
                    ],
                ],
            ],
            ['core/query-pagination'],
        ],
        allowedControls: [ /*'inherit', 'order', 'search', */'taxQuery'],
        attributes: {
            namespace: VARIATION_NAME,
            className: 'wp-block-saleziani-posts',
            query: {
                perPage: 6,
                pages: 0,
                offset: 0,
                postType: 'post',
                order: 'desc',
                orderBy: 'date',
                author: '',
                search: '',
                exclude: [],
                sticky: '',
                inherit: false,
            },
        },
        scope: ['inserter'],
    }
);
