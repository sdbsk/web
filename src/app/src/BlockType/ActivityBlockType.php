<?php

declare(strict_types=1);

namespace App\BlockType;

use App\Service\Stack;
use DateTime;
use WP_Block;

class ActivityBlockType extends AbstractBlockType implements BlockTypeInterface
{
    public function __construct(private readonly Stack $stack)
    {
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        $post = get_post();
        $buttonText = get_post_meta($this->stack->page()->ID, 'button-text', true);
        $buttonUrl = get_post_meta($this->stack->page()->ID, 'button-url', true);
        $activityTags = get_the_terms($post->ID, 'activity_tag');

        $tagsList = '';
        if ($activityTags && !is_wp_error($activityTags)) {
            foreach ($activityTags as $tag) {
                $tagsList .= '<li>' . esc_html($tag->name) . '</li>';
            }
        }

        $activityDates = [];

        foreach (get_post_meta($this->stack->page()->ID, 'date') as $item) {
            $decoded = json_decode($item, true);
            if (isset($decoded['start'], $decoded['end'])) {
                $start = new DateTime($decoded['start']);
                $end = new DateTime($decoded['end']);

                if ($end < new DateTime()) {
                    continue;
                }

                if ($start->format('H:i') === '00:00' && $end->format('H:i') === '00:00') {
                    if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
                        $activityDates[] = $start->format('j. n. Y');
                    } else {
                        if ($start->format('Y') === $end->format('Y')) {
                            $activityDates[] = $start->format('j. n.') . ' - ' . $end->format('j. n. Y');
                        } else {
                            $activityDates[] = $start->format('j. n. Y') . ' - ' . $end->format('j. n. Y');
                        }
                    }
                } else {
                    if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
                        $activityDates[] = $start->format('j. n. Y H:i') . ' - ' . $end->format('H:i');
                    } else {
                        $activityDates[] = $start->format('j. n. Y H:i') . ' - ' . $end->format('j. n. Y H:i');
                    }
                }
            }
        }

        $dates = implode(', ', $activityDates);

        $buttons = '';
        if ($buttonText && $buttonUrl) {
            $buttons .= '<!-- wp:buttons -->
            <div class="wp-block-buttons"><!-- wp:button -->
                <div class="wp-block-button">
                    <a class="wp-block-button__link wp-element-button" href="' . $buttonUrl . '">' . $buttonText . '</a>
                </div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->';
        }

        $content = '<div class="activity-list">
    <div class="activity-item">
        <div class="activity-item-title">
            <h2>' . get_the_title() . '</h2>
            <ul class="tags">' . $tagsList . '</ul>
        </div>
        <p class="activity-item-venue">' . get_post_meta($this->stack->page()->ID, 'venue', true) . '</p>
        <div class="activity-item-content">
            <p>' . get_the_excerpt() . '</p>
            <p>' . $dates . '</p>
        </div>
        <div class="activity-item-bottom-content">' .
            $buttons .
            '<a class="activity-item-link" href="' . get_permalink($post) . '">Zobraziť viac</a>
            <p>' . get_post_meta($this->stack->page()->ID, 'bottom-text', true) . '</p>
        </div>
    </div>
</div>';

        return $this->wrapContent($block, $content);
    }
}
