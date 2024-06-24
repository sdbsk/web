<?php

namespace App\BlockType;

use WP_Block;

class NewsletterFormBlockType extends AbstractBlockType implements BlockTypeInterface {
    public function attributes(): array
    {
        return [
            'title' => [
                'default' => 'Chcete sledovať, čo máme nové? Pridajte sa do nášho newslettra.',
                'type' => 'string',
            ],
            'source' => [
                'default' => 'saleziani-sk',
                'type' => 'string',
            ],
        ];
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        return $this->wrapContent($block, '
                <h3>' . $attributes['title'] . '</h3>
                <form method="post" action="https://sdbsk.ecomailapp.cz/public/subscribe/1/43c2cd496486bcc27217c3e790fb4088?source=' . preg_replace('~[^a-zA-Z0-9\-]~', '', $attributes['source']) . '">
                    <input type="email" name="email" placeholder="Vaša emailová adresa" required="required">
                    <label class="input-checkbox">
                        <input type="checkbox" name="gdpr" required="required">
                        <span class="label">Súhlasím so spracúvaním osobných údajov</span>
                    </label>
                    <button type="submit" name="submit">Registrovať</button>
                </form>
        ');
    }
}
