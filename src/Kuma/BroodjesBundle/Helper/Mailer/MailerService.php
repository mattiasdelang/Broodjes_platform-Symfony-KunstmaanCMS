<?php

namespace Kuma\BroodjesBundle\Helper\Mailer;

use Doctrine\ORM\EntityManager;
use Kuma\BroodjesBundle\Entity\LunchOrder;
use Kuma\BroodjesBundle\Entity\Transaction;
use Kuma\BroodjesBundle\Entity\UserInfo;
use Kuma\BroodjesBundle\Helper\Slack\WebHookService;
use Symfony\Bundle\TwigBundle\TwigEngine;

class MailerService
{
    private $em;
    private $mailer;
    private $template;
    private $webhook;

    public function __construct(EntityManager $em, \Swift_Mailer $mailer, TwigEngine $template, WebHookService $webhook)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->template = $template;
        $this->webhook = $webhook;
    }

    public function defaultOrder()
    {
        $orders = $this->em->getRepository('KumaBroodjesBundle:LunchOrder')->findBy(['status' => 0]);
        $users = $this->em->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['defaultToggle' => 0]);

        $userArray = [];
        $orderArray = [];

        foreach ($users as $user) {
            $userArray[] = $user->getUser()->getId();
        }

        foreach ($orders as $order) {
            $orderArray[] = $order->getUser()->getId();
        }
        $diffs = array_diff($userArray, $orderArray);
        $today = date('N');

        foreach ($users as $user) {
            if (in_array($user->getUser()->getId(), $diffs)) {
                $default = $this->em->getRepository('KumaBroodjesBundle:DefaultOrder')->findBy(
                    ['day' => $today, 'user' => $user->getUser(), 'pause' => 0]
                );
                if (count($default)) {
                    if ($default[0]->getEndProduct() !== null && $default[0]->getEndProduct()->getPrice() <= $user->getCredits()) {
                        $newOrder = new LunchOrder();
                        $newOrder->addEndProduct($default[0]->getEndProduct());
                        $newOrder->setPrice($default[0]->getEndProduct()->getPrice());
                        $newOrder->setDate(new \DateTime());
                        $newOrder->setUser($default[0]->getEndProduct()->getUser());

                        $this->em->persist($newOrder);
                    }
                }
            }
        }
        $this->em->flush();
    }

    public function mail()
    {
        $orders = $this->em->getRepository('KumaBroodjesBundle:LunchOrder')->findBy(['status' => 0]);
        $today = date('d.m.y');

        /** @var LunchOrder $order */
        foreach ($orders as $order) {
            /** @var UserInfo $userInfo */
            $userInfo = $this->em->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
                ['user' => $order->getUser()]
            );

            $credits = $userInfo[0]->getCredits();
            $newBalance = $credits - $order->getPrice();
            $userInfo[0]->setCredits($newBalance);
            $this->em->persist($userInfo[0]);

            $date = date('Y-m-d, G:m:s');
            $transaction = new Transaction();
            $transaction->setUser($order->getUser());
            $transaction->setCredits('-' . $order->getPrice());
            $transaction->setMethod('Payment');
            $transaction->setCreateDate($date);

            $name = '';
            foreach ($order->getEndProducts() as $prod) {
                $name .= $prod->getName() . ', ';
            }
            $order->setProductnames($name);
            $this->em->persist($order);
            $this->em->persist($transaction);
        }

        try {
            $message = \Swift_Message::newInstance()
                ->setSubject('Bestelling Kunstmaan: ' . $today)
                ->setFrom('test@test.be')
                ->setTo('mattias.delang@kunstmaan.be')
                ->setBody(
                    $this->template->render(
                        '@KumaBroodjes/Mail/order_mail.html.twig',
                        ['today' => $today, 'orders' => $orders]
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if (!empty($orders)) {
            $this->em->getRepository('KumaBroodjesBundle:LunchOrder')->setStatus();
            $this->em->flush();
            $this->webhook->sendCurl('#test_bot', '<!channel> De bestelling is geplaatst!');
        } else {
            $this->webhook->sendCurl('#test_bot', '<!channel> Er zijn vandaag geen broodjes besteld.');
        }
    }
}
