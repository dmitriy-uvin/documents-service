<?php

namespace App\Http\Controllers\Api;

use App\Actions\Api\Individual\SearchIndividualsDocumentModeAction;
use App\Actions\Api\Individual\SearchIndividualsDocumentModeCollectionAction;
use App\Actions\Api\Individual\SearchIndividualsDocumentModeCollectionRequest;
use App\Actions\Api\Individual\SearchIndividualsDocumentModeRequest;
use App\Actions\Api\Individual\SearchIndividualsPersonModeAction;
use App\Actions\Api\Individual\SearchIndividualsPersonModeCollectionAction;
use App\Actions\Api\Individual\SearchIndividualsPersonModeCollectionRequest;
use App\Actions\Api\Individual\SearchIndividualsPersonModeRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchIndivDocumentModeCollectionHttpRequest;
use App\Http\Requests\SearchIndividualsDocumentHttpRequest;
use App\Http\Requests\SearchIndividualsPersonModeHttpRequest;
use App\Http\Requests\SearchIndivPersonModeCollectionHttpRequest;
use App\Presenters\ApiPresenter;
use Illuminate\Http\JsonResponse;

final class IndividualsController extends Controller
{
    private ApiPresenter $apiPresenter;
    private SearchIndividualsPersonModeAction $searchIndividualsPersonModeAction;
    private SearchIndividualsDocumentModeAction $searchIndividualsDocumentModeAction;
    private SearchIndividualsPersonModeCollectionAction $searchIndividualsPersonModeCollectionAction;
    private SearchIndividualsDocumentModeCollectionAction $searchIndividualsDocumentModeCollectionAction;

    public function __construct(
        SearchIndividualsPersonModeAction $searchIndividualsPersonModeAction,
        SearchIndividualsDocumentModeAction $searchIndividualsDocumentModeAction,
        SearchIndividualsPersonModeCollectionAction $searchIndividualsPersonModeCollectionAction,
        SearchIndividualsDocumentModeCollectionAction $searchIndividualsDocumentModeCollectionAction,
        ApiPresenter $apiPresenter
    ) {
        $this->searchIndividualsPersonModeAction = $searchIndividualsPersonModeAction;
        $this->searchIndividualsDocumentModeAction = $searchIndividualsDocumentModeAction;
        $this->searchIndividualsPersonModeCollectionAction = $searchIndividualsPersonModeCollectionAction;
        $this->searchIndividualsDocumentModeCollectionAction = $searchIndividualsDocumentModeCollectionAction;
        $this->apiPresenter = $apiPresenter;
    }

    public function getDocumentsPersonMode(SearchIndividualsPersonModeHttpRequest $request): JsonResponse
    {
        $response = $this->searchIndividualsPersonModeAction->execute(
            new SearchIndividualsPersonModeRequest(
                $request->query('name'),
                $request->query('surname'),
                $request->query('patronymic'),
                $request->query('date_birth'),
            )
        );

        return response()->json(
            $this->apiPresenter->presentCollection(
                $response->getIndividuals()
            )
        );
    }

    public function getDocumentsPersonModeCollection(
        SearchIndivPersonModeCollectionHttpRequest $request
    ): JsonResponse {
        $response = $this->searchIndividualsPersonModeCollectionAction->execute(
            new SearchIndividualsPersonModeCollectionRequest(
                $request->individuals
            )
        );

        return response()->json(
            $this->apiPresenter->presentCollection(
                $response->getIndividuals()
            )
        );
    }

    public function getDocumentsDocumentMode(SearchIndividualsDocumentHttpRequest $request): JsonResponse
    {
        $response = $this->searchIndividualsDocumentModeAction->execute(
            new SearchIndividualsDocumentModeRequest(
                $request->query('inn_number'),
                $request->query('snils_number'),
                $request->query('passport_number')
            )
        );

        return response()->json(
            $this->apiPresenter->presentCollection(
                $response->getIndividuals()
            )
        );
    }

    public function getDocumentsDocumentModeCollection(
        SearchIndivDocumentModeCollectionHttpRequest $request
    ): JsonResponse {
        $response = $this->searchIndividualsDocumentModeCollectionAction->execute(
            new SearchIndividualsDocumentModeCollectionRequest(
                $request->individuals
            )
        );

        return response()->json(
            $this->apiPresenter->presentCollection(
                $response->getIndividuals()
            )
        );
    }
}
