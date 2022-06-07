<?php

namespace App\Controller;

use App\Repository\CacRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DataScraper;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(CacRepository $cacRepository): Response
    {
        // récupération des données par le scraper
        $data = DataScraper::getData();

        // récupération de lastDate en BDD
        $lastDate = $cacRepository->findLastDate();
        if ( !empty($lastDate)) {
            $lastDate = $lastDate[0]->getCreatedAt();
            $lastDate = $lastDate->format('d/m/Y');
        } else {
            $lastDate = null;
        }
        // tri des entrées postérieures à lastDate
        $newData = [];
        foreach ($data as $row) {
            if ( $lastDate !== $row[0]) {
                $newData[] = $row;
            }
            else {
                break;
            }
        }
        // vérification de l'heure du jour

        // inversion du tableau pour que les nouvelles entrées soient ordonnées chronologiquement
        $reverseData = array_reverse($newData);

        // enregistrement en BDD des données triées précédemment
        $cacRepository->saveData($reverseData);

        return $this->render('home/index.html.twig', [
            'data' => $data,
            'lastDate' => $lastDate,
        ]);
    }
}
