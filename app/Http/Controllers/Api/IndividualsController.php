<?php

namespace App\Http\Controllers\Api;

use App\Actions\Api\Individual\SearchIndividualsDocumentModeAction;
use App\Actions\Api\Individual\SearchIndividualsDocumentModeRequest;
use App\Actions\Api\Individual\SearchIndividualsPersonModeAction;
use App\Actions\Api\Individual\SearchIndividualsPersonModeRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchIndividualsPersonModeHttpRequest;
use App\Presenters\ApiPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class IndividualsController extends Controller
{
    private ApiPresenter $apiPresenter;
    private SearchIndividualsPersonModeAction $searchIndividualsPersonModeAction;
    private SearchIndividualsDocumentModeAction $searchIndividualsDocumentModeAction;

    public function __construct(
        SearchIndividualsPersonModeAction $searchIndividualsPersonModeAction,
        SearchIndividualsDocumentModeAction $searchIndividualsDocumentModeAction,
        ApiPresenter $apiPresenter
    ) {
        $this->searchIndividualsPersonModeAction = $searchIndividualsPersonModeAction;
        $this->searchIndividualsDocumentModeAction = $searchIndividualsDocumentModeAction;
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

    public function getDocumentsDocumentMode(Request $request): JsonResponse
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
}
