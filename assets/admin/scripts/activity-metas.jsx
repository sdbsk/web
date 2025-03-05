import {registerMetaTextField} from "./fields";

if ('activity' === window.pagenow) {
    registerMetaTextField('Text v spodnej časti', 'bottom-text');
    registerMetaTextField('Text tlačidla', 'button-text');
    registerMetaTextField('URL tlačidla', 'button-url');
    registerMetaTextField('Venue', 'venue');
}
