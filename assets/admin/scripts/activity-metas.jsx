if ('activity' === window.pagenow) {
    const {registerPlugin} = window.wp.plugins;
    const {PluginPostStatusInfo} = window.wp.editPost;
    const {TextControl} = window.wp.components;
    const {withSelect, withDispatch} = window.wp.data;
    const {compose} = window.wp.compose;

    const Fields = (props) => <PluginPostStatusInfo>
        <TextControl label={'Venue'} value={props.venue} onChange={value => props.onVenueChange(value)}/>
        <TextControl label={'Text tlačidla'} value={props.button_text} onChange={value => props.onButtonTextChange(value)}/>
    </PluginPostStatusInfo>;

    registerPlugin('activity-metas', {
        render: compose([
            withSelect(select => {
                const meta = select('core/editor').getEditedPostAttribute('meta');

                return ({venue: meta ? meta.venue : ''});
            }), withDispatch(dispatch => ({
                onButtonTextChange: (value) => {
                    dispatch('core/editor').editPost({meta: {button_text: value}});
                },
                onVenueChange: (value) => {
                    dispatch('core/editor').editPost({meta: {venue: value}});
                }
            }))
        ])(Fields)
    });
}
