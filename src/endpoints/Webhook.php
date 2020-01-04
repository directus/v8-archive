<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\Schema\SchemaManager;
use Directus\Services\RevisionsService;
use Directus\Services\WebhookService;

class Webhook extends Route
{
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ACTIVE = 'active';
    /** @var DirectusUsersTableGateway $usersGateway */
    protected $usersGateway;

    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->post('', [$this, 'create']);
        $app->get('/{id}', [$this, 'read']);
        $app->patch('/{id}', [$this, 'update']);
        $app->delete('/{id}', [$this, 'delete']);

        // Revisions
        $app->get('/{id}/revisions', [$this, 'webhookRevisions']);
        $app->get('/{id}/revisions/{offset}', [$this, 'oneWebhookRevision']);
    }

    /**
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $service = new WebhookService($this->container);
        $responseData = $service->findAll(
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        $payload = $request->getParsedBody();
        if (isset($payload[0]) && \is_array($payload[0])) {
            return $this->batch($request, $response);
        }
        $service = new WebhookService($this->container);
        $responseData = $service->create(
            $payload,
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function read(Request $request, Response $response)
    {
        $service = new WebhookService($this->container);
        $responseData = $service->findByIds(
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function update(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        $service = new WebhookService($this->container);

        $payload = $request->getParsedBody();
        if (isset($payload[0]) && \is_array($payload[0])) {
            return $this->batch($request, $response);
        }

        $id = $request->getAttribute('id');

        if (false !== strpos($id, ',')) {
            return $this->batch($request, $response);
        }

        $responseData = $service->update(
            $id,
            $payload,
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function delete(Request $request, Response $response)
    {
        $service = new WebhookService($this->container);

        $id = $request->getAttribute('id');
        if (false !== strpos($id, ',')) {
            return $this->batch($request, $response);
        }

        $service->delete(
            $id,
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, []);
    }

    /**
     * @return Response
     */
    public function webhookRevisions(Request $request, Response $response)
    {
        $service = new RevisionsService($this->container);
        $responseData = $service->findAllByItem(
            SchemaManager::COLLECTION_WEBHOOKS,
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function oneWebhookRevision(Request $request, Response $response)
    {
        $service = new RevisionsService($this->container);
        $responseData = $service->findOneByItemOffset(
            SchemaManager::COLLECTION_WEBHOOKS,
            $request->getAttribute('id'),
            $request->getAttribute('offset'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @throws \Exception
     *
     * @return Response
     */
    protected function batch(Request $request, Response $response)
    {
        $service = new WebhookService($this->container);

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();

        $responseData = null;
        if ($request->isPost()) {
            $responseData = $service->batchCreate($payload, $params);
        } elseif ($request->isPatch()) {
            if ($request->getAttribute('id')) {
                $ids = explode(',', $request->getAttribute('id'));
                $responseData = $service->batchUpdateWithIds($ids, $payload, $params);
            } else {
                $responseData = $service->batchUpdate($payload, $params);
            }
        } elseif ($request->isDelete()) {
            $ids = explode(',', $request->getAttribute('id'));
            $service->batchDeleteWithIds($ids, $params);
        }

        return $this->responseWithData($request, $response, $responseData);
    }
}
