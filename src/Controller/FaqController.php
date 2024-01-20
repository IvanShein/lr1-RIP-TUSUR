<?php

namespace App\Controller;

use App\Entity\Faq;
use App\Repository\FaqRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/faq')]
class FaqController extends AbstractController
{
    #[Route('/', name: 'get_faq', methods: 'GET')]
    public function getAllFaqs(FaqRepository $repo): JsonResponse
    {
        $allFaqs = $repo->findAll();
    
        return $this->json([
            'message' => 'This is all FAQs.',
            'path' => $allFaqs,
        ]);
    }
    
    #[Route('/', name: 'create_faq', methods: 'POST')]
    public function createFaq(Request $request, FaqRepository $repo): JsonResponse
    {
        if(is_null($request->request->get(key: 'question', default: null))) return $this->json([
            'message' => 'No data. The field -question- is required.'
        ], status: 400);

        $faq = new Faq();
        $faq->setQuestion($request->request->get(key: 'question'));
        $faq->setAnswer($request->request->get(key: 'answer'));
    
        $repo->save($faq, true);
    
        return $this->json([
            'message' => 'Faq was created successfully',
            'path' => $faq,
        ]);
    }

    #[Route('/{id}', name: 'edit_faq', methods: 'PUT')]
    public function editFaq($id, Request $request, FaqRepository $repo): JsonResponse
    {
        $faq = $repo->find($id);
        if(!$faq instanceof Faq) return $this->json([
            'message' => 'Question with this ID have not be found.'
        ], status: 404);

        $data = json_decode($request->getContent(), associative:true);

        if(array_key_exists('question', $data) && $data['question'] !== $faq->getQuestion()) {
            $faq->setQuestion($data['question']);
        };
        if(array_key_exists('answer', $data) && $data['answer'] !== $faq->getAnswer()) {
            $faq->setAnswer($data['answer']);
        };
    
        $repo->save($faq, true);
    
        return $this->json([
            'message' => 'Faq was edited successfully',
            'path' => $faq,
        ]);
    }

    #[Route('/{id}', name: 'delete_faq', methods: 'delete')]
    public function deleteFaq($id, FaqRepository $repo): JsonResponse
    {
        $faq = $repo->find($id);
        if(!$faq instanceof Faq) return $this->json([
            'message' => 'Question with this ID have not be found.'
        ], status: 404);
    
        $repo->remove($faq, true);
    
        return $this->json([
            'message' => 'Faq was deleted successfully',
            'path' => $faq,
        ]);
    }
}


