import ServerSideRender from '@wordpress/server-side-render';

export default {
    name: 'theme/latest-default-category-posts',
    title: 'Najnovšie články z predvolenej kategórie',
    category: 'theme',
    edit: (props) => <ServerSideRender block={'theme/latest-default-category-posts'} attributes={props.attributes}/>
};
