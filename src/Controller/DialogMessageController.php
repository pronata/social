<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DialogMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class DialogMessageController extends AbstractController
{
    public function __construct(
        private readonly DialogMessageRepository $repository
    ) {
    }

    #[Route('/dialog/{userId}/send', name: 'dialog_send', methods: ['POST'])]
    public function send(
        Uuid $userId,
        Request $request,
        Security $security
    ): JsonResponse
    {
        $contentParams = json_decode($request->getContent(), true);

        $requiredParams = ['text'];

        if (!$contentParams || count(array_diff(array_keys($contentParams), $requiredParams)) > 0) {
            throw new BadRequestHttpException('Невалидные данные');
        }

        /** @var User $authUser*/
        $authUser = $security->getUser();

        $this->repository->addDialogMessage($authUser->getId(), $userId, $contentParams['text'] );

        $response = new JsonResponse('Успешно отправлено сообщение');
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $response;
    }

    #[Route('/dialog/{userId}/list', name: 'dialog_list', methods: ['GET'])]
    public function list(
        Uuid $userId,
        Security $security
    ): JsonResponse
    {
        /** @var User $authUser*/
        $authUser = $security->getUser();

        $dialogMessages = $this->repository->findDialog($authUser->getId(), $userId);

        $response = new JsonResponse($dialogMessages);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $response;
    }
}