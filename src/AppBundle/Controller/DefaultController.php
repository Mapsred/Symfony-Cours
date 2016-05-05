<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Quote;
use AppBundle\Entity\Vote;
use AppBundle\Form\QuoteCreate;
use AppBundle\Utils\Helper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        $quoteRepo = $this->get("manager")->getQuoteRepo();

        $twigArray['limit'] = 5;
        $twigArray['title'] = "Accueil";
        $twigArray['quotes'] = $quoteRepo->findLimitLast($twigArray['limit']);

        return $this->render("AppBundle:default:homepage.html.twig", $twigArray);
    }

    /**
     * @Route("/{type}-{nb}", name="type_quote", requirements={
     *  "nb": "\d+",
     *  "type": "top|flop"
     *  }, defaults={"nb": 10})
     * @param $type
     * @param $nb
     * @return Response
     */
    public function orderAction($type, $nb)
    {
        $quoteRepo = $this->get("manager")->getQuoteRepo();

        $title = $type." ".$nb;
        $twigArray['limit'] = $title;
        $twigArray['title'] = $title;
        $twigArray['more'] = "Sont affichés uniquement les quotes ayant reçu des votes";
        $twigArray['quotes'] = $quoteRepo->findAllByVoteTypeAndLimit("flop" ? "no" : "yes", $nb);

        return $this->render("AppBundle:default:homepage.html.twig", $twigArray);
    }

    /**
     * @Route("/quote/new", name="new_quote")
     * @param Request $request
     * @return Response
     */
    public function createQuoteAction(Request $request)
    {
        $quote = new Quote();
        $form = $this->createForm(QuoteCreate::class, $quote);
        $form->handleRequest($request);
        $twigArray['form'] = $form->createView();

        if ($form->isValid()) {
            $quote->setDate(new \DateTime());
            if ($quote->getAuthor()) {
                $author = $this->get("manager")->getUserRepo()->findOneByUsername($quote->getAuthor());
                $manager = $this->get("manager");
                $author = isset($author) ? $author : $manager->createUser(Helper::getIp($request), $quote->getAuthor());
                $quote->setAuthor($author);
            }
            $this->get("manager")->persist($quote);
            $this->get("manager")->flush();
            $this->get('session')->getFlashBag()->add('success', 'Votre quote a bien été ajoutée!');

            return $this->redirectToRoute("homepage");
        }

        return $this->render("AppBundle:default:quote_new.html.twig", $twigArray);
    }

    /**
     * @Route("/ajax/vote", name="ajax_vote_quote")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxVoteAction(Request $request)
    {
        if (!$request->request->has("vote")) {
            return Helper::errorJsonResponse();
        }

        $manager = $this->get("manager");
        $type = $request->request->get("type");
        $id = $request->request->get("quote");
        $user = $manager->getUserRepo()->findOneByIp(Helper::getIp($request));
        $user = isset($user) ? $user : $manager->createUser(Helper::getIp($request));
        $quote = $manager->getQuoteRepo()->findOneById($id);
        $vote = $manager->getVoteRepo()->findOneByQuoteUser($quote, $user);

        if ($vote) {
            if ($vote->getType() == $type) {
                return new JsonResponse(
                    ["type" => "danger", "message" => "Vous avez déjà voté pour cette quote", "id" => $id]
                );
            }

            $vote->setType($type);
            $manager->persist($vote);
            $manager->flush();

            return new JsonResponse(
                ["type" => 'success', "message" => "Votre vote a bien été mis à jour", "id" => $id]
            );
        } else {
            $vote = new Vote();
            $vote->setUser($user);
            $vote->setDate(Helper::getDate());
            $vote->setQuote($quote);
            $vote->setType($type);

            $manager->persist($vote);
            $manager->flush();

            return new JsonResponse(["type" => 'success', "message" => "Votre vote a bien été ajouté", "id" => $id]);
        }
    }

    /**
     * @Route("/ajax/count", name="ajax_count_vote")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxCount(Request $request)
    {
        $ids = explode(",", $request->request->get("ids"));

        $manager = $this->get("manager");
        $results = [];
        foreach ($ids as $id) {
            $quote = $manager->getQuoteRepo()->findOneById($id);
            $results[$id] = [
                "no" => $manager->getVoteRepo()->countTypeForQuote($quote, "no"),
                "yes" => $manager->getVoteRepo()->countTypeForQuote($quote, "yes"),
            ];
        }

        return new JsonResponse($results);
    }


}
