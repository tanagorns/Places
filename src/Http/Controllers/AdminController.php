<?php

namespace TypiCMS\Modules\Places\Http\Controllers;

use TypiCMS\Modules\Core\Http\Controllers\BaseAdminController;
use TypiCMS\Modules\Places\Http\Requests\FormRequest;
use TypiCMS\Modules\Places\Models\Place;
use TypiCMS\Modules\Places\Repositories\EloquentPlace;

class AdminController extends BaseAdminController
{
    public function __construct(EloquentPlace $place)
    {
        parent::__construct($place);
    }

    /**
     * List models.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $models = $this->repository->with('files')->findAll();
        app('JavaScript')->put('models', $models);

        return view('places::admin.index');
    }

    /**
     * Create form for a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $model = $this->repository->createModel();
        app('JavaScript')->put('model', $model);

        return view('places::admin.create')
            ->with(compact('model'));
    }

    /**
     * Edit form for the specified resource.
     *
     * @param \TypiCMS\Modules\Places\Models\Place $place
     *
     * @return \Illuminate\View\View
     */
    public function edit(Place $place)
    {
        app('JavaScript')->put('model', $place);

        return view('places::admin.edit')
            ->with(['model' => $place]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \TypiCMS\Modules\Places\Http\Requests\FormRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FormRequest $request)
    {
        $model = $this->repository->create($request->all());

        return $this->redirect($request, $model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \TypiCMS\Modules\Places\Models\Place              $place
     * @param \TypiCMS\Modules\Places\Http\Requests\FormRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Place $place, FormRequest $request)
    {
        $this->repository->update($request->id, $request->all());

        return $this->redirect($request, $place);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \TypiCMS\Modules\Places\Models\Place $place
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Place $place)
    {
        $deleted = $this->repository->delete($place);

        return response()->json([
            'error' => !$deleted,
        ]);
    }

    /**
     * List models.
     *
     * @return \Illuminate\View\View
     */
    public function files(Place $place)
    {
        $data = [
            'models' => $place->files,
        ];

        return response()->json($data, 200);
    }
}
