<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Classes\OffersProvider;
use App\Classes\OffersProviderException;

class IndexController extends AbstractController
{
    public static $oppPossibleValues = [5, 10, 15, 25, 50, 100];
    public static $filterDefaults = [
        'only_active' => false,
        'city' => '',
        'company_id' => 0,
        'days_since_add' => 0,
        'admin_name' => ''
    ];

    public function makeTwigExtension() {
        $staticFunc = new \Twig_SimpleFunction('static', function ($class, $property) {
            if (property_exists($class, $property)) {
                return $class::$$property;
            }
            return null;
        });

        $this->get('twig')->addFunction($staticFunc);
    }

    /**
     * @Route("/index", name="index")
     */

    public function index()
    {
        $this->makeTwigExtension();

        $session = new Session();
        if (!$session->has('page'))             $session->set('page', 0);
        if (!$session->has('opp'))              $session->set('opp', 5);
        foreach (self::$filterDefaults as $key => $value)
            if (!$session->has('f_' . $key))    $session->set('f_' . $key, $value);

        $filters = [
            'only_active' =>                    $session->get('f_only_active'),
            'city' =>                           $session->get('f_city'),
            'company_id' =>                     $session->get('f_company_id'),
            'days_since_add' =>                 $session->get('f_days_since_add'),
            'admin_name' =>                     $session->get('f_admin_name')
        ];

        $vars = [
            'offersProviderLoaded' =>           true,
            'opp' =>                            $session->get('opp'),
            'page' =>                           $session->get('page'),
            'startFrom' =>                      $session->get('page') * $session->get('opp')
        ];

        try {
            $vars['offersProvider'] = new OffersProvider($filters);
            $vars['maxPage'] = (int)($vars['offersProvider']->getOffersCount() / $session->get('opp'));
        } catch(OffersProviderException $e) {
            $vars['offersProviderLoaded'] = false;
        }

        $vars['filters'] = $filters;
        $vars['filterActive'] = $filters['only_active'] || !empty($filters['city']) || $filters['company_id'] > 0 || $filters['days_since_add'] > 0 || !empty( $filters['admin_name']);

        return $this->render('service/service.html.twig', $vars);
    }

    /**
     * @Route("/setpage/{page}")
     */

    public function setPage($page) {
        $page = (int)$page;

        if ($page < 0) $page = 0;
        (new Session())->set('page', $page);
        return $this->redirectToRoute('index', array(), 301);
    }

    /**
     * @Route("/setopp/{opp}")
     */

    public function setOffersPerPage($opp) {
        $opp = (int)$opp;

        if (!in_array($opp, self::$oppPossibleValues)) $opp = self::$oppPossibleValues[0];
        $session = new Session();
        $session->set('opp', $opp);
        $session->set('page', 0);
        return $this->redirectToRoute('index', array(), 301);
    }

    /**
     * @Route("/remove_filter/{filter}")
     */

    public function removeFilter($filter) {
        $session = new Session();
        if (isset(self::$filterDefaults[$filter])) $session->set('f_' . $filter, self::$filterDefaults[$filter]);
        $session->set('page', 0);
        return $this->redirectToRoute('index', array(), 301);
    }

    /**
     * @Route("/resetfilters")
     */

    public function resetFilters() {
        $session = new Session();
        $session->set('page', 0);
        foreach (self::$filterDefaults as $key => $value)
            $session->set('f_' . $key, $value);
        
        return $this->redirectToRoute('index', array(), 301);
    }

    /**
     * @Route("/save_filters", methods="POST")
     */

    public function saveFilters(Request $request) {
        $session = new Session();

        $session->set('f_only_active', (bool)$request->request->get('only_active'));
        $session->set('f_city', (string)$request->request->get('city'));
        $session->set('f_company_id', (int)$request->request->get('company_id'));
        $session->set('f_days_since_add', (int)$request->request->get('days_since_add'));
        $session->set('f_admin_name', (string)$request->request->get('admin_name'));

        return $this->redirectToRoute('index', array(), 301);
    }


    
}
