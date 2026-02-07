<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class InvoiceController extends AbstractController
{
    #[Route('/facture/{id}/pdf', name: 'app_invoice_pdf')]
    #[IsGranted('ROLE_USER')]
    public function invoicePdf(Orders $order): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if ($order->getUser()->getId() !== $user->getId()) {
            return $this->redirectToRoute('app_user_orders');
        }
        $numero = $order->getNumero();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);

        $html = $this->renderView('invoice/pdf.html.twig', [
            'order' => $order,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="facture-' . $numero . '.pdf"',
            ]
        );
    }
}
