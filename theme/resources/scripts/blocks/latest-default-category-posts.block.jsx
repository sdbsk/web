import ServerSideRender from '@wordpress/server-side-render';

export default {
    name: 'theme/latest-default-category-posts',
    title: 'Najnovšie články',
    description: 'Zobrazte najnovšie články z predvolenej kategórie. Ak ju chcete zmeniť prejdite do Nastavenia -> Písanie.',
    category: 'theme',
    example: {},
    edit: (props) => <ServerSideRender block={'theme/latest-default-category-posts'} attributes={props.attributes}/>
};
