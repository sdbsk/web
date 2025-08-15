if ('page' === window.pagenow) {
    const {registerPlugin, getPlugin} = window.wp.plugins;
    const {PluginPostStatusInfo} = window.wp.editPost;
    const {CheckboxControl} = window.wp.components;
    const {withSelect, withDispatch} = window.wp.data;
    const {compose} = window.wp.compose;

    let pluginRegistered = false;

    let HasNavigationField = (props) => <PluginPostStatusInfo className={'edit-post-post-author'}>
        <CheckboxControl
            label={'Has Navigation'}
            checked={!!props.has_navigation}
            onChange={props.onHasNavigationChange}
        />
    </PluginPostStatusInfo>;

    HasNavigationField = compose([
        withSelect(select => {
            const meta = select('core/editor').getEditedPostAttribute('meta');
            return ({has_navigation: meta && meta.has_navigation === '1'});
        }),
        withDispatch(dispatch => ({
            onHasNavigationChange: (checked) => {
                dispatch('core/editor').editPost({meta: {has_navigation: checked ? '1' : ''}});
            }
        }))
    ])(HasNavigationField);

    wp.data.subscribe(() => {
        const plugin = getPlugin('has-navigation');

        if (undefined === plugin) {
            const post = wp.data.select('core/editor').getCurrentPost();

            if (post && 0 === post.parent && false === pluginRegistered) {
                registerPlugin('has-navigation', {render: HasNavigationField});
                pluginRegistered = true;
            }
        }
    });
}
