<?php

namespace Kuma\BroodjesBundle\Controller;

use Blameable\Fixture\Document\User;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kuma\BroodjesBundle\Entity\LunchOrder;
use Kuma\BroodjesBundle\Entity\Transaction;
use Kuma\BroodjesBundle\Entity\UserInfo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SlackController extends Controller
{
    /**
     * @Route("/access/verification")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        $clientId = $this->getParameter('slack.api.client.id');
        $clientSecret = $this->getParameter('slack.api.client.secret');
        $token = $this->getParameter('slack.api.token');
        $code = $request->get('code');
        $session = $request->getSession();
        $translator = $this->get('translator');

        $json = file_get_contents(
            'https://slack.com/api/oauth.access?client_id=' . $clientId . '&client_secret=' . $clientSecret . '&code=' . $code
        );
        $obj = json_decode($json);

        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['user' => $user]);
        $userJson = file_get_contents(
            'https://slack.com/api/users.info?token=' . $token . '&user=' . $obj->user->id . '&pretty=1'
        );
        $user = json_decode($userJson);

        $userInfo[0]->setSlackAccessToken($obj->access_token);
        $userInfo[0]->setSlackId($obj->user->id);
        $userInfo[0]->setSlackName($user->user->name);
        $userInfo[0]->setSlackTeamId($obj->team->id);

        $this->getDoctrine()->getManager()->persist($userInfo[0]);
        $this->getDoctrine()->getManager()->flush();

        $slack = $translator->trans(
            'flashbag.notice.login.slack.success'
        );

        $session->getFlashBag()->add('success', $slack);

        return new RedirectResponse($this->generateUrl('kunstmaan_dashboard'));
    }

    /**
     * @Route("/product/list")
     */
    public function listAction(Request $request)
    {
        $slackUserId = $request->get('user_id');
        $token = $request->get('token');
        $requestToken = $this->getParameter('slack.api.request.list.token');

        if ($token !== $requestToken) {
            return new JsonResponse(null, 401, ['HTTP/1.0 401 Unauthorized']);
        }
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['slackId' => $slackUserId]
        );
        $string = '';
        if (count($userInfo)) {
            $endProducts = $this->getDoctrine()->getRepository('KumaBroodjesBundle:EndProduct')->findBy(
                ['user' => $userInfo[0]->getUser()]
            );
            foreach ($endProducts as $endProduct) {
                $string .= '*' . $endProduct->getSlackName() . '* : ' . $endProduct->getName() . ', € ' . $endProduct->getPrice(
                    ) . "\n";
            }
            $array = [
                'text' => 'Hey ' . $userInfo[0]->getSlackName() . ', je huidig saldo bedraagt: € ' . $userInfo[0]->getCredits(
                    ),
                'attachments' => [
                    [
                        'text' => $string,
                        'color' => 'good',
                        'mrkdwn_in' => [
                            'text',
                        ],
                    ],
                ],
            ];
        } else {
            $array = [
                'text' => 'Hey, je bent nog niet aangemeld met slack op het broodjesplatform.',
            ];
        }

        return new JsonResponse($array);
    }

    /**
     * @Route("/product/order")
     */
    public function orderAction(Request $request)
    {
        $slackUserId = $request->get('user_id');
        $token = $request->get('token');
        $requestToken = $this->getParameter('slack.api.request.order.token');
        $prodName = $request->get('text');
        $string = '';

        if ($token !== $requestToken) {
            return new JsonResponse(null, 401, ['HTTP/1.0 401 Unauthorized']);
        }

        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['slackId' => $slackUserId]
        );

        if (!count($userInfo)) {
            $array = [
                'text' => 'Hey, je bent nog niet aangemeld met slack op het broodjesplatform.',
            ];

            return new JsonResponse($array);
        }

        /** @var User $user */
        $user = $this->getDoctrine()->getEntityManager()->getRepository('KunstmaanAdminBundle:User')->find(
            $userInfo[0]->getUser()
        );

        /** @var EndProduct $endproduct */
        $endproduct = $this->getDoctrine()->getRepository('KumaBroodjesBundle:EndProduct')->findBy(
            ['user' => $userInfo[0]->getUser(), 'slackName' => $prodName]
        );

        if (!count($endproduct)) {
            $array = [
                'text' => 'Dit product bestaat niet of is niet jouw product!',
            ];

            return new JsonResponse($array);
        }

        /** @var LunchOrder $order */
        $order = $this->getDoctrine()->getEntityManager()->getRepository('KumaBroodjesBundle:LunchOrder')->isOrderToday(
            $userInfo[0]->getUser()
        );

        /** @var UserInfo $userInfo */
        $userInfo = $this->getDoctrine()->getEntityManager()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['user' => $userInfo[0]->getUser()]
        );

        $endProdPrice = $endproduct[0]->getPrice();

        $orderCheck = count($order);

        //check if there is an order
        if ($orderCheck > 1) {
            $array = [
                'text' => 'Er is meer dan 1 open bestelling, dit kan niet, contacteer Ruud!',
            ];

            return new JsonResponse($array);
        }
        //no current order for today, make new one, and add endproduct
        if ($orderCheck == 0) {
            //check if user has enough credit to pay the endproduct
            if ($userInfo[0]->getCredits() < $endProdPrice) {
                $array = [
                    'text' => 'Je saldo is te laag!',
                ];

                return new JsonResponse($array);
            }
            $newOrder = new LunchOrder();
            $newOrder->setUser($userInfo[0]->getUser());
            $newOrder->addEndProduct($endproduct[0]);
            $newOrder->setPrice($endProdPrice);
            $this->getDoctrine()->getEntityManager()->persist($newOrder);

            $array = [
                'text' => 'Hey ' . $userInfo[0]->getSlackName() . ', je hebt product ' . $endproduct[0]->getName(
                    ) . ' bijgevoegd aan je bestelling! Totale prijs bestelling: € ' . $endProdPrice,
                'attachments' => [
                    [
                        'text' => $endproduct[0]->getName() . ', € ' . $endProdPrice . "\n",
                        'color' => 'good',
                    ],
                ],
            ];
        } else { // there is already an order today, for this user
            $currentPrice = $order[0]->getPrice();
            $newPrice = $currentPrice + $endProdPrice;

            //check if product is already orded today, avoid duplication error
            foreach ($order[0]->getEndProducts() as $p) {
                if ($p->getId() === $endproduct[0]->getId()) {
                    $array = [
                        'text' => 'Dit product zit al in je bestelling!',
                    ];

                    return new JsonResponse($array);
                }
            }
            // check if the total(current order price + endproduct) credit can be payed
            if ($userInfo[0]->getCredits() < $newPrice) {
                $array = [
                    'text' => 'Je saldo is te laag, dit product wordt niet bij je bestelling gevoegd!',
                ];

                return new JsonResponse($array);
            }
            $order[0]->addEndProduct($endproduct[0]);
            $order[0]->setPrice($newPrice);
            $this->getDoctrine()->getEntityManager()->persist($order[0]);

            foreach ($order[0]->getEndproducts() as $endproduct[0]) {
                $string .= $endproduct[0]->getName() . ', € ' . $endproduct[0]->getPrice() . "\n";
            }

            $array = [
                'text' => 'Hey ' . $userInfo[0]->getSlackName() . ', je hebt product ' . $endproduct[0]->getName(
                    ) . ' bijgevoegd aan je bestelling! Totale prijs bestelling: € ' . $newPrice,
                'attachments' => [
                    [
                        'text' => $string,
                        'color' => 'good',
                    ],
                ],
            ];
        }
        $this->getDoctrine()->getEntityManager()->flush();

        return new JsonResponse($array);
    }

    /**
     * @Route("/user/balance")
     */
    public function balanceAction(Request $request)
    {
        $slackUserId = $request->get('user_id');
        $token = $request->get('token');
        $requestToken = $this->getParameter('slack.api.request.balance.token');

        if ($token !== $requestToken) {
            return new JsonResponse(null, 401, ['HTTP/1.0 401 Unauthorized']);
        }
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['slackId' => $slackUserId]
        );

        if (count($userInfo)) {
            $array = [
                'text' => 'Hey ' . $userInfo[0]->getSlackName() . ', je huidig saldo bedraagt: € ' . $userInfo[0]->getCredits(
                    ),
            ];
        } else {
            $array = [
                'text' => 'Hey, je bent nog niet aangemeld met slack op het broodjesplatform.',
            ];
        }

        return new JsonResponse($array);
    }

    /**
     * @Route("/default/toggle")
     */
    public function toggleDefaultAction(Request $request)
    {
        $slackUserId = $request->get('user_id');
        $token = $request->get('token');
        $requestToken = $this->getParameter('slack.api.request.toggle.token');

        if ($token !== $requestToken) {
            return new JsonResponse(null, 401, ['HTTP/1.0 401 Unauthorized']);
        }
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['slackId' => $slackUserId]
        );
        if (!count($userInfo)) {
            $array = [
                'text' => 'Hey, je bent nog niet aangemeld met slack op het broodjesplatform.',
            ];

            return new JsonResponse($array);
        }

        if ($userInfo[0]->getDefaultToggle() == 0) {
            $array = [
                'text' => 'Hey ' . $userInfo[0]->getSlackName() . ', je hebt automatisch bestellen gepauzeerd.',
            ];
            $userInfo[0]->setDefaultToggle(1);
        } else {
            $array = [
                'text' => 'Hey ' . $userInfo[0]->getSlackName() . ', je hebt automatisch bestellen gestart.',
            ];
            $userInfo[0]->setDefaultToggle(0);
        }

        $this->getDoctrine()->getManager()->persist($userInfo[0]);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse($array);
    }

    /**
     * @Route("/default/set")
     */
    public function setDefaultAction(Request $request)
    {
        $slackUserId = $request->get('user_id');
        $token = $request->get('token');
        $requestToken = $this->getParameter('slack.api.request.default.token');

        if ($token !== $requestToken) {
            return new JsonResponse(null, 401, ['HTTP/1.0 401 Unauthorized']);
        }
        $text = $request->get('text');
        $data = explode(' on ', $text);

        if (!isset($data[1])) {
            $array = [
                'text' => "Dit commando klopt niet! Volg voorbeeld: /broodje_setdefaults ['slacknaam'] on ['dagnr']",
            ];

            return new JsonResponse($array);
        }

        if ($data[1] > 0 && $data[1] < 6) {
            /** @var UserInfo $userInfo */
            $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
                ['slackId' => $slackUserId]
            );

            if (!count($userInfo)) {
                $array = [
                    'text' => 'Hey, je bent nog niet aangemeld met slack op het broodjesplatform.',
                ];

                return new JsonResponse($array);
            }

            $endproduct = $this->getDoctrine()->getRepository('KumaBroodjesBundle:EndProduct')->findBy(
                ['user' => $userInfo[0]->getUser(), 'slackName' => $data[0]]
            );

            if (!count($endproduct)) {
                $array = [
                    'text' => 'Dit product bestaat niet!',
                ];

                return new JsonResponse($array);
            }

            $defaultOrder = $this->getDoctrine()->getRepository('KumaBroodjesBundle:DefaultOrder')->findBy(
                ['user' => $userInfo[0]->getUser(), 'day' => $data[1]]
            );

            $defaultOrder[0]->setEndProduct($endproduct[0]);

            $this->getDoctrine()->getManager()->persist($defaultOrder[0]);
            $this->getDoctrine()->getManager()->flush();

            setlocale(LC_TIME, 'nl_NL');

            $createDay = function ($dayNumber) {
                return mktime(null, null, null, null, $dayNumber);
            };
            $weekDays = [$createDay(0), $createDay(1), $createDay(2), $createDay(3), $createDay(4)];

            $array = [
                'text' => 'Hey ' . $userInfo[0]->getSlackName(
                    ) . ', je hebt de automatische bestelling voor ' . strftime('%A', $weekDays[$data[1] - 1]) . ' op ' . $endproduct[0]->getName(
                    ) . ' gezet!',
            ];
        } elseif ($data[1] > 5 && $data[1] < 7) {
            $array = [
                'text' => 'Tof, tof, werken in het weekend, maar Piot doet daar niet aan mee!',
            ];
        } else {
            $array = [
                'text' => 'Een werkweek telt maar 5 dagen, dus zijn de mogelijke dagen 1-5!',
            ];
        }

        return new JsonResponse($array);
    }

    /**
     * @Route("/default/show")
     */
    public function showDefaultAction(Request $request)
    {
        $slackUserId = $request->get('user_id');
        $token = $request->get('token');
        $requestToken = $this->getParameter('slack.api.request.show.default.token');

        if ($token !== $requestToken) {
            return new JsonResponse(null, 401, ['HTTP/1.0 401 Unauthorized']);
        }
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['slackId' => $slackUserId]
        );
        if (!count($userInfo)) {
            $array = [
                'text' => 'Hey, je bent nog niet aangemeld met slack op het broodjesplatform.',
            ];

            return new JsonResponse($array);
        }
        $defaultOrders = $this->getDoctrine()->getRepository('KumaBroodjesBundle:DefaultOrder')->findBy(
            ['user' => $userInfo[0]->getUser()], ['day' => 'ASC']
        );
        setlocale(LC_TIME, 'nl_NL');
        $createDay = function ($dayNumber) {
            return mktime(null, null, null, null, $dayNumber);
        };
        $weekDays = [$createDay(0), $createDay(1), $createDay(2), $createDay(3), $createDay(4)];

        $string = '';
        foreach ($defaultOrders as $defaultOrder) {
            if ($defaultOrder->getPause() == 0) {
                $toggle = 'gestart';
            } else {
                $toggle = 'gestopt';
            }
            if ($defaultOrder->getEndProduct() == null) {
                $string .= '*' . strftime('%A', $weekDays[$defaultOrder->getDay() - 1]) . '* : geen default, € 0,' . $toggle . " \n";
            } else {
                $string .= '*' . strftime('%A', $weekDays[$defaultOrder->getDay() - 1]) . '* : ' . $defaultOrder->getEndProduct()->getName(
                    ) . ', € ' . $defaultOrder->getEndProduct()->getPrice() . ', ' . $toggle . "\n";
            }
        }
        $array = [
            'text' => 'Hey ' . $userInfo[0]->getSlackName() . ', je huidig saldo bedraagt: € ' . $userInfo[0]->getCredits(),
            'attachments' => [
                [
                    'text' => $string,
                    'color' => 'good',
                    'mrkdwn_in' => [
                        'text',
                    ],
                ],
            ],
        ];

        return new JsonResponse($array);
    }

    /**
     * @Route("/payment/mollie")
     */
    public function addCreditAction(Request $request)
    {
        $slackUserId = $request->get('user_id');
        $token = $request->get('token');
        $requestToken = $this->getParameter('slack.api.request.payment.token');
        $amount = $request->get('text');
        $amount = str_replace(',', '.', $amount);

        if ($amount == '' || !is_numeric($amount)) {
            $array = [
                'text' => 'Hey, gelieve een nummer in te geven.',
            ];

            return new JsonResponse($array);
        }

        if ($token !== $requestToken) {
            return new JsonResponse(null, 401, ['HTTP/1.0 401 Unauthorized']);
        }
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['slackId' => $slackUserId]
        );

        if (!count($userInfo)) {
            $array = [
                'text' => 'Hey, je bent nog niet aangemeld met slack op het broodjesplatform.',
            ];

            return new JsonResponse($array);
        }

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
            $transaction->setUser($userInfo[0]->getUser());
            $transaction->setCreateDate($payment->createdDatetime);
            $transaction->setCredits($amount);
            $transaction->setPaidDate($payment->expiredDatetime);
            $transaction->setOrderId($orderId);
            $transaction->setMollieTransactionId($payment->id);
            $transaction->setStatus($payment->status);
            $transaction->setProfileId($payment->profileId);

            $this->getDoctrine()->getManager()->persist($transaction);
            $this->getDoctrine()->getManager()->flush();

            $array = [
                'text' => 'Hey ' . $userInfo[0]->getSlackName() . ', klik op deze link om ' . $amount . " Euro toe te voegen aan je saldo:\n" . $payment->getPaymentUrl(),
            ];
        } catch (\Mollie_API_Exception $e) {
            $array = [
                'text' => 'API call failed: ' . htmlspecialchars($e->getMessage()) .
                          ' on field ' . htmlspecialchars($e->getField()),
            ];
        }

        return new JsonResponse($array);
    }
}
