<?php

namespace Polar\Action;

use Polar\Annotation\Route;
use Polar\Middleware\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class Authorize
 * @package Polar\Action
 * @Route(name="authorize", path="/authorize", methods={"GET"})
 */
class Authorize extends AbstractMiddleware
{


    /**
     * @param Request $request
     * @param Response $response
     * @param callable|null $out
     * @return RedirectResponse
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        /** @var AuthenticationServiceInterface $auth */
        $auth = $this->container->get(AuthenticationServiceInterface::class);
        if (array_key_exists('code', $request->getQueryParams())) {
            $auth->getAdapter()->setCredential($request->getQueryParams()['code']);
            $result = $auth->authenticate();
            if ($result->isValid()) {
                return new RedirectResponse('/admin');
            }
            return $this->render([
                'status' => 400,
                'reason' => 'auth',
                'error' => $result->getMessages()[0]
            ], "error::error");
        }
        return new RedirectResponse($auth->getAdapter()->getRedirectUri());
    }
}
