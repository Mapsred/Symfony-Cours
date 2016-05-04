<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $twigArray['quotes'] = $this->getDoctrine()->getRepository("AppBundle:Quote")->findAll();
        return $this->render("AppBundle:default:homepage.html.twig", $twigArray);
    }

    /**
     * @Route("/{type}-{nb}", name="type_quote", requirements={
     *  "nb": "\d+",
     *  "type": "top|flop"
     *  })
     * @param Request $request
     * @param $type
     * @param $nb
     * @return Response
     */
    public function orderAction(Request $request, $type, $nb)
    {
        $type = $type == "flop" ? "DESC" : "ASC";
    }

    /**
     * @Route("/quote/new", name="new_quote")
     * @return Response
     */
    public function createQuoteAction()
    {

    }
}
