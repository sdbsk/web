const ServerSideRender = window.wp.serverSideRender;
const {registerBlockType} = window.wp.blocks;
const {InspectorControls, useBlockProps} = window.wp.blockEditor;
const {PanelBody, TextControl, SelectControl, ToggleControl, RadioControl, Card, CardBody} = window.wp.components;
const {useState, useRef} = window.React;

const emptyCampaign = {
    campaign_id: '',
    has_onetime_payment: false,
    has_recurring_payment: false,
    default_recurring_amount: '',
    default_onetime_amount: '',
}

function campaignAttributes(campaign) {
    const result = {
        ...emptyCampaign,
        campaign_id: campaign?.darujme_id ?? '',
    };

    [1, 2, 3, 4].forEach((index) => {
        result[`onetime_amount_${index}`] = (campaign ?? [])[`onetime_amount_${index}`] ?? '';
        result[`recurring_amount_${index}`] = (campaign ?? [])[`recurring_amount_${index}`] ?? '';
        result.has_onetime_payment ||= '' !== result[`onetime_amount_${index}`];
        result.has_recurring_payment ||= '' !== result[`recurring_amount_${index}`];
    });

    return result;
}

function availablePaymentFrequencies(campaign) {
    if (null === campaign) {
        return [];
    }

    const result = [];

    ['onetime', 'recurring'].forEach((type) => {
        if (campaign[`has_${type}_payment`] && [1, 2, 3, 4].reduce((acc, index) => acc || campaign[`${type}_amount_${index}`], false)) {
            result.push(type);
        }
    })

    return result;

}

function CampaignSelector({campaignId, campaigns, onChange}) {
    const campaignsById = useRef((campaigns ?? []).reduce((acc, campaign) => ({
        ...acc,
        [campaign.darujme_id]: campaignAttributes(campaign)
    }), {}));

    return <SelectControl
        label="Vyber Darujme zbierku"
        help={'Po pridaní zbierky v Darujme môže trvať niekoľko minút dokým sa objaví v tomto zozname.'}
        value={campaignId ?? ''}
        options={[
            {label: 'Žiadna zbierka', value: ''},
            ...(campaigns ?? []).map((campaign) => ({
                label: campaign.title,
                value: campaign.darujme_id
            }))
        ]}
        onChange={(value) => onChange(campaignsById.current[value] ?? emptyCampaign)}
        __nextHasNoMarginBottom
    />
}

function uniqueNonEmpty(items) {
    return items.reduce((acc, item) => acc.includes(item) || '' === item ? acc : [...acc, item], []);
}

function CampaignAmountsControl({amounts, enabled, defaultAmount, toggleLabel, amountLabel, onToggle, onChange, onChangeDefault}) {
    const notEmptyAmounts = uniqueNonEmpty(amounts);

    return <Card>
        <CardBody size={'small'}>
            <ToggleControl
                label={toggleLabel}
                help={enabled ? 'Nemusíš použiť všetky 4 prednastavené sumy' : ''}
                checked={enabled}
                onChange={(value) => onToggle(value)}
            />
            {0 !== notEmptyAmounts.length && <RadioControl
                label="Prednastavená suma"
                selected={defaultAmount}
                options={notEmptyAmounts.map((amount) => ({label: amount, value: amount}))}
                onChange={(value) => onChangeDefault(value)}
            />}
            {enabled && amounts.map((amount, index) => <TextControl
                key={index}
                label={`${index + 1}. ${amountLabel}`}
                value={amount}
                type={'number'}
                onChange={(value) => onChange(index + 1, value)}/>)}
        </CardBody>
    </Card>
}

function amounts(campaign, frequency) {
    return [1, 2, 3, 4].map((index) => campaign[`${frequency}_amount_${index}`]);
}

registerBlockType('saleziani/darujme-form', {
    category: 'theme',
    description: 'Získajte nových adresátov vašich newslettrov.',
    example: {},
    icon: 'email',
    title: 'Darujme formulár',
    edit: ({attributes, setAttributes: setBlockAttributes, name}) => {
        const onetimeAmounts = amounts(attributes, 'onetime');
        const recurringAmounts = amounts(attributes, 'recurring');

        const notEmptyOnetimeAmounts = onetimeAmounts.filter((amount) => amount !== '');
        const notEmptyRecurringAmounts = recurringAmounts.filter((amount) => amount !== '');

        const availableFrequencies = availablePaymentFrequencies(attributes);

        function setAttributes(campaign) {
            const newAttributes = {...attributes, ...campaign};
            const currentAvailableFrequencies = availablePaymentFrequencies(newAttributes);

            ['onetime', 'recurring'].forEach((frequency) => {
                const newAmounts = uniqueNonEmpty(amounts(newAttributes, frequency))

                if (!newAmounts.includes(newAttributes[`default_${frequency}_amount`])) {
                    campaign[`default_${frequency}_amount`] = newAmounts[0] ?? '';
                }
            })

            if (!currentAvailableFrequencies.includes(newAttributes.payment_frequency)) {
                campaign.payment_frequency = currentAvailableFrequencies[0] ?? 'onetime';
            }

            setBlockAttributes(campaign);
        }

        let ClientRendering = false;

        if (!attributes.campaign_id) {
            ClientRendering = () => <CampaignSelector onChange={setAttributes} campaigns={attributes.campaigns}/>;
        } else if (
            (!attributes.has_onetime_payment || 0 === notEmptyOnetimeAmounts.length) &&
            (!attributes.has_recurring_payment || 0 === notEmptyRecurringAmounts.length)
        ) {
            ClientRendering = () => <div>
                V nastaveniach bloku je potrebné povoliť jednorazové a/alebo pravidelné platby a zadať sumy.
            </div>;
        }

        return <div>
            <InspectorControls key="setting">
                <PanelBody
                    title={'Settings'}
                    initialOpen={true}
                >
                    <CampaignSelector onChange={setAttributes} campaigns={attributes.campaigns}
                                      campaignId={attributes.campaign_id}/>

                    {attributes.campaign_id && <>
                        <TextControl
                            label="Titulok"
                            value={attributes.title}
                            onChange={(value) => setAttributes({title: value})}
                        />
                        {attributes.has_onetime_payment && attributes.has_recurring_payment && availableFrequencies.length > 1 &&
                            <Card>
                                <CardBody size={'small'}>
                                    <RadioControl
                                        label="Prednastavená frekvencia platby"
                                        selected={attributes.payment_frequency}
                                        options={[
                                            {label: 'Jednorazovo', value: 'onetime'},
                                            {label: 'Pravidelne', value: 'recurring'},
                                        ]}
                                        onChange={(value) => setAttributes({payment_frequency: value})}
                                    />
                                </CardBody>
                            </Card>}

                        <CampaignAmountsControl
                            enabled={attributes.has_onetime_payment}
                            amounts={onetimeAmounts}
                            defaultAmount={attributes.default_onetime_amount}
                            onToggle={(value) => setAttributes({has_onetime_payment: value})}
                            onChange={(index, value) => setAttributes({[`onetime_amount_${index}`]: value})}
                            onChangeDefault={(value) => setAttributes({default_onetime_amount: value})}
                            amountLabel={'jednorazová suma'}
                            toggleLabel={'Umožniť jednorazové platby'}/>

                        <CampaignAmountsControl
                            enabled={attributes.has_recurring_payment}
                            amounts={recurringAmounts}
                            defaultAmount={attributes.default_recurring_amount}
                            onToggle={(value) => setAttributes({has_recurring_payment: value})}
                            onChange={(index, value) => setAttributes({[`recurring_amount_${index}`]: value})}
                            onChangeDefault={(value) => setAttributes({default_recurring_amount: value})}
                            amountLabel={'pravidelná suma'}
                            toggleLabel={'Umožniť pravidelné platby'}/>
                    </>}
                </PanelBody>
            </InspectorControls>
            {ClientRendering ? <ClientRendering/> :
                <ServerSideRender attributes={attributes} block={name} key={'preview'} httpMethod={'POST'}/>}
        </div>
    }
});
