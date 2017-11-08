<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/users")
     */
    public function getUsersAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $users;
    }

    /**
     * @Rest\Get("/users/{id}", name="get_user")
     */
    public function getUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findBy(['id'=>$id]);
        return View::create($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/users")
     */
    public function createUserAction(Request $request)
    {
        $user = new User();

        $userForm = $this->createForm(UserType::class, $user);

        $data = json_decode($request->getContent(), true);

        $userForm->submit($data);

        if (!$userForm->isValid()) {
            return View::create($userForm, 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return View::create(null,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl(
                'get_user', array('id' => $user->getId()),
                true)
            ]
        );
    }
}