<?php

namespace App\Controllers\Admin;

/**
 * DEPRECATED - Use ProgramFormFieldsController instead
 * This is kept for backward compatibility
 */
class FormFieldsController extends BaseAdminController
{
    public function index()
    {
        return redirect()->to('/admin/program-forms/base');
    }
}
