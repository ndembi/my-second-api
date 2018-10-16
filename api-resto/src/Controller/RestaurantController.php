<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/restaurant")
 */
class RestaurantController extends AbstractController
{
    /**
     * @Route("/", name="restaurant_index", methods="GET")
     */
    public function getRestaurantAction(RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = $restaurantRepository->findAll();
        $_restaurant = [];
        $_restaurants = [];

        //sérialisation manuelle
        foreach ($restaurants as $restaurant) {
            $_restaurant['name'] = $restaurant->getName();
            $_restaurant['adress'] = $restaurant->getAddress();
            $_restaurant['phone'] = $restaurant->getPhone();
            $_restaurant['id'] = $restaurant->getId();
            $_restaurants[] = $_restaurant;
        }
        return new JsonResponse($_restaurants,200);

    }

    /**
     * @Route("/", name="restaurant_new", methods="POST")
     */
    public function postRestaurantAction(Request $request, ValidatorInterface $validator): Response
    {
        $restaurant = new Restaurant();
        $body = $request->getContent();
        $data = json_decode($body,true);
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->submit($data);
        $errors = $validator->validate($restaurant);

    if (count($errors) > 0) {
        $errorsString = (string) $errors;
        return new JsonResponse($errorsString);
    }
            $em = $this->getDoctrine()->getManager();
            $em->persist($restaurant);
            $em->flush();
            return $this->redirectToRoute('restaurant_index');

    }

    /**
     * @Route("/{id}", name="restaurant_show", methods="GET")
     */
    public function showRestaurantAction(Restaurant $restaurant): Response
    {
            $_restaurant['name'] = $restaurant->getName();
            $_restaurant['adress'] = $restaurant->getAddress();
            $_restaurant['phone'] = $restaurant->getPhone();
            $_restaurant['id'] = $restaurant->getId();

        return new JsonResponse($_restaurant, 200);

    }

    /**
     * @Route("/{id}", name="restaurant_edit", methods="PUT")
     */
    public function putRestaurantAction(Request $request, Restaurant $restaurant,  ValidatorInterface $validator): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->submit($data);
        $errors = $validator->validate($restaurant);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('restaurant_index');

    }

    /**
     * @Route("/{id}", name="restaurant_delete", methods="DELETE")
     */
    public function deleteRestaurantAction(Request $request, Restaurant $restaurant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($restaurant);
            $em->flush();
        }

        return $this->redirectToRoute('restaurant_index');
    }
}
