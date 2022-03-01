<?php

declare(strict_types=1);

namespace TicTacToe\Play\Infrastructure\Communication\Http\Symfony\Controller;

use DDDStarterPack\Aggregate\Domain\EntityId;
use DDDStarterPack\Service\Application\Response\BasicApplicationServiceResponse;
use DDDStarterPack\Service\Domain\Response\ServiceResponse;
use DDDStarterPack\Service\Domain\Service;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TicTacToe\Play\Application\Service\MakeTheMove;
use TicTacToe\Play\Application\Service\MakeTheMoveRequest;
use TicTacToe\Play\Application\Service\StartNewGame;

class PlayController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function startNewGame(StartNewGame $startNewGame): Response
    {
        return $this->executeService(
            service: $startNewGame,
            successfulStatus: 201,
            contextPath: ['route_name' => 'start_new_game'],
        );
    }

    public function makeTheMove(Request $request, MakeTheMove $makeTheMove): Response
    {
        $content = json_decode($request->getContent(), true);

        $request = MakeTheMoveRequest::create(
            playId: $content['play_id'],
            player: $content['player'],
            bordCellNumber: $content['position'],
        );

        return $this->executeService(
            service: $makeTheMove,
            request: $request,
        );
    }

    private function executeService(Service $service, mixed $request = null, int $successfulStatus = 200, array $contextPath = []): JsonResponse
    {
        try {
            /** @var BasicApplicationServiceResponse $serviceResponse */
            $serviceResponse = $service->execute($request);

            return $this->prepareResponse($serviceResponse, $successfulStatus, $contextPath);
        } catch (Exception $e) {
            return new JsonResponse(json_encode(['success' => false, 'message' => $e->getMessage()]), 500, [], true);
        }
    }

    /**
     * TODO - Refactoring: extract method - move method - split phase ecc.
     *
     * @param object   $serviceResponse
     * @param int      $successfulStatus
     * @param string[] $contextPath
     *
     * @return JsonResponse
     */
    private function prepareResponse(object $serviceResponse, int $successfulStatus, array $contextPath): JsonResponse
    {
        $body = null;
        $headers = [];

        if (!$serviceResponse->isSuccess()) {
            $status = 500;
            $body = [
                'success' => false,
                'message' => (string) $serviceResponse->body(),
            ];

            return new JsonResponse(json_encode($body), $status, $headers, true);
        }

        if ($serviceResponse instanceof ServiceResponse) {
            if ($serviceResponse->body() instanceof EntityId) {
                $body = ['id' => (string) $serviceResponse->body()];
                $headers = $this->prepareHeaders($contextPath, $body['id']);
            } else {
                $body = (array) $serviceResponse->body();
            }
        }

        return new JsonResponse(json_encode($body), $successfulStatus, $headers, true);
    }

    /**
     * @param string[] $contextPath
     * @param string   $entityId
     *
     * @return array
     */
    protected function prepareHeaders(array $contextPath, string $entityId): array
    {
        if (!isset($contextPath['route_name']) || empty($contextPath['route_name'])) {
            return [];
        }

        $url = $this->urlGenerator->generate($contextPath['route_name'], ['id' => $entityId], UrlGeneratorInterface::ABSOLUTE_PATH);

        return [
            'Location' => $url,
        ];
    }
}
