<?php

namespace Kuma\BroodjesBundle\Controller;

use Kuma\BroodjesBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends Controller
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     * @Route("/credits/add", name="kuma_broodjes_credits_add")
     */
    public function addCreditsAction(Request $request)
    {
        $url = $request->headers->get('referer');
        $amount = $request->request->get('amount');
        $amount = str_replace(',', '.', $amount);
        $translator = $this->get('translator');
        $session = $request->getSession();

        if ($amount == '' || !is_numeric($amount)) {
            return new RedirectResponse(
                $url
            );
        }

        $cashButton = $request->request->get('cash');

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $userId = $request->request->get('user');
            $user = $this->getDoctrine()->getRepository('KunstmaanAdminBundle:User')->find($userId);
        } else {
            $user = $this->getUser();
        }

        if (!is_null($cashButton)) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
                $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
                    ['user' => $user]
                );
                $newBalance = $userInfo[0]->getCredits() + $amount;

                $date = date('Y-m-d, G:m:s');
                $transaction = new Transaction();
                $transaction->setUser($user);
                $transaction->setCreateDate($date);
                $transaction->setMethod('cash');
                $transaction->setCredits($amount);

                $userInfo[0]->setCredits($newBalance);

                $this->getDoctrine()->getManager()->persist($userInfo[0]);
                $this->getDoctrine()->getManager()->persist($transaction);
                $this->getDoctrine()->getManager()->flush();

                $creditIncrease = $translator->trans(
                    'flashbag.notice.credit.increase',
                    ['%amount%' => $amount]
                );
                $session->getFlashBag()->add('success', $creditIncrease);

                $webhook = $this->get('kumabroodjesbundle.helper.service.webhook');
                $webhook->sendCurl(
                    '@' . $userInfo[0]->getSlackName(),
                    'Je saldo is verhoogd met ' . $amount . ' Euro door een cash transactie.'
                );
            }

            return new RedirectResponse(
                $url
            );
        } else {
            $mollie = new \Mollie_API_Client();
            $apiKey = $this->getParameter('mollie.api.key.test');
            $mollie->setApiKey($apiKey);
            $orderId = md5(time());

            try {
                $payment = $mollie->payments->create(
                    [
                        'amount' => $amount,
                        'description' => 'Recharge broodjesplatform',
                        'redirectUrl' => 'http://broodjesplatform.nine.staging.kunstmaan.com/en/admin/dashboard/',
                        'webhookUrl' => 'http://kunstmaan:kunstmaan@broodjesplatform.nine.staging.kunstmaan.com/payment/webhook/call',
                        'metadata' => [
                            'order_id' => $orderId,
                        ],
                    ]
                );

                $transaction = new Transaction();
                $transaction->setUser($user);
                $transaction->setCreateDate($payment->createdDatetime);
                $transaction->setCredits($amount);
                $transaction->setPaidDate($payment->expiredDatetime);
                $transaction->setOrderId($orderId);
                $transaction->setMollieTransactionId($payment->id);
                $transaction->setStatus($payment->status);
                $transaction->setProfileId($payment->profileId);

                $this->getDoctrine()->getManager()->persist($transaction);
                $this->getDoctrine()->getManager()->flush();

                $waitPayment = $translator->trans(
                    'flashbag.notice.credit.slack.wait'
                );
                $session->getFlashBag()->add('info', $waitPayment);

                header('Location: ' . $payment->getPaymentUrl());
                exit;
            } catch (\Mollie_API_Exception $e) {
                echo 'API call failed: ' . htmlspecialchars($e->getMessage());
                echo ' on field ' . htmlspecialchars($e->getField());
            }

            return new RedirectResponse(
                $url
            );
        }
    }

    /**
     * @param Request $request
     * @Route("/webhook/call")
     *
     * @return JsonResponse
     */
    public function webhookAction(Request $request)
    {
        $id = $request->get('id');
        $mollie = new \Mollie_API_Client();
        $apiKey = $this->getParameter('mollie.api.key.test');
        $mollie->setApiKey($apiKey);

        $payment = $mollie->payments->get($id);

        /** @var Transaction $transction */
        $transaction = $this->getDoctrine()->getRepository('KumaBroodjesBundle:Transaction')->getMollieTransaction($id);

        if (!count($transaction)) {
            return new JsonResponse(
                null, 400, [
                    'Content-Type' => 'application/json',
                ]
            );
        }
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['user' => $transaction[0]->getUser()]
        );
        if ($payment->isPaid()) {
            $transaction[0]->setPaidDate($payment->paidDatetime);
            $transaction[0]->setMethod($payment->method);
            $transaction[0]->setStatus($payment->status);
            $newCredits = $payment->amount + $userInfo[0]->getCredits();
            $userInfo[0]->setCredits($newCredits);

            $this->getDoctrine()->getEntityManager()->persist($transaction[0]);
            $this->getDoctrine()->getEntityManager()->persist($userInfo[0]);
            $this->getDoctrine()->getEntityManager()->flush();

            $webhook = $this->get('kumabroodjesbundle.helper.service.webhook');
            $webhook->sendCurl(
                '@' . $userInfo[0]->getSlackName(),
                'Je saldo is verhoogd met ' . $payment->amount . ' Euro door een Mollie transactie.'
            );

            return new JsonResponse(
                null, 200, [
                    'Content-Type' => 'application/json',
                ]
            );
        } elseif ($payment->isCancelled()) {
            $transaction[0]->setStatus($payment->status);
            $webhook = $this->get('kumabroodjesbundle.helper.service.webhook');
            $webhook->sendCurl(
                '@' . $userInfo[0]->getSlackName(),
                'Je betaling is gecanceld, het gaat over het bedrag van ' . $payment->amount . ' Euro, met als transactieId ' . $id
            );

            $this->getDoctrine()->getEntityManager()->persist($transaction[0]);
            $this->getDoctrine()->getEntityManager()->flush();

            return new JsonResponse(
                null, 200, [
                    'Content-Type' => 'application/json',
                ]
            );
        } elseif ($payment->isExpired()) {
            $transaction[0]->setStatus($payment->status);
            $webhook = $this->get('kumabroodjesbundle.helper.service.webhook');
            $webhook->sendCurl(
                '@' . $userInfo[0]->getSlackName(),
                'Je betaling is verlopen, het gaat over het bedrag van ' . $payment->amount . ' Euro, met als transactieId ' . $id
            );

            $this->getDoctrine()->getEntityManager()->persist($transaction[0]);
            $this->getDoctrine()->getEntityManager()->flush();

            return new JsonResponse(
                null, 200, [
                'Content-Type' => 'application/json',
            ]
            );
        }

        return new JsonResponse(
            null, 400, [
                'Content-Type' => 'application/json',
            ]
        );
    }
}
