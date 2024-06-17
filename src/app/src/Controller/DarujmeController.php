<?php

namespace App\Controller;

use App\BlockType\DarujmeFormBlockType;
use App\Form\Type\DarujmeDonationType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DarujmeController extends AbstractController
{
    #[Route('/a/form/{campaign}', name: 'darujme_form', methods: ['POST'])]
    public function index(Request $request, DarujmeFormBlockType $darujmeFormBlockType, string $campaign): Response
    {
//        try {
        $campaignDecoded = $darujmeFormBlockType->decodedCampaign($campaign);

        $form = $darujmeFormBlockType->form($campaignDecoded);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $form = $this->createFormBuilder()->create('donation', DarujmeDonationType::class, [
                'action' => 'https://api.darujme.sk/v1/donations/post/'
            ])
                ->setData($this->darujmeData($campaignDecoded, $data))
                ->getForm();

//                $form = $this->formFactory
//                    ->createNamedBuilder('donation', DarujmeDonationType::class, $this->dajnato->darujmeData($campaign, $data), [
//                        'action' => 'https://api.darujme.sk/v1/donations/post/'
//                    ])
//                    ->getForm();

            return new Response(preg_replace('/donation\\[([a-zA-Z_]*)]/ms', '$1', $this->renderView('darujmeForm.html.twig', array(
                'form' => $form->createView(),
            ))));
        }

        return new Response($darujmeFormBlockType->formContent($form, $campaignDecoded));
//        } catch (Exception) {
//            throw $this->createNotFoundException();
//        }
    }

    private function darujmeData(array $campaign, array $data): array
    {
        if ('recurring' === $data['onetimeOrRecurring']) {
            $value = $data['recurringAmount'] ?? null;
            $paymentType = $data['recurringPaymentType'];
        } else {
            $value = $data['onetimeAmount'] ?? null;
            $paymentType = $data['onetimePaymentType'];
        }

        $value = empty($value) ? $data['otherAmount'] : $value;
        $value = round($value * ($data['expenses'] ? 1.039 : 1));
        $expenses = $data['expenses'] ? 'yes' : 'no';

        return [
            'campaign_id' => $campaign['campaign_id'],
            'value' => $value,
            'payment_method_id' => $paymentType,
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'additional_data' => [
                DarujmeDonationType::EXPENSES_FIELD_ID => $expenses,
            ]
        ];
    }
}
