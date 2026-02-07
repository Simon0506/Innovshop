<?php

namespace App\EventListener;

use App\Service\CartMerger;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginListener
{
    public function __construct(private CartMerger $cartMerger) {}

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $this->cartMerger->mergeSessionCartToUserCart($user);
    }
}