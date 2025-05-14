<?php

namespace App\Controller;

use App\Document\ConnectionLog;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestMongoController extends AbstractController
{
    #[Route('/mongo-test', name: 'mongo_test')]
    public function index(DocumentManager $dm): Response
    {
        try {
            $log = new ConnectionLog();
            $log->setUserId('test-user-id');
            $log->setUsername('debug@example.com');
            $log->setIp('127.0.0.1');
            $log->setSuccess(true);
            $log->setTimestamp(new \DateTime());

            $dm->persist($log);
            $dm->flush();

            return new Response('✅ Log Mongo enregistré avec succès.');
        } catch (\Throwable $e) {
            return new Response('❌ Erreur Mongo : ' . $e->getMessage());
        }
    }
}
