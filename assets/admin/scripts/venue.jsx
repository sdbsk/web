if ('activity' === window.pagenow) {
    const {registerPlugin} = window.wp.plugins;
    const {PluginPostStatusInfo} = window.wp.editPost;
    const {TextControl} = window.wp.components;
    const {withSelect, withDispatch} = window.wp.data;
    const {compose} = window.wp.compose;

    let VenueField = (props) => <PluginPostStatusInfo>
        <TextControl label="Venue" value={props.venue} onChange={newVenue => props.onVenueChange(newVenue)}/>
    </PluginPostStatusInfo>;

    VenueField = compose([
        withSelect(select => {
            const meta = select('core/editor').getEditedPostAttribute('meta');

            return ({venue: meta ? meta.venue : ''});
        }), withDispatch(dispatch => ({
            onVenueChange: (value) => {
                dispatch('core/editor').editPost({meta: {venue: value}});
            }
        }))
    ])(VenueField);

    registerPlugin('venue', {render: VenueField});
}
