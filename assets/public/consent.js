import * as CookieConsent from "vanilla-cookieconsent";

window.getCookieConsentPreferences = () => {
    return CookieConsent.getUserPreferences();
}

function showAllIframes() {
    var elements = document.getElementsByClassName('cc-iframe');
    for (var i = 0; i < elements.length; i++) {
        elements[i].outerHTML = elements[i].getAttribute('data-iframe');
    }
}

window.showBlockedIframes = () => {
    CookieConsent.acceptCategory('targeting');
    showAllIframes();

    return false;
}

CookieConsent.run({
    disablePageInteraction: true,
    revision: 1,
    cookie: {
        name: 'cc_cookie',
        sameSite: "Lax",
        expiresAfterDays: () => {
            if (!CookieConsent.acceptedCategory('targeting')) return 0;

            return 365;
        },
    },

    // https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions
    guiOptions: {
        consentModal: {
            layout: 'box',
            position: 'middle center',
            equalWeightButtons: true,
            flipButtons: true
        },
        preferencesModal: {
            layout: 'box',
            equalWeightButtons: true,
            flipButtons: true
        }
    },

    onFirstConsent: ({cookie}) => {
        if (cookie.categories.includes('targeting')) {
            showAllIframes();
        }
    },

    onConsent: ({cookie}) => {
        if (cookie.categories.includes('targeting')) {
            showAllIframes();
        }
    },

    onChange: ({cookie}) => {
        if (cookie.categories.includes('targeting')) {
            showAllIframes();
        }
    },

    categories: {
        necessary: {
            enabled: true,
            readOnly: true
        },
        functional: {},
        targeting: {}
    },

    language: {
        default: 'sk',
        translations: {
            sk: {
                consentModal: {
                    title: 'Súhlasíte s používaním cookies?',
                    description: 'Cookies a ďalšie technológie sledovania používame na zlepšenie vášho zážitku z prehliadania našich webových stránok a na cielené reklamy. ',
                    acceptAllBtn: 'Súhlasím',
                    showPreferencesBtn: 'Chcem vidieť možnosti',
                },
                preferencesModal: {
                    title: 'Vaše nastavenia cookies',
                    acceptAllBtn: 'Súhlasím',
                    acceptNecessaryBtn: 'Nesúhlasím',
                    savePreferencesBtn: 'Súhlasím s vybranými',
                    closeIconLabel: 'Zavrieť',
                    // inspiracia: https://www.bain.com/about/cookie-policy/
                    sections: [
                        {
                            description: `Tu môžete nastaviť ako sa majú používať cookies a služby, ktoré ukladajú cookies. Nastavenia môžete kedykoľvek zmeniť na stránke <a href="/zasady-pouzivania-cookies" target="_blank">zásad používania cookies</a>. Ak chcete odmietnuť svoj súhlas s konkrétnymi činnosťami opísanými nižšie, prepnite prepínače na vypnuté a stlačte tlačidlo "Súhlasím s vybranými" alebo môžete odmietnuť všetko stlačením tlačidla "Nesúhlasím".`,
                        },
                        {
                            title: 'Nevyhnutné cookies',
                            description: 'Nevyhnutné na prehliadanie webstránky a používanie jej základných funkcií.',
                            linkedCategory: 'necessary',
                            cookieTable: {
                                headers: {
                                    company: 'Organizácia',
                                    domains: 'Domény',
                                    description: '',
                                },
                                body: [
                                    {
                                        company: 'Saleziáni don Bosca – Slovenská provincia',
                                        domains: 'saleziani.sk',
                                        description: 'Saleziani sú katolícka rehoľná kongregácia založená svätým Jánom Boscom v roku 1859. Zameriavajú sa na prácu s mládežou a výchovu, prevádzkujú školy, internáty a ďalšie zariadenia podporujúce mládež.' +
                                            '<br><br><a href="https://gdpr.kbs.sk/obsah/sekcia/h/cirkev/p/zavazne-predpisy-rkc" target="_blank">Ochrana osobných údajov</a>'
                                    },
                                ]
                            }
                        },
                        {
                            title: 'Marketingové a spresňujúce cookies',
                            description: 'Zvyčajne ich poskytujú tretie strany, napríklad sociálne siete, aby vám mohli poskytovať relevantný obsah.',
                            linkedCategory: 'targeting',
                            cookieTable: {
                                headers: {
                                    company: 'Organizácia',
                                    domains: 'Domény',
                                    description: '',
                                },
                                body: [
                                    {
                                        company: 'Google LLC',
                                        domains: 'doubleclick.net, youtube.com, google.com, ggpht.com, gstatic.com, googleapis.com, ytimg.com',
                                        description: 'Google LLC je americká nadnárodná technologická spoločnosť, ktorá sa špecializuje na služby a produkty súvisiace s internetom, medzi ktoré patrí vyhľadávač, cloud computing, softvér a hardvér.' +
                                            '<br><br><a href="https://policies.google.com/terms" target="_blank">Podmienky používania</a> | <a href="https://policies.google.com/privacy" target="_blank">Ochrana osobných údajov</a>'
                                    },
                                    {
                                        company: 'Meta Platforms, Inc.',
                                        domains: 'facebook.net, facebook.com',
                                        description: 'Meta Platforms, Inc., pod obchodným názvom Meta, je americký nadnárodný technologický konglomerát so sídlom v Menlo Parku v Kalifornii. Spoločnosť vlastní a prevádzkuje okrem iných produktov a služieb aj Facebook, Instagram, Threads a WhatsApp.' +
                                            '<br><br><a href="https://www.facebook.com/terms/" target="_blank">Podmienky používania</a> | <a href="https://www.facebook.com/privacy/policy" target="_blank">Ochrana osobných údajov</a>'
                                    },
                                ]
                            }
                        },
                        {
                            title: 'Viac informácií',
                            description: 'Ak máte akékoľvek otázky týkajúce sa našich zásad týkajúcich sa cookies a vašich možností, <a href="/kontakt">kontaktujte nás</a>.'
                        }
                    ]
                }
            }
        }
    }
});

function renderCookieConsentOrganizations(category) {
    const organizations = CookieConsent.getConfig().language.translations.sk.preferencesModal.sections.reduce((acc, section) => {
        if (section.linkedCategory === category) {
            return section.cookieTable.body ?? [];
        }

        return acc;
    }, []);

    document.querySelectorAll(`.cc-${category}-cookies`).forEach(element => {
        let content = '<div class="cc-cookies-table"><table class="table table-bordered cc-cookies-table"><thead><tr><th>Organizácia</th><th>Domény</th></tr></thead><tbody>';

        organizations.forEach((organization) => {
            content += `<tr><td>${organization.company}<br><small>${organization.description}</small></td><td>${organization.domains}</td></tr>`;
        });

        content += '</tbody></table></div>';

        element.outerHTML = content;
    });
}

function openPreferences(event) {
    event.preventDefault();
    CookieConsent.showPreferences()
}

document.querySelectorAll('.cc-open-preferences').forEach(element => {
    element.addEventListener('click', openPreferences);
});


renderCookieConsentOrganizations('necessary');
renderCookieConsentOrganizations('targeting');
