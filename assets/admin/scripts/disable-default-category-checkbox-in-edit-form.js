import onMount from '../util/on-mount';

onMount('.editor-post-taxonomies__hierarchical-terms-list > .editor-post-taxonomies__hierarchical-terms-choice input').then((input) => {
    input.disabled = true;
    input.style.cursor = 'not-allowed';
});
