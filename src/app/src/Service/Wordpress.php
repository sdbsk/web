<?php

namespace App\Service;

use App\BlockType\BlockTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class Wordpress
{
    private string $template;

    public function __construct(
        #[AutowireIterator('wordpress-block-type')] private readonly iterable $blockTypes
    )
    {
        $this->template = wp_get_theme()->get_template();
    }

    public function registerBlockTypes(): static
    {
        $template = wp_get_theme()->get_template();

        /** @var BlockTypeInterface $blockType */
        foreach ($this->blockTypes as $blockType) {
            register_block_type($this->namespacedName($blockType), [
                'attributes' => $blockType->attributes(),
                'render_callback' => [$blockType, 'render'],
            ]);
        }

        return $this;
    }

    private function namespacedName(BlockTypeInterface $object): string
    {
        $name = preg_replace('/^.*([^\\\]+)Block(Type)$/U', '$1', $object::class);
        $name[0] = strtolower($name[0]);
        return $this->template . '/' . strtolower(preg_replace('/([A-Z])/', '-$1', $name));
    }
}
