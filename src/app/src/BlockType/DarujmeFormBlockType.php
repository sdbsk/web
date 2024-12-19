<?php

declare(strict_types=1);

namespace App\BlockType;

use App\Entity\DarujmeCampaign;
use App\Form\Type\DonationType;
use App\Repository\DarujmeCampaignRepository;
use Generator;
use InvalidArgumentException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use WP_Block;

class DarujmeFormBlockType extends AbstractBlockType implements BlockTypeInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        private readonly DarujmeCampaignRepository $darujmeCampaignRepository,
        private readonly FormFactoryInterface      $formFactory,
        private readonly UrlGeneratorInterface     $urlGenerator,
        private Environment                        $twig,
    )
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->getPropertyAccessor();
    }

    public function attributes(): array
    {
        $attributes = [
            'title' => [
                'default' => 'Chcem darovať',
                'type' => 'string',
            ],
            'campaign_id' => [
                'default' => '',
                'type' => 'string',
            ],
            'payment_frequency' => [
                'default' => 'onetime',
                'type' => 'string',
            ],
            'default_onetime_amount' => [
                'default' => '',
                'type' => 'string',
            ],
            'default_recurring_amount' => [
                'default' => '',
                'type' => 'string',
            ],
            'campaigns' => [
                'default' => $this->campaigns(),
                'type' => 'array',
            ],
            'has_onetime_payment' => [
                'default' => false,
                'type' => 'boolean',
            ],
            'has_recurring_payment' => [
                'default' => false,
                'type' => 'boolean',
            ],
            'form_layout' => [
                'default' => 'full-form',
                'type' => 'string',
            ],
            'widget_button_label' => [
                'default' => 'Darovať',
                'type' => 'string',
            ],
        ];

        foreach ($this->amountKeys() as $amountAttribute => $amountProperty) {
            $attributes[$amountAttribute] = [
                'default' => '',
                'type' => 'string',
            ];
        }

        return $attributes;
    }

    public function form(array $campaign, bool $isAdmin = false, string $formName = '', int $blockIndex = 0): FormInterface
    {
        return $this->formFactory
            ->createNamedBuilder('donation' . ($formName ? '-' . $formName : '') . '-' . $blockIndex, DonationType::class, options: [
                'campaign' => $campaign,
                'disabled' => $isAdmin,
                'action' => $this->urlGenerator->generate('darujme_form', [
                    'campaign' => $this->encodedCampaign([
                        ...$campaign,
                        'block_index' => $blockIndex,
                        'form_layout' => 'full-form',
                        'initiated_by_widget' => ($campaign['initiated_by_widget'] ?? false) || 'widget' === $campaign['form_layout']
                    ]),
                ]),
            ])
            ->getForm();
    }

    public function decodedCampaign(string $encodedCampaign): array
    {
        [$hashedCampaign,] = explode('.', $encodedCampaign);

        $campaign = json_decode(base64_decode($hashedCampaign), true);

        if ($this->encodedCampaign($campaign) !== $encodedCampaign) {
            throw new InvalidArgumentException('Invalid campaign signature');
        }

        return $campaign;
    }

    public function formContent(FormInterface $form, $campaign, bool $isAdmin = false): string
    {
        return $this->twig->render('campaign.html.twig', [
            'donation_form' => $form->createView(),
            'formTitle' => $campaign['title'],
            'isEmbedded' => true,
            'isAdmin' => $isAdmin,
            'canBeOnetime' => $campaign['has_onetime_payment'],
            'canBeRecurring' => $campaign['has_recurring_payment'],
            'formLayout' => $campaign['form_layout'],
            'widgetButtonLabel' => $campaign['widget_button_label'],
        ]);
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        static $blockIndex = 0;

        $campaign = $this->formCampaign($attributes);

        if (!$campaign['has_onetime_payment'] && !$campaign['has_recurring_payment']) {
            return '';
        }

        $isAdmin = defined('REST_REQUEST') && REST_REQUEST;
        $form = $this->form($campaign, $isAdmin, '', $blockIndex++);

        $form->setData([
            'onetimeAmount' => empty($attributes['default_onetime_amount']) ? null : (float)$attributes['default_onetime_amount'],
            'recurringAmount' => empty($attributes['default_recurring_amount']) ? null : (float)$attributes['default_recurring_amount'],
            'onetimeOrRecurring' => $attributes['payment_frequency'],
        ]);

        return $this->wrapContent($block, $this->formContent($form, $campaign, $isAdmin));
    }

    private function encodedCampaign(array $campaign): string
    {
        $hashedCampaign = base64_encode(json_encode($campaign));

        return $hashedCampaign . '.' . hash_hmac('sha256', $hashedCampaign, 'darujme');
    }

    private function formCampaign(array $attributes): array
    {
        $formCampaign = [
            'campaign_id' => $attributes['campaign_id'],
            'title' => $attributes['title'],
            'has_onetime_payment' => $attributes['has_onetime_payment'],
            'has_recurring_payment' => $attributes['has_recurring_payment'],
            'form_layout' => $attributes['form_layout'],
            'widget_button_label' => $attributes['widget_button_label'],
        ];

        foreach (DarujmeCampaign::AMOUNT_TYPES as $amountType) {
            $amountTypeKey = "{$amountType}_options";

            if (!$formCampaign['has_onetime_payment'] && 'onetime' === $amountType) {
                continue;
            }

            if (!$formCampaign['has_recurring_payment'] && 'recurring' === $amountType) {
                continue;
            }

            for ($i = 1; $i <= 4; $i++) {
                $amountValue = $attributes["{$amountType}_amount_$i"] ?? null;

                if (!empty($amountValue)) {
                    $formCampaign[$amountTypeKey][] = $amountValue;
                }
            }

            if (empty($formCampaign[$amountTypeKey])) {
                $formCampaign["has_{$amountType}_payment"] = false;
            }
        }

        return $formCampaign;
    }

    private function campaigns()
    {
        $campaigns = [];

        foreach ($this->darujmeCampaignRepository->findAll() as $campaign) {
            $campaignData = [
                'darujme_id' => $campaign->getDarujmeId(),
                'title' => $campaign->getNote(),
                'status' => $campaign->getStatus(),
            ];

            foreach ($this->amountKeys() as $amountAttribute => $amountProperty) {
                $amount = $this->propertyAccessor->getValue($campaign, $amountProperty);
                $campaignData[$amountAttribute] = null === $amount ? null : (string)round($amount / 100, 2);
            }

            $campaigns[] = $campaignData;
        }

        usort($campaigns, fn($a, $b) => $a['title'] <=> $b['title']);

        return $campaigns;
    }

    private function amountKeys(): Generator
    {
        foreach (DarujmeCampaign::AMOUNT_TYPES as $amountType) {
            for ($i = 1; $i <= 4; $i++) {
                yield "{$amountType}_amount_$i" => "{$amountType}Amount$i";
            }
        }
    }
}
