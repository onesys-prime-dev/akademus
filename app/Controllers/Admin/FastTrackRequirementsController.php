<?php

namespace App\Controllers\Admin;

use App\Models\RequirementDocumentModel;
use App\Models\ThemeModel;
use App\Services\ProgramRequirementSyncService;

class FastTrackRequirementsController extends BaseAdminController
{
    protected $docModel;
    protected $themeModel;
    protected $syncService;

    public function __construct()
    {
        $this->docModel    = new RequirementDocumentModel();
        $this->themeModel  = new ThemeModel();
        $this->syncService = new ProgramRequirementSyncService();
    }

    public function index()
    {
        $documents = $this->docModel->getAllFastTrackFlaggableDocuments();

        // Group by category so the view can render one card per category.
        $byCategory = [];
        foreach ($documents as $doc) {
            $byCategory[$doc['category_id']]['category_name'] = $doc['category_name'];
            $byCategory[$doc['category_id']]['documents'][]   = $doc;
        }

        return view('admin/fast-track-requirements', [
            'theme'              => $this->themeModel->getAllSettings(),
            'title'              => 'Fast Track Requirements',
            'categories'         => $byCategory,
            'fast_track_enabled' => $this->themeModel->getSetting('fast_track_enabled', '1') === '1',
        ]);
    }

    public function save()
    {
        $checked = $this->request->getPost('is_fast_track_required') ?? [];
        $checked = array_map('intval', (array) $checked);

        foreach ($this->docModel->getAllFastTrackFlaggableDocuments() as $doc) {
            $requirementId = (int) $doc['requirement_id'];
            $shouldBeOn    = in_array($requirementId, $checked, true);

            if ((bool) $doc['is_fast_track_required'] === $shouldBeOn) {
                continue;
            }

            $this->docModel->update($requirementId, ['is_fast_track_required' => $shouldBeOn ? 1 : 0]);
            $this->syncService->syncRequirement($requirementId, (int) $doc['category_id']);
        }

        return redirect()->to('admin/fast-track-requirements')->with('success', 'Fast Track requirements saved.');
    }
}
