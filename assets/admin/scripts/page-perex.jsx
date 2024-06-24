if ('page' === window.pagenow) {
    const {registerPlugin, getPlugin} = window.wp.plugins;
    const {PluginPostStatusInfo} = window.wp.editPost;
    const {TextareaControl} = window.wp.components;
    const {withSelect, withDispatch} = window.wp.data;
    const {compose} = window.wp.compose;

    let pluginRegistered = false;

    let PagePerexField = (props) => <PluginPostStatusInfo className={'edit-post-post-author'}>
        <TextareaControl label={'Perex'} value={props.page_perex} onChange={newPagePerex => props.onPagePerexChange(newPagePerex)}/>
    </PluginPostStatusInfo>;

    PagePerexField = compose([
        withSelect(select => {
            const meta = select('core/editor').getEditedPostAttribute('meta');

            return ({page_perex: meta ? meta.page_perex : ''});
        }), withDispatch(dispatch => ({
            onPagePerexChange: (value) => {
                dispatch('core/editor').editPost({meta: {page_perex: value}});
            }
        }))
    ])(PagePerexField);

    wp.data.subscribe(() => {
        const plugin = getPlugin('page-perex');

        if (undefined === plugin) {
            const post = wp.data.select('core/editor').getCurrentPost();

            if (post && 0 === post.parent && false === pluginRegistered) {
                registerPlugin('page-perex', {render: PagePerexField});
                pluginRegistered = true;
            }
        }
    });
}
