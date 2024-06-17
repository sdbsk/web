<?php

namespace App\Command;

use App\Entity\DarujmeCampaign;
use App\Repository\DarujmeCampaignRepository;
use App\Service\DarujmeApi;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyAccess\PropertyAccess;

#[AsCommand(name: 'update:darujme-campaigns')]
class UpdateDarujmeCampaignsCommand extends Command
{
    public function __construct(
        private readonly DarujmeApi                $darujmeApi,
        private readonly DarujmeCampaignRepository $darujmeCampaignRepository,
        private readonly EntityManagerInterface    $entityManager
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $console = new SymfonyStyle($input, $output);
        $campaigns = [];

        foreach ($this->darujmeCampaignRepository->findAll() as $campaign) {
            $campaigns[$campaign->getDarujmeId()] = $campaign;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->getPropertyAccessor();

        foreach ($this->darujmeApi->campaigns() as $campaign) {
            if ('e65f8baa-5c6f-4562-8a9f-9cb765239a6a' === $campaign['id']) {
                // legacy LudiaLudom campaign
                continue;
            }

            $console->writeln("Updating campaign {$campaign['id']} {$campaign['note']}");

            $campaignEntity = ($campaigns[$campaign['id']] ?? new DarujmeCampaign())
                ->setDarujmeId($campaign['id'])
                ->setStatus($campaign['status'] ?? 'unknown')
                ->setLegacy($campaign['is_legacy'] ?? false)
                ->setName($campaign['name'] ?? '')
                ->setNote($campaign['note'] ?? '')
                ->setDonatedTotal(round($campaign['donated_total'] ?? 0) + ($campaign['matching_value'] ?? 0))
                ->setCreatedAt(($campaigns[$campaign['id']] ?? null)?->getCreatedAt() ?? new DateTimeImmutable());

            if ($campaignEntity->getUpdatedAt() > new DateTimeImmutable('-4 hours')) {
                try {

                    try {
                        $details = $this->darujmeApi->publicCampaign($campaign['id']);
                    } catch (Exception $e) {
                        $console->warning("Failed to get public details of campaign {$campaign['id']} {$campaign['note']}");
                        $details = null;
                    }

                    if (null === $details) {
                        $console->writeln("Using fallback private details for campaign {$campaign['id']} {$campaign['note']}");
                        $amounts = $this->amountsFromPrivateDetails($this->darujmeApi->campaign($campaign['id']));
                    } else {
                        $amounts = $this->amountsFromPublicDetails($details);
                    }

                    foreach ($amounts as $property => $value) {
                        $propertyAccessor->setValue($campaignEntity, $property, null === $value ? null : (int)round(str_replace(',', '.', $value) * 100));
                    }
                } catch (Exception $e) {
                    $console->warning("Failed to update campaign {$campaign['id']} {$campaign['note']}: {$e->getMessage()}");
                }
            }

            $this->entityManager->persist($campaignEntity->setUpdatedAt(new DateTimeImmutable()));
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    private function amountsFromPublicDetails(array $details): array
    {
        $amounts = [];

        foreach (DarujmeCampaign::AMOUNT_TYPES as $apiType => $type) {
            for ($i = 1; $i <= 4; $i++) {
                $amounts["{$type}Amount$i"] = $details['donations'][$apiType]['values'][$i - 1] ?? null;
            }
        }

        return $amounts;
    }

    private function amountsFromPrivateDetails(array $details): array
    {
        $amounts = [];

        foreach ($details['templates'] ?? [] as $template) {
            if ('website' !== ($template['type'] ?? false)) {
                continue;
            }

            foreach ($template['content']['donationSelection'] ?? [] as $donationSelection) {
                if ('payment' === $donationSelection['key']) {
                    $apiAmounts = [];

                    foreach ($donationSelection['options'] ?? [] as $option) {
                        $type = DarujmeCampaign::AMOUNT_TYPES[$option['dependentValue'] ?? $option['dependencyValue']] ?? null;
                        $value = preg_replace('/[^\d,.]/', '', $option['label'] ?? '');

                        if (null === $type || empty($value)) {
                            continue;
                        }

                        $apiAmounts[$type][] = $value;
                    }

                    if (!empty($apiAmounts)) {
                        foreach (DarujmeCampaign::AMOUNT_TYPES as $type) {
                            for ($i = 1; $i <= 4; $i++) {
                                $amounts["{$type}Amount$i"] = $apiAmounts[$type][$i - 1] ?? null;
                            }
                        }
                    }

                    if (!empty($amounts)) {
                        return $amounts;
                    }
                }
            }

            $content = $template['content']['content'] ?? '';
            $content = str_replace(["\n", "\r", "\t"], ['', '', ''], $content);
            $content = preg_replace('/ +/', ' ', $content);

            foreach (DarujmeCampaign::AMOUNT_TYPES as $apiType => $type) {
                preg_match_all('/name="price" value="(\d+)" id="widget-price-radio-' . $apiType . '/', $content, $matches);

                for ($i = 1; $i <= 4; $i++) {
                    if (null === ($amounts["{$type}Amount$i"] ?? null)) {
                        $amounts["{$type}Amount$i"] = $matches[1][$i - 1] ?? null;
                    }
                }
            }
        }

        foreach (DarujmeCampaign::AMOUNT_TYPES as $type) {
            for ($i = 1; $i <= 4; $i++) {
                if (!isset($amounts["{$type}Amount$i"])) {
                    $amounts["{$type}Amount$i"] = null;
                }
            }
        }

        return $amounts;
    }
}
