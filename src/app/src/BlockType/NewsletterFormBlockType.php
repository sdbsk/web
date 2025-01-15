<?php

declare(strict_types=1);

namespace App\BlockType;

use WP_Block;
use WP_Post;

class NewsletterFormBlockType extends AbstractBlockType implements BlockTypeInterface
{
    public function attributes(): array
    {
        return [
            'description' => [
                'default' => '',
                'type' => 'string',
            ],
            'optionals' => [
                'default' => [],
                'type' => 'array',
            ],
            'primary' => [
                'default' => 'newsletter',
                'type' => 'string',
            ],
            'source' => [
                'default' => 'web-saleziani-sk',
                'type' => 'string',
            ],
            'title' => [
                'default' => 'Chcete sledovať, čo máme nové? Pridajte sa do nášho newslettra.',
                'type' => 'string',
            ],
            'url' => [
                'default' => 'https://sdbsk.ecomailapp.cz/public/subscribe/1/43c2cd496486bcc27217c3e790fb4088',
                'type' => 'string',
            ],
        ];
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        return $this->wrapContent($block, '
                <h3>' . $attributes['title'] . '</h3>
                <form method="post" action="' . $attributes['url'] . (str_contains($attributes['url'], '?') ? '&' : '?') . 'source=' . preg_replace('~[^a-zA-Z0-9\-]~', '', $attributes['source']) . '">
                    <input type="email" name="email" placeholder="Vaša emailová adresa" required="required">'
            . (empty($attributes['description']) ? '' : ('<div class="description">' . $attributes['description'] . '</div>'))
            . '<label class="input-checkbox d-none">
                   <input type="checkbox" name="custom_fields[' . strtoupper($attributes['primary']) . ']" value="ano" checked="checked">
                   <span class="label">' . strtoupper($attributes['primary']) . '</span>
               </label>'
            . (empty($attributes['optionals']) ? '' : (implode('', array_map(
                    fn(WP_Post $newsletter): string => '<label class="input-checkbox">
                        <input type="checkbox" name="custom_fields[' . strtoupper($newsletter->post_name) . ']" value="ano">
                        <span class="label">' . $newsletter->post_title . '</span>
                    </label>',
                    get_posts(['post__in' => $attributes['optionals'], 'post_type' => 'newsletter', 'posts_per_page' => -1,]),
                )) . '<div class="border-top w-100 mt-3 pt-3"></div>')) .
            '<label class="input-checkbox">
                 <input type="checkbox" name="gdpr" required="required">
                 <span class="label">Súhlasím so spracúvaním osobných údajov</span>
             </label>
             <button type="submit" name="submit" class="mt-2">Registrovať</button>
             </form>
        ');
    }
}
