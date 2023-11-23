const {registerBlockVariation, unregisterBlockVariation} = window.wp.blocks;

const MY_VARIATION_NAME = 'saleziani/posts';

setTimeout(() => {
    unregisterBlockVariation('core/query', 'posts-list');
}, 1000);

registerBlockVariation('core/query', {
        name: MY_VARIATION_NAME,
        title: 'Post Listsss',
        description: 'Displays a list of books',
        isActive: ({namespace, query}) => {
            return (
                namespace === MY_VARIATION_NAME
                && query.postType === 'post'
            );
        },
        icon: 'email',
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
                            ['core/post-featured-image', {isLink: true}]]
                        ],
                        ['core/group', {className: 'col-md-7 col-lg-8'}, [
                            ['core/post-terms', {term: "category", separator: ""}],
                            ['core/post-title', {isLink: true}],
                            ['core/post-excerpt', {moreText: "Čítať viac", lock: {move: true, remove: true}}],
                            // ['core/read-more', {content: "Čítať viac"}]
                        ]],
                    ],
                    ],
                ],
            ],
            ['core/query-pagination'],
            // [ 'core/query-no-results' ],
        ],
        allowedControls: [ /*'inherit', 'order', 'search', */'taxQuery'],
        attributes: {
            namespace: MY_VARIATION_NAME,
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
