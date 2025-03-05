if ('activity' === window.pagenow) {
    const {registerPlugin} = window.wp.plugins;
    const {PluginPostStatusInfo} = window.wp.editor;
    const {TextControl} = window.wp.components;
    const {withSelect, withDispatch} = window.wp.data;
    const {compose} = window.wp.compose;

    const Fields = (props) => <PluginPostStatusInfo className={'d-block'}>
        <TextControl label={'Text v spodnej časti'} value={props.bottom_text} onChange={newBottomText => props.onBottomTextChange(newBottomText)}/>
        <TextControl label={'Text tlačidla'} value={props.button_text} onChange={newButtonText => props.onButtonTextChange(newButtonText)}/>
        <TextControl label={'URL tlačidla'} value={props.button_url} onChange={newButtonUrl => props.onButtonUrlChange(newButtonUrl)}/>
        <TextControl label={'Venue'} value={props.venue} onChange={newVenue => props.onVenueChange(newVenue)}/>
    </PluginPostStatusInfo>;

    registerPlugin('activity-metas', {
        render: compose([
            withSelect(select => {
                const meta = select('core/editor').getEditedPostAttribute('meta');

                console.log(meta);

                return {
                    bottom_text: meta ? meta.bottom_text : '', button_text: meta ? meta.button_text : '', button_url: meta ? meta.button_url : '', venue: meta ? meta.venue : ''
                };
            }), withDispatch(dispatch => ({
                onBottomTextChange: (value) => {
                    dispatch('core/editor').editPost({meta: {bottom_text: value}});
                }, onButtonTextChange: (value) => {
                    dispatch('core/editor').editPost({meta: {button_text: value}});
                }, onButtonUrlChange: (value) => {
                    dispatch('core/editor').editPost({meta: {button_url: value}});
                }, onVenueChange: (value) => {
                    dispatch('core/editor').editPost({meta: {venue: value}});
                }
            }))
        ])(Fields)
    });
}
