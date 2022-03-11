<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class UserFormAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private FlashBagInterface $flashBag,
        private RouterInterface $router,
        private Security $security,
        private SessionInterface $session
        ) {}

    public function supports(Request $request): ?bool
    {
        return ($request->getPathInfo() === '/login' && $request->isMethod('POST'));
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->get('_username');
        $password = $request->get('_password');

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($this->security->isGranted('ROLE_CUSTOMER')) {

            // User have validated a booking before login.
            if ($this->session->has('order') && $this->session->has('booking')) {

                $booking = $this->session->get('booking');
                $order = $this->session->get('order');

                // Reset restaurant from bookingController::bookTable
                $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $booking->getRestaurant()->getId()]);

                $booking->setUser($this->security->getUser());
                $booking->setBookingOrder($order);
                $restaurant->addBooking($booking);

                $this->entityManager->persist($booking);
                $this->entityManager->flush();

                $this->session->remove('booking');
                $this->session->remove('order');

                $this->flashBag->add('success', 'Votre commande a bien été enregistrée.');

                return new RedirectResponse($this->router->generate('show_customer_orders', ['id' => $this->security->getUser()->getId()]));

            // User have validated an order before login.
            } else if ($this->session->has('booking')) {                

                $booking = $this->session->get('booking');

                // Reset restaurant from bookingController::bookTable
                $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $booking->getRestaurant()->getId()]);
                $restaurant->addBooking($booking);
                $booking->setUser($this->security->getUser());

                $this->entityManager->persist($booking);
                $this->entityManager->flush();

                $this->session->remove(('booking'));

                $this->flashBag->add('success', 'Votre réservation a bien été prise en compte.');
                
                return new RedirectResponse($this->router->generate('show_customer_bookings'));
            }
        }

        /**
         * Redirect user to previous url before login.
         * Set target Url from session(SecurityController) where redirect user.
         */
        $targetUrl = $this->session->get('_security_referer_url');

        if ($targetUrl) {
            return new RedirectResponse($targetUrl);
        }

        return new RedirectResponse(
            $this->router->generate('home')
        );

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse('/login');        
    }    
}
