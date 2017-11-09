<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends FOSRestController
{
    /**
     * @ApiDoc
     * @Rest\Get("/users")
     * @return View
     */
    public function getUsersAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $view = $this->view($users);
        $context = new Context();
        $context->setGroups(["list"]);
        $view->setContext($context);

        return $view;
    }

    /**
     * @ApiDoc
     * @Rest\Get("/users/{id}", name="get_user")
     * @ParamConverter("user", class="AppBundle:User")
     * @param User $user
     * @return View
     */
    public function getUserAction(User $user)
    {
        $view = $this->view($user);
        $context = new Context();
        $context->setGroups(["details"]);
        $view->setContext($context);
        return $view;
    }

    /**
     * @ApiDoc
     * @Rest\Post("/users")
     * @param Request $request
     * @return View
     */
    public function createUserAction(Request $request)
    {
        $user = new User();

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->submit($request->request->all());

        if (!$userForm->isValid()) {
            return $this->view($userForm, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->view($user, Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('get_user', array('id' => $user->getId()), true)
            ]
        );
    }

    /**
     * @ApiDoc
     * @Rest\Put("/users/{id}")
     * @ParamConverter("user", class="AppBundle:User")
     * @param User $user
     * @param Request $request
     * @return View
     */
    public function updateUserAction(User $user, Request $request)
    {
        $userForm = $this->createForm(UserType::class, $user);

        $userForm->submit($request->request->all());

        if (!$userForm->isValid()) {
            return $this->view($userForm, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->view($user, Response::HTTP_OK,
            [
                'Location' => $this->generateUrl('get_user', array('id' => $user->getId()), true)
            ]
        );
    }
}