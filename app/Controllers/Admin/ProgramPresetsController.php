<?php

namespace App\Controllers\Admin;

use App\Services\PresetService;
use App\Models\ProgramModel;
use App\Models\PresetModel;
use App\Models\ThemeModel;

class ProgramPresetsController extends BaseAdminController
{
    protected $presetService;
    protected $programModel;
    protected $presetModel;

    public function __construct()
    {
        $this->presetService = new PresetService();
        $this->programModel  = new ProgramModel();
        $this->presetModel   = new PresetModel();
    }

    public function index()
    {
        $programs  = $this->programModel->findAll();
        $presets   = $this->presetModel->getActivePresets();
        $presetMap = array_column($presets, null, 'id');

        return view('admin/presets/programs', [
            'programs'  => $programs,
            'presets'   => $presets,
            'presetMap' => $presetMap,
            'theme'     => (new ThemeModel())->getAllSettings() ?? [],
            'title'     => 'Program Assignments',
        ]);
    }

    public function assignPreset()
    {
        $programId = (int) $this->request->getPost('program_id');
        $presetId  = $this->request->getPost('preset_id');

        if (!$this->programModel->find($programId)) {
            session()->setFlashdata('error', 'Program not found.');
            return redirect()->to(base_url('admin/programs/presets'));
        }

        if ($presetId) {
            if (!$this->presetModel->find($presetId)) {
                session()->setFlashdata('error', 'Preset not found.');
                return redirect()->to(base_url('admin/programs/presets'));
            }
            $this->presetService->assignPresetToProgram($programId, $presetId);
        } else {
            $this->presetService->removePresetFromProgram($programId);
        }

        session()->setFlashdata('success', 'Preset assignment updated successfully!');
        return redirect()->to(base_url('admin/programs/presets'));
    }
}
