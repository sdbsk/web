import {registerMetaTextField} from './fields';

const {registerPlugin} = window.wp.plugins;
const {PluginPostStatusInfo} = window.wp.editor;
const {Button} = window.wp.components;
const {withSelect, withDispatch} = window.wp.data;
const {compose} = window.wp.compose;

if ('activity' === window.pagenow) {
    const registerDateField = () => {
        let CollectionField = (props) => {
            const value = props.date ?? [];

            const addItem = () => props.onChange([...value, JSON.stringify({start: new Date().toISOString().slice(0, 16), end: ''})]);

            const removeItem = (index) => {
                const newValue = [...value];
                newValue.splice(index, 1);
                props.onChange(newValue);
            };

            const updateItem = (index, parameter, newValue) => {
                const updatedValue = [...value];
                const date = JSON.parse(updatedValue[index]);

                date[parameter] = newValue;
                updatedValue[index] = JSON.stringify(date);

                props.onChange(updatedValue);
            };

            const inputStyle = {
                marginBottom: '16px'
            };

            const labelStyle = {
                fontSize: '11px',
                fontWeight: 500,
                lineHeight: 1.4,
                textTransform: 'uppercase',
                display: 'block',
                marginBottom: '8px'
            };

            const removeStyle = {
                position: 'absolute',
                top: '20px',
                padding: '4px'
            };

            return <PluginPostStatusInfo>
                <div>
                    {value.map((val, index) => {
                        const date = JSON.parse(val);

                        return <div key={index} style={{position: 'relative'}}>
                            <label style={labelStyle}>Termín {index + 1} začiatok</label>
                            <input
                                onInput={(e) => updateItem(index, 'start', e.target.value)}
                                type={'datetime-local'}
                                style={inputStyle}
                                value={date.start}
                            />
                            <Button isDestructive onClick={() => removeItem(index)} style={removeStyle}>
                                <span className="dashicons dashicons-no"/>
                            </Button>
                            <label style={labelStyle}>Termín {index + 1} koniec</label>
                            <input
                                onInput={(e) => updateItem(index, 'end', e.target.value)}
                                type={'datetime-local'}
                                style={inputStyle}
                                value={date.end}
                            />
                        </div>;
                    })}
                    <Button isPrimary onClick={addItem}>Pridať termín</Button>
                </div>
            </PluginPostStatusInfo>;
        };

        CollectionField = compose([
            withSelect((select) => {
                const meta = select('core/editor').getEditedPostAttribute('meta');
                return {date: Array.isArray(meta.date) ? meta.date : JSON.parse(meta.date)};
            }), withDispatch((dispatch) => ({
                onChange: (value) => dispatch('core/editor').editPost({meta: {date: value}})
            }))
        ])(CollectionField);

        registerPlugin('date', {render: CollectionField});
    };

    registerMetaTextField('Text v spodnej časti', 'bottom-text');
    registerMetaTextField('Text tlačidla', 'button-text');
    registerMetaTextField('URL tlačidla', 'button-url');
    registerMetaTextField('Miesto konania', 'venue');
    registerDateField();
}
