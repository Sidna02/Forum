<?php

namespace App\Controller\Admin\Stats;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class AdminStats
{
    private EntityManager $em;
    private TopicRepository $topicRepository;
    private CommentRepository $commentRepoisotry;
    private CategoryRepository $categoryRepository;
    private UserRepository $userRep;
    private ChartBuilderInterface $chartBuilder;

    public function __construct(
        ChartBuilderInterface  $chartBuilder,
        EntityManagerInterface $em,
        TopicRepository        $topicRepository,
        CategoryRepository     $categoryRepository,
        CommentRepository      $commentRepository,
        UserRepository         $userRep
    ) {
        $this->chartBuilder = $chartBuilder;
        $this->em = $em;
        $this->topicRepository = $topicRepository;
        $this->commentRepoisotry = $commentRepository;
        $this->categoryRepository = $categoryRepository;
        $this->userRep = $userRep;
    }

    public function getBirthStats(): Chart
    {
        $users = $this->userRep->countBirthDatesByYear();
        $labels = [];
        $count = [];
        foreach ($users as $user) {
            $labels[] = $user['y']; //identicators for the chart
            $count[] = $user['c']; //Data for the chart
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_PIE);
        $chart->setData([
            'labels' => $labels,

            'datasets' => [
                [
                    'label' => 'Registed Users by Birth Year',
                    'backgroundColor' => self::getRandomColors(count($labels)),
                    'data' => $count,
                ],
            ],
        ]);

        $chart->setOptions([]);
        return $chart;
    }

    public function getRegistrationsPerDays(): Chart
    {
        $results = $this->userRep->countRegistrationsByDay();
        $labels = [];
        $count = [];
        foreach ($results as $result) {
            $labels[] = $result['date'];
            $count[] = $result['c'];
        }
        $labels[] = "2022-05-01";
        $count[] = 0;
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);


        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Registrations per Date',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $count,
                ],
            ],
        ]);

        $chart->setOptions(
            [
                'scales' =>
                    [

                        'y' =>
                            [
                                'ticks' =>
                                    [
                                        'beginAtZero' => true,
                                        'precision' => 0,
                                    ]
                            ]
                    ]
            ]
        );
        dump($chart);
        return $chart;
    }

    public static function getRandomColors($n): array
    {
        $colors = [];
        for ($i = 0; $i < $n; $i++) {
            $colors[] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }


        return $colors;
    }
}
