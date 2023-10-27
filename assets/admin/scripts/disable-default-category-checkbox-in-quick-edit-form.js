import onMount from '../util/on-mount';

onMount('.category-checklist > li > label > input').then((input) => {
    input.disabled = true;
    input.style.cursor = 'not-allowed';
});
